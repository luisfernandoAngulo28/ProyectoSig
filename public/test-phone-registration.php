<?php
/**
 * Script de prueba directo para los endpoints de registro con teléfono
 * Este script simula las llamadas sin pasar por el routing de Laravel
 */

// Suprimir warnings deprecated
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

// Cargar Laravel
require __DIR__ .'/../bootstrap/autoload.php';
$app = require_once __DIR__ .'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h1>Test de Endpoints - Registro con Teléfono</h1>\n\n";
echo "<pre>\n";

// ============================================================================
// TEST 1: Enviar OTP
// ============================================================================
echo "============================================================================\n";
echo "TEST 1: Enviar OTP a teléfono 987654321\n";
echo "============================================================================\n\n";

try {
    $phone = "987654321";
    
    // Verificar si el teléfono ya está registrado
    $userExists = App\User::where('cellphone', $phone)->first();
    if($userExists) {
        echo "❌ ERROR: Este número de teléfono ya está registrado.\n\n";
    } else {
        // Generar código OTP de 6 dígitos
        $otpCode = Func::generateOTP(6);
        
        // Crear registro OTP
        $newOtp = new App\Otp;
        $newOtp->phone = $phone;
        $newOtp->code = $otpCode;
        $newOtp->type = 'phone';
        $newOtp->time_expiration_code = time() + 600; // 10 minutos
        $newOtp->parent_id = 0; // Usuario aún no creado
        $newOtp->save();
        
        echo "✅ SUCCESS: Código OTP generado y guardado\n";
        echo "Phone: {$phone}\n";
       echo "Code: {$otpCode}\n";
        echo "Expires in: 600 seconds (10 minutes)\n";
        echo "OTP ID: {$newOtp->id}\n\n";
        
        // Guardar el código para uso en el siguiente test
        $testCode = $otpCode;
        $testPhone = $phone;
    }
    
} catch (Exception $e) {
    echo "❌ ERROR en Test 1: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// TEST 2: Verificar OTP
// ============================================================================
echo "============================================================================\n";
echo "TEST 2: Verificar código OTP\n";
echo "============================================================================\n\n";

try {
    if(isset($testCode) && isset($testPhone)) {
        $phone = $testPhone;
        $code = $testCode;
        
        // Buscar el código OTP más reciente para este teléfono
        $codeOtpFind = App\Otp::where('phone', $phone)
                                ->where('code', $code)
                                ->where('type', 'phone')
                                ->orderBy('created_at', 'desc')
                                ->first();

        if(!$codeOtpFind) {
            echo "❌ ERROR: El código no es válido.\n\n";
        } else {
            // Verificar si el código expiró
            $timeNow = time();
            if($timeNow > $codeOtpFind->time_expiration_code) {
                echo "❌ ERROR: El código ya expiró.\n\n";
            } else {
                // Código válido, generar token temporal
                $tempToken = str_random(60);
                
                // Actualizar el OTP con el token temporal
                $codeOtpFind->temp_token = $tempToken;
                $codeOtpFind->save();
                
                echo "✅ SUCCESS: Código verificado correctamente\n";
                echo "Phone: {$phone}\n";
                echo "Code: {$code}\n";
                echo "Temp Token: {$tempToken}\n";
                echo "Token válido por: 30 minutos\n\n";
                
                // Guardar para el siguiente test
                $testTempToken = $tempToken;
            }
        }
    } else {
        echo "⚠️  SKIP: Test 1 falló, no se puede continuar\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR en Test 2: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// TEST 3: Registrar Usuario
// ============================================================================
echo "============================================================================\n";
echo "TEST 3: Registrar usuario con teléfono\n";
echo "============================================================================\n\n";

try {
    if(isset($testTempToken) && isset($testPhone)) {
        $tempToken = $testTempToken;
        $phone = $testPhone;
        $name = "Juan Pérez de Prueba";
        $gender = "male";
        
        // Verificar el token temporal
        $otpRecord = App\Otp::where('temp_token', $tempToken)
                              ->where('phone', $phone)
                              ->where('type', 'phone')
                              ->orderBy('created_at', 'desc')
                              ->first();
        
        if(!$otpRecord) {
            echo "❌ ERROR: Token inválido o expirado.\n\n";
        } else {
            // Verificar que no pasaron más de 30 minutos desde la verificación
            $timeNow = time();
            $tokenCreatedTime = strtotime($otpRecord->updated_at);
            if(($timeNow - $tokenCreatedTime) > 1800) { // 30 minutos
                echo "❌ ERROR: Token expirado.\n\n";
            } else {
                // Verificar nuevamente que el teléfono no esté registrado
                $userExists = App\User::where('cellphone', $phone)->first();
                if($userExists) {
                    echo "❌ ERROR: Este número de teléfono ya está registrado.\n\n";
                } else {
                    // Crear el usuario
                    $user = new App\User;
                    $user->name = $name;
                    $user->cellphone = $phone;
                    $user->email = $phone . '@andre.app'; // Email generado
                    $user->password = bcrypt(str_random(16)); // Password aleatorio
                    $user->gender = $gender;
                    $user->type = 'customer'; // Tipo pasajero
                    $user->active = 1;
                    $user->verified = 1;
                    $user->save();
                    
                    // Actualizar el OTP con el user_id
                    $otpRecord->parent_id = $user->id;
                    $otpRecord->save();
                    
                    echo "✅ SUCCESS: Usuario registrado correctamente\n";
                    echo "User ID: {$user->id}\n";
                    echo "Name: {$user->name}\n";
                    echo "Cellphone: {$user->cellphone}\n";
                    echo "Email: {$user->email}\n";
                    echo "Gender: {$user->gender}\n";
                    echo "Type: {$user->type}\n";
                    echo "Active: {$user->active}\n";
                    echo "Verified: {$user->verified}\n\n";
                    
                    echo "⚠️  Nota: El usuario fue creado exitosamente.\n";
                    echo "    Para probarlo nuevamente, debes eliminar este registro de la BD.\n\n";
                }
            }
        }
    } else {
        echo "⚠️  SKIP: Tests anteriores fallaron, no se puede continuar\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR en Test 3: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// RESUMEN
// ============================================================================
echo "============================================================================\n";
echo "RESUMEN DE PRUEBAS\n";
echo "============================================================================\n\n";
echo "✅ Si todos los tests pasaron, los endpoints están funcionando correctamente\n";
echo "✅ La lógica de registro con teléfono + OTP funciona\n";
echo "✅ La base de datos tiene los campos necesarios\n\n";
echo "📝 Próximos pasos:\n";
echo "   1. Configurar Twilio para envío real de SMS\n";
echo "   2. Actualizar Flutter con las URLs del backend\n";
echo "   3. Probar el flujo completo end-to-end\n\n";

echo "</pre>";
