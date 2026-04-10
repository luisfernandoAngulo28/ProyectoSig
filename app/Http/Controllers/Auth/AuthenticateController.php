<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Helpers\SpecialFunc;
use App\Driver;
use App\User;
use App\Otp;

class AuthenticateController extends Controller {
  
    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');
        
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false, 'error' => 'Su usuario y contraseña no coinciden.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'error' => 'Hubo un error, vuelva a intentarlo.'], 500);
        }

        $user = auth()->user();
        if(!$user){
             return response()->json(['status' => false, 'error' => 'Usuario no encontrado tras autenticación.'], 500);
        }

        $date = date('d/n/Y H:i:s', strtotime('+6 months'));
        
        $roles = [];
        // Intento obtener roles de forma muy segura para evitar 500 si la tabla no existe
        try {
            if (isset($user->role_user) && ($user->role_user instanceof \Illuminate\Support\Collection || is_array($user->role_user))) {
                foreach($user->role_user as $role){
                    $roles[] = [
                        'id' => $role->id,
                        'name' => $role->name,
                        'description' => $role->description,
                    ];
                }
            }
        } catch (\Throwable $t) {
            // Ignoramos errores de relación por si falta la tabla 'roles' o 'role_user'
        }
        
        // Si no hay roles detectados, intentamos poner el de conductor por defecto si tiene registro de driver
        if(count($roles) == 0){
             $driver = \App\Driver::where('user_id', $user->id)->first();
             if($driver){
                $roles[] = [
                    'id' => 3, 
                    'name' => 'driver',
                    'description' => 'Conductor',
                ];
             }
        }

        $userData = [
            'token' => $token,
            'expirationDate' => $date,
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name ?? $user->name,
            'last_name' => $user->last_name ?? '',
            'email' => $user->email,
            'cellphone' => $user->cellphone,
            'code_cellphone' => $user->code_cellphone ?? '+591',
            'address' => $user->address ?? '',
            'is_verify' => ($user->verified == 1),
            'role' => $roles,
            'client_socket_code' => $user->client_socket_code ?? '',
        ];

        // Añadir campos de conductor si existen consultando directamente para evitar errores de relación
        $driver = \App\Driver::where('user_id', $user->id)->first();
        if($driver){
            // Verificar si el conductor está aprobado
            if ($driver->active != 1) {
                return response()->json([
                    'status' => false,
                    'error' => 'Tu cuenta está pendiente de aprobación. Espera la confirmación por WhatsApp.',
                ], 403);
            }
            $userData['drivers_id'] = $driver->id;
            $userData['drivers_license_number'] = $driver->license_number;
            $userData['drivers_car_with_grill'] = (int)$driver->car_with_grill;
            $userData['drivers_travel_with_pets'] = (int)$driver->travel_with_pets;
            $userData['drivers_image'] = $driver->image;
            $userData['qr_image'] = $driver->qr_image;
            $userData['drivers_number_of_passengers'] = $driver->number_of_passengers;
            $userData['driver_rating_total'] = '5.0'; 
        }

        return response()->json([
            'status' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => $userData
        ], 200);
    }


    public function sendCodeEmail(Request $request){
        $validator = \Validator::make($request->all(), [
            'email' => 'required',
        ],[
            'email.required' => 'El campo email es requerido.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => ['Debes enviar los parámetros'],
                'errors' => $validator->errors()->all(),
            ], 400);
        }

        try {
            $userFind = \App\User::where('email', $request->input('email'))->first();
            if(!$userFind)
                return  response()->json(['status'=>false, 'message'=>['No existe un usuario con este email en los registros.'], 'errors'=>['Email inválido']] , 400);   
            else{
                $otpCode = \Func::generateOTP(6);
                $newOtp = new \App\Otp;
                $newOtp->code = $otpCode;
                $newOtp->time_expiration_code = time() + 3600;
                $newOtp->parent_id = $userFind->id;
                $newOtp->save();
                \SpecialFunc::send_email( "Recuperar Contraseña", [ $request->input('email') ], 'Reestablecer contraseña.', 
                'Para restablecer su contraseña, por favor, ingrese el siguiente código en la aplicación móvil y siga las instrucciones proporcionadas. Tenga en cuenta que este código tendrá una validez de 60 minutos: Código:' .$otpCode  );

                return ['status'=>true, 'message'=>'El código se envió correctamente.', 'errors'=>[], 'data'=>[]];
            } 

        } catch (\Throwable $th) {
            \Log::error('Error en sendCodeEmail: ' . $th->getMessage());
            return  response()->json(['status'=>false, 'message'=>'Error en el servidor.', 'errors'=>['Error interno.']] , 500);   
        }
    }

    public function recoverPassword(Request $request){
        try {
            $validator = \Validator::make($request->all(), [
                'code' => 'required',
                'password' => 'required',
            ],[
                'code.required' => 'El campo code es requerido.',
                'password.required' => 'El campo password es requerido.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => ['Debes enviar los parámetros'],
                    'errors' => $validator->errors()->all(),
                ], 400);
            }
            // dd("HOLA>>");

            $codeOtpFind = \App\Otp::where('code', $request->input('code'))->first();

            if($codeOtpFind){
                $timeNow = time();
                if($timeNow > $codeOtpFind->time_expiration_code){
                    return response()->json(['status'=>false, 'message'=>['El código ya expiró vuelva a solicitarlo.'], 'errors'=>[ 'Código inválido.' ]] , 400);
                }else{
                    $password = $request->input('password');
                    $user = \App\User::where('id', $codeOtpFind->parent_id)->first();
                    \App\User::where('id', $user->id)->update(['password'=> bcrypt($password) ]);
                    return response()->json(['status'=>true, 'message'=>['La contraseña fue reestablecida correctamente. '], 'errors'=>[  ]] , 200);
                }
            }else{
                return response()->json(['status'=>false, 'message'=>['El código no existe vuelva a solicitarlo.'], 'errors'=>[ 'Código inválido.' ]] , 400);
            }
        } catch (\Throwable $th) {
            \Log::error('Error en recoverPassword: ' . $th->getMessage());
            return  response()->json(['status'=>false, 'message'=>['Error en el servidor.'], 'errors'=>['Error interno.']] , 500);
        }
    }

    
    public function validCode(Request $request){
        try {
            
            $validator = \Validator::make($request->all(), [
                'code' => 'required',
            ],[
                'code.required' => 'El campo code es requerido.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => ['Debes enviar los parámetros'],
                    'errors' => $validator->errors()->all(),
                ], 400);
            }
            
            $codeOtpFind = \App\Otp::where('code', $request->input('code'))->first();

            if($codeOtpFind){
                $timeNow = time();
                if($timeNow > $codeOtpFind->time_expiration_code){
                    return response()->json(['status'=>false, 'message'=>['El código ya expiró vuelva a solicitarlo.'], 'errors'=>[ 'Código inválido.' ]] , 400);
                }else{
                    return response()->json(['status'=>true, 'message'=>['Código válido.'], 'errors'=>[]] , 200);
                }
            }else{
                return response()->json(['status'=>false, 'message'=>['El código no existe vuelva a solicitarlo.'], 'errors'=>[ 'Código inválido.' ]] , 400);
            }


        } catch (\Throwable $th) {
            \Log::error('Error en validCode: ' . $th->getMessage());
            return  response()->json(['status'=>false, 'message'=>['Error en el servidor.'], 'errors'=>['Error interno.']] , 500);
        }
    }

    /**
     * Send OTP code to phone number (for new user registration)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtpPhone(Request $request){
        $validator = \Validator::make($request->all(), [
            'phone' => 'required|digits_between:8,9',
        ],[
            'phone.required' => 'El campo teléfono es requerido.',
            'phone.digits_between' => 'El teléfono debe tener 8 o 9 dígitos.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => ['Debes enviar los parámetros correctamente'],
                'errors' => $validator->errors()->all(),
            ], 400);
        }

        try {
            $phone = $request->input('phone');
            $exposeOtpCode = filter_var(env('OTP_EXPOSE_CODE', 'false'), FILTER_VALIDATE_BOOLEAN);
            
            // Verificar si el teléfono ya está registrado
            $userExists = \App\User::where('cellphone', $phone)->first();
            if($userExists) {
                return response()->json([
                    'status' => false, 
                    'message' => ['Este número de teléfono ya está registrado.'], 
                    'errors' => ['Teléfono ya existe']
                ], 400);
            }
            
            // Generar código OTP de 6 dígitos
            $otpCode = \Func::generateOTP(6);
            
            // Crear registro OTP directamente con DB para evitar el observer de Solunes
            // que consulta la tabla 'nodes' (que no existe en esta BD).
            \DB::table('otps')->insert([
                'phone'                => $phone,
                'code'                 => $otpCode,
                'type'                 => 'phone',
                'time_expiration_code' => time() + 600, // 10 minutos
                'parent_id'            => 0, // Usuario aún no creado
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
            
            // Enviar OTP por canal configurado (whatsapp por defecto)
            $otpMessage = "Tu código de verificación AnDre Taxi es: " . $otpCode . ". Válido por 10 minutos.";
            $delivery = $this->sendOtpMessage($phone, $otpMessage);

            if (!$delivery['sent'] && !$exposeOtpCode) {
                \Log::warning("OTP could not be delivered to: {$phone}, but OTP was saved.");
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo enviar el código de verificación en este momento. Intenta nuevamente.',
                    'errors' => ['No fue posible entregar el OTP por WhatsApp/SMS.'],
                    'data' => [
                        'expires_in' => 600,
                        'delivery_channel' => $delivery['channel'],
                        'message_sent' => false,
                    ]
                ], 503);
            }
            
            return response()->json([
                'status' => true, 
                'message' => 'Código OTP enviado correctamente.', 
                'errors' => [],
                'data' => [
                    'expires_in' => 600, // 10 minutos
                    'delivery_channel' => $delivery['channel'],
                    'message_sent' => $delivery['sent'],
                    'code' => $exposeOtpCode ? $otpCode : null,
                ]
            ], 200);
            
        } catch (\Throwable $th) {
            \Log::error('Error en sendOtpPhone: ' . $th->getMessage());
            return response()->json([
                'status' => false, 
                'message' => 'Error en el servidor.', 
                'errors' => ['Error interno.']
            ], 500);
        }
    }

    /**
     * Verify OTP code from phone
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtpPhone(Request $request){
        $validator = \Validator::make($request->all(), [
            'phone' => 'required|digits_between:8,9',
            'code' => 'required|digits:6',
        ],[
            'phone.required' => 'El campo teléfono es requerido.',
            'phone.digits_between' => 'El teléfono debe tener 8 o 9 dígitos.',
            'code.required' => 'El campo código es requerido.',
            'code.digits' => 'El código debe tener 6 dígitos.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => ['Debes enviar los parámetros correctamente'],
                'errors' => $validator->errors()->all(),
            ], 400);
        }

        try {
            $phone = $request->input('phone');
            $code = $request->input('code');
            
            // Buscar el código OTP más reciente para este teléfono
            $codeOtpFind = \App\Otp::where('phone', $phone)
                                    ->where('code', $code)
                                    ->where('type', 'phone')
                                    ->orderBy('created_at', 'desc')
                                    ->first();

            if(!$codeOtpFind) {
                return response()->json([
                    'status' => false, 
                    'message' => ['El código no es válido.'], 
                    'errors' => ['Código inválido.']
                ], 400);
            }
            
            // Verificar si el código expiró
            $timeNow = time();
            if($timeNow > $codeOtpFind->time_expiration_code) {
                return response()->json([
                    'status' => false, 
                    'message' => ['El código ya expiró. Solicita uno nuevo.'], 
                    'errors' => ['Código expirado.']
                ], 400);
            }
            
            // Código válido, generar token temporal
            $tempToken = str_random(60);
            
            // Actualizar el OTP con el token temporal
            $codeOtpFind->temp_token = $tempToken;
            $codeOtpFind->save();
            
            return response()->json([
                'status' => true, 
                'message' => ['Código verificado correctamente.'], 
                'errors' => [],
                'data' => [
                    'temp_token' => $tempToken,
                    'phone' => $phone
                ]
            ], 200);
            
        } catch (\Throwable $th) {
            \Log::error('Error en verifyOtpPhone: ' . $th->getMessage());
            return response()->json([
                'status' => false, 
                'message' => ['Error en el servidor.'], 
                'errors' => ['Error interno.']
            ], 500);
        }
    }

    /**
     * Register new user with phone number (after OTP verification)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerWithPhone(Request $request){
        $validator = \Validator::make($request->all(), [
            'temp_token' => 'required',
            'phone' => 'required|digits_between:8,9',
            'name' => 'required|min:2',
            'gender' => 'required|in:male,female',
            'photo' => 'nullable|string',
        ],[
            'temp_token.required' => 'Token temporal requerido.',
            'phone.required' => 'El campo teléfono es requerido.',
            'phone.digits_between' => 'El teléfono debe tener 8 o 9 dígitos.',
            'name.required' => 'El campo nombre es requerido.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'gender.required' => 'El campo género es requerido.',
            'gender.in' => 'El género debe ser male o female.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => ['Debes enviar los parámetros correctamente'],
                'errors' => $validator->errors()->all(),
            ], 400);
        }

        try {
            $tempToken = $request->input('temp_token');
            $phone = $request->input('phone');
            
            // Verificar el token temporal
            $otpRecord = \App\Otp::where('temp_token', $tempToken)
                                  ->where('phone', $phone)
                                  ->where('type', 'phone')
                                  ->orderBy('created_at', 'desc')
                                  ->first();
            
            if(!$otpRecord) {
                return response()->json([
                    'status' => false, 
                    'message' => ['Token inválido o expirado.'], 
                    'errors' => ['Token inválido.']
                ], 400);
            }
            
            // Verificar que no pasaron más de 30 minutos desde la verificación
            $timeNow = time();
            $tokenCreatedTime = strtotime($otpRecord->updated_at);
            if(($timeNow - $tokenCreatedTime) > 1800) { // 30 minutos
                return response()->json([
                    'status' => false, 
                    'message' => ['Token expirado. Por favor inicia el proceso nuevamente.'], 
                    'errors' => ['Token expirado.']
                ], 400);
            }
            
            // Verificar nuevamente que el teléfono no esté registrado
            $userExists = \App\User::where('cellphone', $phone)->first();
            if($userExists) {
                return response()->json([
                    'status' => false, 
                    'message' => ['Este número de teléfono ya está registrado.'], 
                    'errors' => ['Teléfono ya existe']
                ], 400);
            }
            
            // Crear el usuario
            $user = new \App\User;
            $fullName = trim((string) $request->input('name'));
            if (strlen($fullName) < 2) {
                $fallbackFirst = trim((string) $request->input('first_name'));
                $fallbackLast = trim((string) $request->input('last_name'));
                $fullName = trim($fallbackFirst . ' ' . $fallbackLast);
            }
            if (strlen($fullName) < 2) {
                $fullName = 'Usuario ' . $phone;
            }
            $nameParts = preg_split('/\s+/', $fullName, 2);
            $firstName = $nameParts[0] ?? $fullName;
            $lastName = $nameParts[1] ?? '';
            $genderValue = $request->input('gender');

            $user->name = $fullName;
            $user->cellphone = $phone;
            $user->email = $phone . '@andre.app'; // Email generado
            $user->password = bcrypt(str_random(16)); // Password aleatorio
            if ($this->hasTableColumnSafe('users', 'gender')) {
                $user->gender = $genderValue;
            }
            if ($this->hasTableColumnSafe('users', 'sex')) {
                $user->sex = $genderValue;
            }
            if ($this->hasTableColumnSafe('users', 'first_name')) {
                $user->first_name = $firstName;
            }
            if ($this->hasTableColumnSafe('users', 'last_name')) {
                $user->last_name = $lastName;
            }
            if ($this->hasTableColumnSafe('users', 'type')) {
                $user->type = 'customer'; // Tipo pasajero
            }
            if ($this->hasTableColumnSafe('users', 'active')) {
                $user->active = 1;
            }
            if ($this->hasTableColumnSafe('users', 'verified')) {
                $user->verified = 1;
            }
            if ($this->hasTableColumnSafe('users', 'is_verify')) {
                $user->is_verify = 1;
            }
            
            // Si hay foto
            if($request->has('photo')) {
                $user->image = $request->input('photo');
            }
            
            $user->save();

            // Forzar persistencia directa para esquemas con observers/mutaciones heredadas.
            $forceUpdate = [];
            if ($this->hasTableColumnSafe('users', 'name')) {
                $forceUpdate['name'] = $fullName;
            }
            if ($this->hasTableColumnSafe('users', 'first_name')) {
                $forceUpdate['first_name'] = $firstName;
            }
            if ($this->hasTableColumnSafe('users', 'last_name')) {
                $forceUpdate['last_name'] = $lastName;
            }
            if ($this->hasTableColumnSafe('users', 'gender')) {
                $forceUpdate['gender'] = $genderValue;
            }
            if ($this->hasTableColumnSafe('users', 'sex')) {
                $forceUpdate['sex'] = $genderValue;
            }
            if (!empty($forceUpdate)) {
                \DB::table('users')->where('id', $user->id)->update($forceUpdate);
            }

            $user = \App\User::find($user->id);
            
            // Actualizar el OTP con el user_id
            $otpRecord->parent_id = $user->id;
            $otpRecord->save();
            
            // Generar token JWT
            $token = JWTAuth::fromUser($user);
            
            return response()->json([
                'status' => true, 
                'message' => ['Usuario registrado correctamente.'], 
                'errors' => [],
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => trim((string) $user->name) !== '' ? $user->name : $fullName,
                        'cellphone' => $user->cellphone,
                        'email' => $user->email,
                        'gender' => $user->gender ?? $genderValue,
                        'image' => $user->image,
                    ],
                    'token' => $token,
                    'expirationDate' => date('d/n/Y H:i:s', strtotime('+6 months'))
                ]
            ], 201);
            
        } catch (\Throwable $th) {
            \Log::error('Error en registerWithPhone: ' . $th->getMessage());
            return response()->json([
                'status' => false, 
                'message' => ['Error en el servidor.'], 
                'errors' => ['Error interno.']
            ], 500);
        }
    }

    /**
     * Send SMS using Twilio (to be implemented)
     * 
     * @param string $phone
     * @param string $message
     * @return bool
     */
    private function sendSMS($phone, $message){
        try {
            // Obtener credenciales de Twilio desde .env
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilioNumber = env('TWILIO_PHONE_NUMBER');
            
            // Validar que las credenciales estén configuradas
            if (empty($sid) || empty($token) || empty($twilioNumber)) {
                \Log::error('Twilio credentials not configured in .env');
                return false;
            }
            
            // Crear cliente de Twilio
            $client = new \Twilio\Rest\Client($sid, $token);
            
            // Enviar SMS
            $countryCode = preg_replace('/\D+/', '', (string) env('SMS_COUNTRY_CODE', '591'));
            if (empty($countryCode)) {
                $countryCode = '591';
            }

            $normalizedPhone = $this->normalizePhoneDigits($phone);
            $normalizedPhone = ltrim($normalizedPhone, '0');

            $client->messages->create(
                '+' . $countryCode . $normalizedPhone,
                [
                    'from' => $twilioNumber,
                    'body' => $message
                ]
            );
            
            \Log::info("SMS sent successfully to: +{$countryCode}{$normalizedPhone}");
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Twilio SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    private function hasTableColumnSafe($table, $column)
    {
        try {
            $results = \DB::select("SHOW COLUMNS FROM `{$table}` LIKE ?", [$column]);
            return !empty($results);
        } catch (\Throwable $e) {
            \Log::warning("No se pudo verificar columna {$table}.{$column}: " . $e->getMessage());
            return false;
        }
    }

    private function normalizePhoneDigits($phone)
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ((strlen($digits) === 11 || strlen($digits) === 12) && strpos($digits, '591') === 0) {
            $digits = substr($digits, 3);
        }

        return $digits;
    }

    private function verifyFirebasePhoneToken($firebaseToken, $expectedPhone)
    {
        try {
            $apiKey = env('FIREBASE_WEB_API_KEY', '');
            if (empty($apiKey)) {
                return [
                    'valid' => false,
                    'message' => 'FIREBASE_WEB_API_KEY no configurada en el servidor.',
                ];
            }

            $response = \Http::post(
                'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . $apiKey,
                ['idToken' => $firebaseToken]
            );

            if (!$response->successful()) {
                \Log::warning('Firebase token inválido: ' . $response->body());
                return [
                    'valid' => false,
                    'message' => 'Token de Firebase inválido o expirado.',
                ];
            }

            $payload = $response->json();
            $firebasePhone = $payload['users'][0]['phoneNumber'] ?? null;

            if (!$firebasePhone) {
                return [
                    'valid' => false,
                    'message' => 'El token de Firebase no tiene teléfono asociado.',
                ];
            }

            $expectedDigits = $this->normalizePhoneDigits($expectedPhone);
            $firebaseDigits = $this->normalizePhoneDigits($firebasePhone);

            if ($expectedDigits !== $firebaseDigits) {
                return [
                    'valid' => false,
                    'message' => 'El teléfono verificado por Firebase no coincide.',
                ];
            }

            return [
                'valid' => true,
                'phone' => $expectedDigits,
            ];
        } catch (\Exception $e) {
            \Log::error('Error validando token Firebase: ' . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'No se pudo validar el token de Firebase.',
            ];
        }
    }

    private function sendWhatsAppMessage($phone, $message)
    {
        try {
            $provider = strtolower((string) env('WHATSAPP_PROVIDER', 'twilio')); // twilio|generic
            $apiUrl = env('WHATSAPP_API_URL');
            $apiToken = env('WHATSAPP_API_TOKEN');

            $countryCode = preg_replace('/\D+/', '', (string) env('SMS_COUNTRY_CODE', '591'));
            if (empty($countryCode)) {
                $countryCode = '591';
            }

            $normalizedPhone = ltrim($this->normalizePhoneDigits($phone), '0');
            $formattedPhone = $countryCode . $normalizedPhone;

            if ($provider === 'twilio') {
                $sid = env('TWILIO_SID');
                $token = env('TWILIO_AUTH_TOKEN');
                $fromWhatsapp = env('WHATSAPP_FROM', 'whatsapp:+14155238886');

                if (empty($sid) || empty($token)) {
                    \Log::warning('Twilio WhatsApp no configurado: faltan TWILIO_SID/TWILIO_AUTH_TOKEN.');
                    return false;
                }

                $twilioUrl = !empty($apiUrl)
                    ? $apiUrl
                    : 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json';

                $response = \Http::asForm()
                    ->withBasicAuth($sid, $token)
                    ->post($twilioUrl, [
                        'From' => $fromWhatsapp,
                        'To' => 'whatsapp:+' . $formattedPhone,
                        'Body' => $message,
                    ]);

                if (!$response->successful()) {
                    \Log::warning('Twilio WhatsApp OTP falló para ' . $formattedPhone . ': ' . $response->body());
                    return false;
                }

                \Log::info('Twilio WhatsApp OTP enviado a: ' . $formattedPhone);
                return true;
            }

            if (empty($apiUrl) || empty($apiToken)) {
                \Log::warning('WhatsApp API no configurada para envío OTP.');
                return false;
            }

            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                \Log::warning('WhatsApp OTP falló para ' . $formattedPhone . ': ' . $response->body());
                return false;
            }

            \Log::info('WhatsApp OTP enviado a: ' . $formattedPhone);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error enviando OTP por WhatsApp: ' . $e->getMessage());
            return false;
        }
    }

    private function sendOtpMessage($phone, $message)
    {
        $channel = strtolower((string) env('OTP_CHANNEL', 'auto')); // firebase|whatsapp|sms|auto
        $fallbackToSms = filter_var(env('OTP_FALLBACK_TO_SMS', 'true'), FILTER_VALIDATE_BOOLEAN);

        // Firebase maneja el envío de SMS directamente desde la app móvil.
        // El backend solo valida el token resultante. No necesita enviar nada.
        if ($channel === 'firebase') {
            return ['sent' => true, 'channel' => 'firebase'];
        }

        if ($channel === 'sms') {
            $smsSent = $this->sendSMS($phone, $message);
            return ['sent' => $smsSent, 'channel' => $smsSent ? 'sms' : 'none'];
        }

        $waSent = $this->sendWhatsAppMessage($phone, $message);
        if ($waSent) {
            return ['sent' => true, 'channel' => 'whatsapp'];
        }

        if (($channel === 'auto' || $channel === 'whatsapp') && $fallbackToSms) {
            $smsSent = $this->sendSMS($phone, $message);
            return ['sent' => $smsSent, 'channel' => $smsSent ? 'sms' : 'none'];
        }

        return ['sent' => false, 'channel' => 'none'];
    }

    // ==========================================
    // ENDPOINTS PARA REGISTRO DE CONDUCTOR
    // ==========================================
    
    /**
     * Enviar código OTP para conductor
     * POST /api-auth/send-otp-phone-driver
     */
    public function sendOtpPhoneDriver(Request $request)
    {
        $normalizedPhone = $this->normalizePhoneDigits($request->input('phone', ''));
        $request->merge(['phone' => $normalizedPhone]);

        $validator = \Validator::make($request->all(), [
            'phone' => 'required|digits_between:8,9',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Número de teléfono inválido',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $phone = $request->phone;
        
        // Verificar si el conductor ya existe
        $driverExists = \App\Driver::where('cellphone', $phone)->first();
        if ($driverExists) {
            return response()->json([
                'status' => false,
                'message' => 'Este número ya está registrado como conductor'
            ], 400);
        }
        
        // Generar código OTP de 6 dígitos
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expirationTime = time() + 600; // 10 minutos
        
        // Guardar OTP sin disparar hooks de modelo que dependen de tablas no presentes.
        \DB::table('otps')->insert([
            'phone' => $phone,
            'parent_id' => 0,
            'code' => $code,
            'type' => 'phone',
            'time_expiration_code' => $expirationTime,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Enviar OTP por canal configurado (whatsapp por defecto)
        $message = "Tu código de verificación AnDre Conductor es: {$code}";
        $delivery = $this->sendOtpMessage($phone, $message);
        
        // Log sin exponer el código OTP
        \Log::info("OTP generado para conductor: {$phone}");
        
        return response()->json([
            'status' => true,
            'message' => 'Código OTP enviado correctamente',
            'data' => [
                'expires_in' => 600,
                'delivery_channel' => $delivery['channel'],
                'message_sent' => $delivery['sent']
            ]
        ], 200);
    }
    
    /**
     * Verificar código OTP para conductor
     * POST /api-auth/verify-otp-phone-driver
     */
    public function verifyOtpPhoneDriver(Request $request)
    {
        $normalizedPhone = $this->normalizePhoneDigits($request->input('phone', ''));
        $request->merge(['phone' => $normalizedPhone]);

        $validator = \Validator::make($request->all(), [
            'phone' => 'required|digits_between:8,9',
            'code' => 'sometimes|digits:6',
            'firebase_token' => 'sometimes|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $phone = $request->phone;
        $code = $request->input('code');
        $firebaseToken = $request->input('firebase_token');

        if (empty($code) && empty($firebaseToken)) {
            return response()->json([
                'status' => false,
                'message' => 'Debes enviar code o firebase_token'
            ], 400);
        }

        if (!empty($firebaseToken)) {
            $firebaseCheck = $this->verifyFirebasePhoneToken($firebaseToken, $phone);
            if (!$firebaseCheck['valid']) {
                return response()->json([
                    'status' => false,
                    'message' => $firebaseCheck['message'] ?? 'Token Firebase inválido',
                ], 400);
            }

            // Generar token temporal para completar registro (válido por 30 minutos)
            $tempToken = str_random(60);

            \DB::table('otps')->insert([
                'phone' => $phone,
                'parent_id' => 0,
                'code' => 'firebase',
                'type' => 'phone',
                'time_expiration_code' => time() + 1800,
                'temp_token' => $tempToken,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Teléfono verificado con Firebase',
                'data' => [
                    'temp_token' => $tempToken,
                    'phone' => $phone,
                    'verified_by' => 'firebase',
                ]
            ], 200);
        }
        
        // Buscar OTP sin usar el modelo Eloquent para evitar hooks globales.
        $codeOtpFind = \DB::table('otps')
            ->where('phone', $phone)
            ->where('code', $code)
            ->where('type', 'phone')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$codeOtpFind) {
            return response()->json([
                'status' => false,
                'message' => 'Código incorrecto o no encontrado'
            ], 400);
        }
        
        // Verificar si el código ha expirado
        if (time() > $codeOtpFind->time_expiration_code) {
            return response()->json([
                'status' => false,
                'message' => 'El código ha expirado. Solicita uno nuevo.'
            ], 400);
        }
        
        // Generar token temporal para completar registro (válido por 30 minutos)
        $tempToken = str_random(60);
        \DB::table('otps')
            ->where('id', $codeOtpFind->id)
            ->update([
                'temp_token' => $tempToken,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        \Log::info("Código verificado para conductor {$phone}, token temporal: {$tempToken}");
        
        return response()->json([
            'status' => true,
            'message' => 'Código verificado correctamente',
            'data' => [
                'temp_token' => $tempToken,
                'phone' => $phone
            ]
        ], 200);
    }
    
    /**
     * Registrar conductor con teléfono
     * POST /api-auth/register-driver-with-phone
     */
    public function registerDriverWithPhone(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'temp_token' => 'required',
            'phone' => 'required|digits_between:8,9',
            'name' => 'required|min:2',
            'email' => 'email', // Opcional: solo valida formato si se envía
            'gender' => 'required|in:male,female',
            'company_type' => 'required|in:auto_economico,auto_confort,moto_taxi,delivery',
            'photo' => 'string', // Base64 opcional (sin 'nullable' por Laravel 5.2)
            'photo_brevete' => 'required|string', // Base64 obligatorio
            'photo_ci' => 'required|string', // Base64 obligatorio
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Datos incompletos o inválidos',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $tempToken = $request->temp_token;
        $phone = $request->phone;
        $name = $request->name;
        $gender = $request->gender;
        $companyType = $request->company_type;
        
        // Verificar el token temporal sin hooks del modelo.
        $codeOtp = \DB::table('otps')
            ->where('phone', $phone)
            ->where('temp_token', $tempToken)
            ->where('type', 'phone')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$codeOtp) {
            return response()->json([
                'status' => false,
                'message' => 'Token inválido o expirado'
            ], 400);
        }
        
        // Verificar que el token no tenga más de 30 minutos
        $tokenAge = time() - strtotime($codeOtp->updated_at);
        if ($tokenAge > 1800) { // 30 minutos
            return response()->json([
                'status' => false,
                'message' => 'El token ha expirado. Por favor, comienza el proceso nuevamente.'
            ], 400);
        }
        
        // Verificar que el conductor no exista
        $driverExists = \App\Driver::where('cellphone', $phone)->first();
        if ($driverExists) {
            return response()->json([
                'status' => false,
                'message' => 'Este número ya está registrado'
            ], 400);
        }
        
        // Separar nombre y apellido
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        
        // Mapear company_type a organization_id (ajustar según tu DB)
        $organizationMap = [
            'auto_economico' => 1,
            'auto_confort' => 2,
            'moto_taxi' => 3,
            'delivery' => 4,
        ];
        $organizationId = $organizationMap[$companyType] ?? 1;
        
        // Procesar imágenes base64
        $imagePath = null;
        if ($request->has('photo') && !empty($request->photo)) {
            $imagePath = $this->saveBase64Image($request->photo, 'drivers/profiles');
        }
        
        $brevetePath = $this->saveBase64Image($request->photo_brevete, 'drivers/brevetes');
        $ciPath = $this->saveBase64Image($request->photo_ci, 'drivers/ci');
        
        // Crear el conductor
        $driver = new \App\Driver;
        $driver->first_name = $firstName;
        $driver->last_name = $lastName;
        // Usar email proporcionado o generar uno automático
        $driver->email = $request->has('email') && !empty($request->email) 
            ? $request->email 
            : $phone . '@andre.app'; 
        $driver->cellphone = $phone;
        $driver->gender = $gender;
        $driver->city_id = 1; // Ciudad por defecto, ajustar según necesidad
        $driver->organization_id = $organizationId;
        $driver->image = $imagePath ?? 'default-driver.png';
        $driver->license_number = 'PENDING'; // Será completado después
        $driver->license_back_image = $brevetePath;
        $driver->license_front_image = $brevetePath; // Misma foto para ambos lados por ahora
        $driver->ci_back_image = $ciPath;
        $driver->ci_front_image = $ciPath; // Misma foto para ambos lados por ahora
        $driver->number_of_passengers = 4; // Por defecto
        $driver->active_trips = 0;
        $driver->car_with_grill = 0;
        $driver->travel_with_pets = 0;
        $driver->password = bcrypt(str_random(16)); // Contraseña aleatoria
        $driver->active = 0; // Pendiente de aprobación
        $driver->verified = 0; // Pendiente de verificación
        $driver->save();
        
        // Vincular el OTP al conductor sin usar el modelo Eloquent.
        \DB::table('otps')
            ->where('id', $codeOtp->id)
            ->update([
                'parent_id' => $driver->id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        \Log::info("Conductor registrado: ID {$driver->id}, Phone: {$phone}");
        
        return response()->json([
            'status' => true,
            'message' => 'Conductor registrado exitosamente',
            'data' => [
                'driver' => [
                    'id' => $driver->id,
                    'name' => $driver->first_name . ' ' . $driver->last_name,
                    'email' => $driver->email,
                    'cellphone' => $driver->cellphone,
                    'gender' => $driver->gender,
                    'company_type' => $companyType,
                    'active' => $driver->active,
                    'verified' => $driver->verified,
                ],
                'message' => 'Tu registro está pendiente de aprobación. Te notificaremos vía WhatsApp cuando sea aprobado.',
            ]
        ], 201);
    }
    
    /**
     * Helper: Guardar imagen base64
     */
    private function saveBase64Image($base64String, $folder = 'uploads')
    {
        try {
            // Eliminar prefijo si existe (data:image/png;base64,)
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
            } else {
                $type = 'png';
            }
            
            $base64String = str_replace(' ', '+', $base64String);
            $imageData = base64_decode($base64String);
            
            if ($imageData === false) {
                return null;
            }
            
            // Generar nombre único
            $fileName = uniqid() . '.' . $type;
            $filePath = $folder . '/' . $fileName;
            $fullPath = public_path($filePath);
            
            // Crear directorio si no existe
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Guardar archivo
            file_put_contents($fullPath, $imageData);
            
            return $filePath;
            
        } catch (\Exception $e) {
            \Log::error('Error guardando imagen base64: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * LOGIN CON TELÉFONO - PASAJERO
     * Paso 1: Enviar OTP al teléfono del pasajero que ya existe
     */
    public function loginWithPhone(Request $request)
    {
        try {
            $normalizedPhone = $this->normalizePhoneDigits($request->input('phone', ''));
            $request->merge(['phone' => $normalizedPhone]);
            $exposeOtpCode = filter_var(env('OTP_EXPOSE_CODE', 'false'), FILTER_VALIDATE_BOOLEAN);

            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits_between:8,9',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
            
            $phone = $request->phone;
            
            // Validar que el usuario EXISTA en la base de datos
            $user = User::where('cellphone', $phone)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'No existe una cuenta con ese número. Por favor regístrate primero.',
                ], 404);
            }
            
            // Generar código OTP de 6 dígitos
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Guardar en tabla otp
            $otp = new Otp();
            $otp->parent_id = $user->id;
            $otp->code = $code;
            $otp->type = 'phone';
            $otp->phone = $phone; // Ensure phone is set for easier lookup
            $otp->time_expiration_code = time() + 600; // 10 minutes
            $otp->save();
            
            // Enviar OTP por canal configurado (whatsapp por defecto)
            $message = "Tu código de acceso AnDre es: {$code}. Válido por 10 minutos.";
            $delivery = $this->sendOtpMessage($phone, $message);

            if (!$delivery['sent'] && !$exposeOtpCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo enviar el código de verificación en este momento. Intenta nuevamente.',
                    'delivery_channel' => $delivery['channel'],
                    'message_sent' => false,
                ], 503);
            }
            
            return response()->json([
                'status' => true,
                'message' => $delivery['channel'] === 'whatsapp' ? 'Código enviado a tu WhatsApp' : 'Código enviado a tu teléfono',
                'delivery_channel' => $delivery['channel'],
                'message_sent' => true,
                'code' => $exposeOtpCode ? $code : null,
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error en loginWithPhone: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al enviar código: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * LOGIN CON TELÉFONO - PASAJERO
     * Paso 2: Verificar OTP y devolver JWT
     */
    public function loginVerifyPhone(Request $request)
    {
        try {
            $normalizedPhone = $this->normalizePhoneDigits($request->input('phone', ''));
            $request->merge(['phone' => $normalizedPhone]);

            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits_between:8,9',
                'code' => 'required|string|size:6',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
            
            $phone = $request->phone;
            $code = $request->code;
            
            // Buscar usuario
            $user = User::where('cellphone', $phone)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }
            
            // Validar OTP
            $otp = Otp::where('parent_id', $user->id)
                      ->where('code', $code)
                      ->where('type', 'phone')
                      ->where('time_expiration_code', '>', time())
                      ->orderBy('created_at', 'desc')
                      ->first();
            
            if (!$otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Código inválido o expirado',
                ], 400);
            }
            
            // Eliminar OTP usado
            $otp->delete();
            
            // Generar JWT token
            $token = JWTAuth::fromUser($user);
            
            return response()->json([
                'status' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->cellphone,
                        'gender' => $user->gender,
                        'photo' => $user->image ? url($user->image) : null,
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error en loginVerifyPhone: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al verificar código: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * LOGIN CON TELÉFONO - CONDUCTOR
     * Paso 1: Enviar OTP al teléfono del conductor que ya existe
     */
    public function loginWithPhoneDriver(Request $request)
    {
        try {
            $normalizedPhone = $this->normalizePhoneDigits($request->input('phone', ''));
            $request->merge(['phone' => $normalizedPhone]);

            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|min:8|max:9',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
            
            $phone = $request->phone;
            
            // Validar que el conductor EXISTA en la base de datos
            $driver = \App\Driver::where('cellphone', $phone)->first();
            
            if (!$driver) {
                return response()->json([
                    'status' => false,
                    'message' => 'No existe una cuenta de conductor con ese número. Por favor regístrate primero.',
                ], 404);
            }
            
            // Validar que el conductor esté ACTIVO (aprobado)
            if ($driver->active != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tu cuenta está pendiente de aprobación. Espera la confirmación por WhatsApp.',
                ], 403);
            }
            
            // Generar código OTP de 6 dígitos
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Guardar OTP — compatible con tabla otps con o sin columnas phone/type
            $otpInsert = [
                'parent_id' => $driver->id,
                'code'      => $code,
                'time_expiration_code' => time() + 600,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if (\Schema::hasColumn('otps', 'phone')) {
                $otpInsert['phone'] = $phone;
            }
            if (\Schema::hasColumn('otps', 'type')) {
                $otpInsert['type'] = 'phone';
            }
            \DB::table('otps')->insert($otpInsert);
            
            // Enviar OTP por canal configurado (whatsapp por defecto)
            $message = "Tu código de acceso AnDre Conductor es: {$code}. Válido por 10 minutos.";
            $delivery = $this->sendOtpMessage($phone, $message);
            
            // Incluir código en respuesta si Twilio no está configurado (modo desarrollo)
            $responseData = [
                'status' => true,
                'message' => $delivery['channel'] === 'whatsapp' ? 'Código enviado a tu WhatsApp' : 'Código enviado a tu teléfono',
                'delivery_channel' => $delivery['channel'],
            ];
            
            if ($delivery['channel'] === 'none' || $delivery['channel'] === 'log') {
                $responseData['code'] = $code;
            }
            
            return response()->json($responseData, 200);
            
        } catch (\Exception $e) {
            \Log::error('Error en loginWithPhoneDriver: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al enviar código: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * LOGIN CON TELÉFONO - CONDUCTOR  
     * Paso 2: Verificar OTP y devolver JWT
     */
    public function loginVerifyPhoneDriver(Request $request)
    {
        try {
            $normalizedPhone = $this->normalizePhoneDigits($request->input('phone', ''));
            $request->merge(['phone' => $normalizedPhone]);

            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|min:8|max:9',
                'code' => 'sometimes|string|size:6',
                'firebase_token' => 'sometimes|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
            
            $phone = $request->phone;
            $code = $request->input('code');
            $firebaseToken = $request->input('firebase_token');

            if (empty($code) && empty($firebaseToken)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Debes enviar code o firebase_token',
                ], 400);
            }
            
            // Buscar conductor
            $driver = \App\Driver::where('cellphone', $phone)->first();
            
            if (!$driver) {
                return response()->json([
                    'status' => false,
                    'message' => 'Conductor no encontrado',
                ], 404);
            }
            
            // Validar que esté activo
            if ($driver->active != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tu cuenta está pendiente de aprobación',
                ], 403);
            }
            
            if (!empty($firebaseToken)) {
                $firebaseCheck = $this->verifyFirebasePhoneToken($firebaseToken, $phone);
                if (!$firebaseCheck['valid']) {
                    return response()->json([
                        'status' => false,
                        'message' => $firebaseCheck['message'] ?? 'Token Firebase inválido',
                    ], 400);
                }
            } else {
                // Validar OTP — busca por parent_id + code sin depender de columna 'type'
                // (compatible con tabla otps con o sin migración AddPhoneToOtpsTable)
                $otp = \DB::table('otps')
                    ->where('parent_id', $driver->id)
                    ->where('code', $code)
                    ->where('time_expiration_code', '>', time())
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$otp) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Código inválido o expirado',
                    ], 400);
                }
                
                // Eliminar OTP usado
                \DB::table('otps')->where('id', $otp->id)->delete();
            }
            
            // Verificar estado de foto facial (actualización mensual)
            $facialPhotoStatus = $this->checkFacialPhotoStatus($driver);
            
            // Generar JWT token
            $token = JWTAuth::fromUser($driver);
            
            return response()->json([
                'status' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'token' => $token,
                    'driver' => [
                        'id' => $driver->id,
                        'first_name' => $driver->first_name,
                        'last_name' => $driver->last_name,
                        'cellphone' => $driver->cellphone,
                        'organization_id' => $driver->organization_id,
                        'active' => $driver->active,
                        'image' => $driver->image ? url($driver->image) : null,
                        'gender' => $driver->gender ?? 'male',
                    ],
                    'facial_photo_status' => $facialPhotoStatus, // INFO DE ACTUALIZACIÓN MENSUAL
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error en loginVerifyPhoneDriver: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al verificar código: ' . $e->getMessage(),
            ], 500);
        }
    }

    // REGISTRO DE VEHÍCULO - CONDUCTOR

    /**
     * Obtener lista de marcas de vehículos activas
     */
    public function getVehicleBrands(Request $request)
    {
        try {
            $brands = \DB::table('vehicle_brands')
                ->where('active', 1)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            return response()->json([
                'status'  => true,
                'message' => 'Marcas obtenidas exitosamente',
                'data'    => $brands,          // array plano, consistente con el resto
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error en getVehicleBrands: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Error al obtener marcas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener lista de modelos según marca
     */
    public function getVehicleModels(Request $request)
    {
        try {
            $brand_id = $request->input('brand_id');
            
            if (!$brand_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'El ID de la marca es requerido',
                ], 400);
            }

            // Usamos DB::table para evitar el error de count() en Eloquent Builder con PHP 7.4
            $models = \DB::table('vehicle_models')->where('vehicle_brand_id', $brand_id)
                ->where('active', 1)
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'vehicle_brand_id']);

            return response()->json([
                'status' => true,
                'message' => 'Modelos obtenidos exitosamente',
                'data' => [
                    'models' => $models
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error en getVehicleModels: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener modelos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar vehículo de conductor
     */
    public function registerDriverVehicle(Request $request)
    {
        try {
            // Validaciones (Laravel 5.2 compatible - sin 'nullable')
            $validator = \Validator::make($request->all(), [
                'driver_id' => 'required|exists:drivers,id',
                'type_vehicle' => 'required|in:auto,moto,torito',
                'vehicle_brand_id' => 'required|exists:vehicle_brands,id',
                'vehicle_model_id' => 'required|exists:vehicle_models,id',
                'number_plate' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'model_year' => 'digits:4', // Opcional en Laravel 5.2 (no required = opcional)
                'vehicle_image' => 'image|mimes:jpeg,jpg,png|max:5120', // 5MB max, opcional
                'side_image' => 'image|mimes:jpeg,jpg,png|max:5120', // Opcional
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 400);
            }

            $driver = \App\Driver::find($request->driver_id);
            if (!$driver) {
                return response()->json([
                    'status' => false,
                    'message' => 'Conductor no encontrado',
                ], 404);
            }

            // Verificar si ya tiene un vehículo activo (Usando DB::table para compatibilidad)
            $existingVehicle = \DB::table('driver_vehicles')->where('parent_id', $driver->id)
                ->where('active', 1)
                ->first();

            if ($existingVehicle) {
                return response()->json([
                    'status' => false,
                    'message' => 'El conductor ya tiene un vehículo registrado',
                    'data' => [
                        'vehicle_id' => $existingVehicle->id
                    ]
                ], 400);
            }

            // Crear vehículo
            $vehicle = new \App\DriverVehicle();
            $vehicle->parent_id = $driver->id;
            $vehicle->city_id = $driver->city_id ?? 1; // Ciudad por defecto
            $vehicle->type_vehicle = $request->type_vehicle;
            $vehicle->vehicle_brand_id = $request->vehicle_brand_id;
            $vehicle->vehicle_model_id = $request->vehicle_model_id;
            $vehicle->number_plate = strtoupper($request->number_plate);
            $vehicle->color = $request->color;
            $vehicle->model_year = $request->model_year;
            $vehicle->active = 1;

            // Procesar imagen del vehículo
            if ($request->hasFile('vehicle_image')) {
                $image = $request->file('vehicle_image');
                $imageName = 'vehicle_' . $driver->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/vehicles'), $imageName);
                $vehicle->vehicle_image = 'images/vehicles/' . $imageName;
            }

            // Procesar imagen lateral
            if ($request->hasFile('side_image')) {
                $sideImage = $request->file('side_image');
                $sideImageName = 'side_' . $driver->id . '_' . time() . '.' . $sideImage->getClientOriginalExtension();
                $sideImage->move(public_path('images/vehicles'), $sideImageName);
                $vehicle->side_image = 'images/vehicles/' . $sideImageName;
            }

            $vehicle->save();

            // Cargar relaciones
            $vehicle->load(['vehicle_brand', 'vehicle_model']);

            return response()->json([
                'status' => true,
                'message' => 'Vehículo registrado exitosamente',
                'data' => [
                    'vehicle' => [
                        'id' => $vehicle->id,
                        'type_vehicle' => $vehicle->type_vehicle,
                        'number_plate' => $vehicle->number_plate,
                        'color' => $vehicle->color,
                        'model_year' => $vehicle->model_year,
                        'vehicle_image' => $vehicle->vehicle_image,
                        'side_image' => $vehicle->side_image,
                        'brand' => $vehicle->vehicle_brand ? $vehicle->vehicle_brand->name : null,
                        'model' => $vehicle->vehicle_model ? $vehicle->vehicle_model->name : null,
                        'active' => $vehicle->active,
                    ]
                ]
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('Error en registerDriverVehicle: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar vehículo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * APROBAR CONDUCTOR
     * Admin aprueba conductor y envía notificación WhatsApp
     */
    public function approveDriver(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'driver_id' => 'required|exists:drivers,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }
            
            $driver = \App\Driver::find($request->driver_id);
            
            if (!$driver) {
                return response()->json([
                    'status' => false,
                    'message' => 'Conductor no encontrado'
                ], 404);
            }
            
            // Verificar si ya está aprobado
            if ($driver->approved == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'El conductor ya fue aprobado anteriormente'
                ], 400);
            }
            
            // Aprobar conductor
            $driver->approved = 1;
            $driver->active = 1; // Activar conductor
            $driver->approved_at = now();
            // 30 días gratis desde hoy
            $driver->free_trial_until = date('Y-m-d', strtotime('+30 days'));
            $driver->save();
            
            \Log::info("Conductor aprobado: ID {$driver->id}, Celular: {$driver->cellphone}");
            
            // Enviar notificación WhatsApp
            $this->sendWhatsAppApprovalNotification($driver);
            
            return response()->json([
                'status' => true,
                'message' => 'Conductor aprobado exitosamente',
                'data' => [
                    'driver' => [
                        'id' => $driver->id,
                        'name' => $driver->first_name . ' ' . $driver->last_name,
                        'cellphone' => $driver->cellphone,
                        'approved' => $driver->approved,
                        'approved_at' => $driver->approved_at,
                        'free_trial_until' => $driver->free_trial_until,
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error en approveDriver: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al aprobar conductor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enviar notificación WhatsApp al conductor aprobado
     */
    private function sendWhatsAppApprovalNotification($driver)
    {
        try {
            $phone = $driver->cellphone;
            
            // Formatear número (formato internacional)
            // Bolivia: +591 + número (sin 0 al inicio)
            $formattedPhone = '591' . ltrim($phone, '0');
            
            $message = "¡Felicidades! 🎉\n\n";
            $message .= "Tu registro como conductor en AnDre ha sido *aprobado*.\n\n";
            $message .= "✅ Ya puedes comenzar a recibir viajes\n";
            $message .= "🎁 Tienes *30 días GRATIS* para usar la aplicación\n";
            $message .= "📅 Vencimiento: " . date('d/m/Y', strtotime($driver->free_trial_until)) . "\n\n";
            $message .= "Descarga la app y comienza a ganar dinero hoy mismo.\n\n";
            $message .= "¡Bienvenido a AnDre! 🚗💚";
            
            // Configuración WhatsApp API
            $apiUrl = env('WHATSAPP_API_URL', 'https://api.whatsapp.com/send');
            $apiToken = env('WHATSAPP_API_TOKEN', '');
            $apiPhone = env('WHATSAPP_API_PHONE', ''); // Número de WhatsApp Business
            
            // Si está configurado, usar API de WhatsApp Business
            if (!empty($apiToken) && !empty($apiUrl)) {
                $response = \Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, [
                    'phone' => $formattedPhone,
                    'message' => $message,
                ]);
                
                \Log::info("WhatsApp enviado a {$formattedPhone}: " . $response->body());
            } else {
                // Log si no está configurado
                \Log::warning("WhatsApp API no configurada. Mensaje no enviado a {$formattedPhone}");
                \Log::info("Mensaje que se enviaría:\n{$message}");
            }
            
        } catch (\Exception $e) {
            \Log::error('Error enviando WhatsApp: ' . $e->getMessage());
            // No lanzar excepción para no bloquear la aprobación
        }
    }
    
    /**
     * ACTUALIZACIÓN MENSUAL DE FOTO FACIAL
     * POST /api-auth/update-facial-photo
     */
    public function updateFacialPhoto(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'photo' => 'required|string', // Base64 de la nueva foto
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $driverId = $request->driver_id;
        $driver = \App\Driver::find($driverId);
        
        if (!$driver) {
            return response()->json([
                'status' => false,
                'message' => 'Conductor no encontrado'
            ], 404);
        }
        
        try {
            // Guardar la nueva foto
            $photoPath = $this->saveBase64Image($request->photo, 'drivers/facial_photos');
            
            if (!$photoPath) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error al procesar la imagen'
                ], 400);
            }
            
            // Actualizar foto y fecha
            $driver->image = $photoPath;
            $driver->facial_photo_updated_at = now();
            $driver->facial_photo_blocked = 0; // Desbloquear si estaba bloqueado
            $driver->save();
            
            \Log::info("Foto facial actualizada: Conductor ID {$driver->id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Foto actualizada exitosamente',
                'data' => [
                    'driver' => [
                        'id' => $driver->id,
                        'image' => url($driver->image),
                        'facial_photo_updated_at' => $driver->facial_photo_updated_at,
                        'facial_photo_blocked' => $driver->facial_photo_blocked,
                        'next_update_date' => date('Y-m-d', strtotime($driver->facial_photo_updated_at . ' +30 days')),
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error al actualizar foto facial: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar foto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar estado de actualización de foto facial
     * Retorna: needs_update (bool), days_remaining (int), blocked (bool)
     */
    private function checkFacialPhotoStatus($driver)
    {
        // ✅ Verificar si las columnas existen antes de usarlas
        $hasFacialColumns = \Schema::hasColumn('drivers', 'facial_photo_updated_at')
                         && \Schema::hasColumn('drivers', 'facial_photo_blocked');

        // Si la tabla no tiene estas columnas aún (migración pendiente), retornar estado neutral
        if (!$hasFacialColumns) {
            return [
                'needs_update'     => false,
                'is_expired'       => false,
                'days_remaining'   => 30,
                'blocked'          => false,
                'next_update_date' => null,
                'message'          => ''
            ];
        }

        $lastUpdate = $driver->facial_photo_updated_at;
        $isBlocked  = $driver->facial_photo_blocked == 1;
        
        // Si no tiene fecha de actualización, considerar la fecha de registro
        if (!$lastUpdate && $driver->created_at) {
            $lastUpdate = $driver->created_at;
        }
        
        // Si aún no tiene fecha, es nueva cuenta sin foto
        if (!$lastUpdate) {
            return [
                'needs_update'     => true,
                'is_expired'       => true,
                'days_remaining'   => 0,
                'blocked'          => false,
                'next_update_date' => null,
                'message'          => 'Debes subir tu foto de perfil'
            ];
        }
        
        // Calcular días desde la última actualización
        $lastUpdateDate   = new \DateTime($lastUpdate);
        $now              = new \DateTime();
        $daysSinceUpdate  = $now->diff($lastUpdateDate)->days;
        
        $daysRemaining  = 30 - $daysSinceUpdate;
        $needsUpdate    = $daysSinceUpdate >= 25;
        $isExpired      = $daysSinceUpdate >= 30;
        $nextUpdateDate = date('Y-m-d', strtotime($lastUpdate . ' +30 days'));
        
        // Auto-bloquear si ha pasado más de 30 días
        if ($isExpired && !$isBlocked) {
            \DB::table('drivers')->where('id', $driver->id)->update([
                'facial_photo_blocked' => 1,
            ]);
            $isBlocked = true;
            $this->sendFacialPhotoBlockedNotification($driver);
        }
        
        // Enviar recordatorio si está a 5 días o menos
        if ($needsUpdate && !$isExpired && $daysSinceUpdate == 25) {
            $this->sendFacialPhotoReminderNotification($driver, $daysRemaining);
        }
        
        $message = '';
        if ($isBlocked) {
            $message = 'Tu cuenta está bloqueada. Actualiza tu foto para continuar.';
        } elseif ($isExpired) {
            $message = 'Tu foto de perfil ha vencido. Actualízala para continuar.';
        } elseif ($needsUpdate) {
            $message = "Tu foto vence en {$daysRemaining} días. No olvides actualizarla.";
        } else {
            $message = 'Tu foto está actualizada.';
        }
        
        return [
            'needs_update' => $needsUpdate,
            'is_expired' => $isExpired,
            'days_remaining' => max(0, $daysRemaining),
            'blocked' => $isBlocked,
            'next_update_date' => $nextUpdateDate,
            'last_update_date' => $lastUpdate,
            'message' => $message
        ];
    }
    
    /**
     * Enviar notificación de recordatorio (5 días antes)
     */
    private function sendFacialPhotoReminderNotification($driver, $daysRemaining)
    {
        try {
            $phone = $driver->cellphone ?? $driver->phone;
            if (!$phone) {
                \Log::warning("Conductor {$driver->id} no tiene teléfono registrado");
                return;
            }
            
            // Formatear teléfono para Bolivia (+591)
            $formattedPhone = '591' . ltrim($phone, '0');
            
            $message = "📸 *Recordatorio AnDre* 📸\n\n";
            $message .= "Hola {$driver->first_name},\n\n";
            $message .= "Te recordamos que tu *foto de perfil* vence en *{$daysRemaining} días*.\n\n";
            $message .= "⚠️ Si no la actualizas, tu cuenta será bloqueada temporalmente.\n\n";
            $message .= "📱 Abre la app y actualiza tu foto ahora.\n\n";
            $message .= "¡Gracias por ser parte de AnDre! 🚗💚";
            
            // Verificar si WhatsApp API está configurado
            $apiUrl = env('WHATSAPP_API_URL');
            $apiToken = env('WHATSAPP_API_TOKEN');
            
            if (!$apiUrl || !$apiToken) {
                \Log::info("WhatsApp API no configurada. Mensaje de recordatorio: {$message}");
                return;
            }
            
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);
            
            if ($response->successful()) {
                \Log::info("Recordatorio de foto enviado a {$formattedPhone}");
            } else {
                \Log::error("Error al enviar recordatorio de foto: " . $response->body());
            }
            
        } catch (\Exception $e) {
            \Log::error('Error al enviar recordatorio de foto por WhatsApp: ' . $e->getMessage());
        }
    }
    
    /**
     * Enviar notificación de bloqueo por foto vencida
     */
    private function sendFacialPhotoBlockedNotification($driver)
    {
        try {
            $phone = $driver->cellphone ?? $driver->phone;
            if (!$phone) {
                return;
            }
            
            $formattedPhone = '591' . ltrim($phone, '0');
            
            $message = "🚫 *Cuenta Bloqueada - AnDre* 🚫\n\n";
            $message .= "Hola {$driver->first_name},\n\n";
            $message .= "Tu cuenta ha sido *bloqueada temporalmente* porque tu foto de perfil ha vencido.\n\n";
            $message .= "📸 Para desbloquear tu cuenta:\n";
            $message .= "1. Abre la app AnDre Conductor\n";
            $message .= "2. Actualiza tu foto de perfil\n";
            $message .= "3. Continúa recibiendo viajes\n\n";
            $message .= "⚠️ Este proceso es obligatorio cada 30 días por seguridad.\n\n";
            $message .= "📞 ¿Problemas? Contáctanos.";
            
            $apiUrl = env('WHATSAPP_API_URL');
            $apiToken = env('WHATSAPP_API_TOKEN');
            
            if (!$apiUrl || !$apiToken) {
                \Log::info("WhatsApp API no configurada. Mensaje de bloqueo: {$message}");
                return;
            }
            
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);
            
            if ($response->successful()) {
                \Log::info("Notificación de bloqueo enviada a {$formattedPhone}");
            }
            
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación de bloqueo por WhatsApp: ' . $e->getMessage());
        }
    }

    // ==========================================
    // CATÁLOGOS PÚBLICOS (para dropdowns de registro)
    // ==========================================

    /**
     * GET /api-auth/regions
     * Retorna todos los departamentos/regiones activos.
     */
    public function getRegions(Request $request)
    {
        try {
            $regions = \DB::table('regions as r')
                ->join('region_translation as rt', function ($j) {
                    $j->on('rt.region_id', '=', 'r.id')
                      ->where('rt.locale', '=', 'es');
                })
                ->where('r.active', 1)
                ->orderBy('r.order', 'asc')
                ->get(['r.id', 'r.code', 'rt.name']);

            return response()->json([
                'status' => true,
                'data'   => $regions,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('getRegions: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error al obtener departamentos.'], 500);
        }
    }

    /**
     * GET /api-auth/cities?region_id=X
     * Retorna municipios de un departamento.
     */
    public function getCitiesByRegion(Request $request)
    {
        try {
            $regionId = (int) $request->input('region_id', 0);

            $query = \DB::table('cities as c')
                ->join('city_translations as ct', function ($j) {
                    $j->on('ct.city_id', '=', 'c.id')
                      ->where('ct.locale', '=', 'es');
                })
                ->where('c.active', 1)
                ->orderBy('ct.name', 'asc')
                ->select(['c.id', 'c.region_id', 'ct.name']);

            if ($regionId > 0) {
                $query->where('c.region_id', $regionId);
            }

            $cities = $query->get();

            return response()->json([
                'status' => true,
                'data'   => $cities,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('getCitiesByRegion: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error al obtener municipios.'], 500);
        }
    }

    /**
     * GET /api-auth/organizations?city_id=X
     * Retorna organizaciones/federaciones de una ciudad.
     */
    public function getOrganizationsByCity(Request $request)
    {
        try {
            $cityId = (int) $request->input('city_id', 0);

            $query = \DB::table('organizations')
                ->where('active', 1)
                ->orderBy('name', 'asc')
                ->select(['id', 'name', 'city_id', 'type']);

            if ($cityId > 0) {
                $query->where('city_id', $cityId);
            }

            $organizations = $query->get();

            return response()->json([
                'status' => true,
                'data'   => $organizations,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('getOrganizationsByCity: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error al obtener organizaciones.'], 500);
        }
    }

}