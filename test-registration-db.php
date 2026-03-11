<?php
/**
 * Script de prueba independiente para los endpoints de registro con teléfono
 * Ejecutar desde la línea de comandos: php test-registration-db.php
 */

// Suprimir warnings deprecated
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

// Cargar Laravel Bootstrap
require __DIR__ .'/bootstrap/autoload.php';
$app = require_once __DIR__ .'/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n═══════════════════════════════════════════════════════════════════════════\n";
echo "  TEST DE REGISTRO CON TELÉFONO + OTP - AnDre Taxi Backend\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

// Variables globales para los tests
$testPhone = "987654" . rand(100, 999); // Generar teléfono aleatorio para testing
$testCode = null;
$testTempToken = null;
$testUserId = null;

// ============================================================================
// TEST 1: Enviar OTP
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST 1: Generar y Guardar Código OTP                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

try {
    echo "📱 Teléfono de prueba: {$testPhone}\n\n";
    
    // Verificar si el teléfono ya está registrado
    $userExists = App\User::where('cellphone', $testPhone)->first();
    if($userExists) {
        echo "❌ ERROR: Este número de teléfono ya está registrado.\n";
        echo "   ID Usuario: {$userExists->id}\n";
        echo "   Nombre: {$userExists->name}\n\n";
        exit(1);
    }
    
    echo "✓ Teléfono no registrado previamente\n";
    
    // Generar código OTP de 6 dígitos
    $testCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    echo "✓ Código OTP generado: {$testCode}\n";
    
    // Crear registro OTP
    $newOtp = new App\Otp;
    $newOtp->phone = $testPhone;
    $newOtp->code = $testCode;
    $newOtp->type = 'phone';
    $newOtp->time_expiration_code = time() + 600; // 10 minutos
    $newOtp->parent_id = 0; // Usuario aún no creado
    $newOtp->save();
    
    echo "✓ OTP guardado en base de datos\n";
    echo "  - ID: {$newOtp->id}\n";
    echo "  - Teléfono: {$newOtp->phone}\n";
    echo "  - Código: {$newOtp->code}\n";
    echo "  - Tipo: {$newOtp->type}\n";
    echo "  - Expira en: 10 minutos\n";
    echo "  - Timestamp expiración: " . date('Y-m-d H:i:s', $newOtp->time_expiration_code) . "\n\n";
    
    echo "✅ TEST 1 PASADO: OTP generado y guardado exitosamente\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR en Test 1: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n\n";
    exit(1);
}

// ============================================================================
// TEST 2: Verificar OTP
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST 2: Verificar Código OTP y Generar Token Temporal                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

try {
    echo "📱 Verificando código {$testCode} para teléfono {$testPhone}\n\n";
    
    // Buscar el código OTP más reciente para este teléfono
    $codeOtpFind = App\Otp::where('phone', $testPhone)
                            ->where('code', $testCode)
                            ->where('type', 'phone')
                            ->orderBy('created_at', 'desc')
                            ->first();

    if(!$codeOtpFind) {
        echo "❌ ERROR: El código no es válido.\n\n";
        exit(1);
    }
    
    echo "✓ Código OTP encontrado en BD\n";
    
    // Verificar si el código expiró
    $timeNow = time();
    if($timeNow > $codeOtpFind->time_expiration_code) {
        echo "❌ ERROR: El código ya expiró.\n";
        echo "   Hora actual: " . date('Y-m-d H:i:s', $timeNow) . "\n";
        echo "   Hora expiración: " . date('Y-m-d H:i:s', $codeOtpFind->time_expiration_code) . "\n\n";
        exit(1);
    }
    
    echo "✓ Código no ha expirado\n";
    
    // Código válido, generar token temporal
    $testTempToken = str_random(60);
    echo "✓ Token temporal generado: {$testTempToken}\n";
    
    // Actualizar el OTP con el token temporal
    $codeOtpFind->temp_token = $testTempToken;
    $codeOtpFind->save();
    
    echo "✓ Token guardado en registro OTP\n";
    echo "  - OTP ID: {$codeOtpFind->id}\n";
    echo "  - Token válido por: 30 minutos\n\n";
    
    echo "✅ TEST 2 PASADO: Código verificado y token temporal generado\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR en Test 2: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n\n";
    exit(1);
}

// ============================================================================
// TEST 3: Registrar Usuario
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST 3: Crear Usuario en Base de Datos                                  ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

try {
    $testName = "Usuario Prueba " . rand(1000, 9999);
    $testGender = "male";
    
    echo "👤 Datos del usuario:\n";
    echo "  - Nombre: {$testName}\n";
    echo "  - Teléfono: {$testPhone}\n";
    echo "  - Género: {$testGender}\n";
    echo "  - Token: {$testTempToken}\n\n";
    
    // Verificar el token temporal
    $otpRecord = App\Otp::where('temp_token', $testTempToken)
                          ->where('phone', $testPhone)
                          ->where('type', 'phone')
                          ->orderBy('created_at', 'desc')
                          ->first();
    
    if(!$otpRecord) {
        echo "❌ ERROR: Token inválido o no encontrado.\n\n";
        exit(1);
    }
    
    echo "✓ Token temporal válido\n";
    
    // Verificar que no pasaron más de 30 minutos desde la verificación
    $timeNow = time();
    $tokenCreatedTime = strtotime($otpRecord->updated_at);
    $timeElapsed = $timeNow - $tokenCreatedTime;
    if($timeElapsed > 1800) { // 30 minutos
        echo "❌ ERROR: Token expirado ({$timeElapsed} segundos transcurridos).\n\n";
        exit(1);
    }
    
    echo "✓ Token no ha expirado ({$timeElapsed} segundos transcurridos)\n";
    
    // Verificar nuevamente que el teléfono no esté registrado
    $userExists = App\User::where('cellphone', $testPhone)->first();
    if($userExists) {
        echo "❌ ERROR: Este número de teléfono ya está registrado.\n\n";
        exit(1);
    }
    
    echo "✓ Teléfono disponible para registro\n";
    
    // Crear el usuario
    $user = new App\User;
    $user->name = $testName;
    $user->cellphone = $testPhone;
    $user->email = $testPhone . '@andre.app';
    $user->password = bcrypt(str_random(16));
    $user->gender = $testGender;
    $user->type = 'customer';
    $user->active = 1;
    $user->verified = 1;
    $user->save();
    
    $testUserId = $user->id;
    
    echo "✓ Usuario creado en base de datos\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Nombre: {$user->name}\n";
    echo "  - Celular: {$user->cellphone}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Género: {$user->gender}\n";
    echo "  - Tipo: {$user->type}\n";
    echo "  - Activo: " . ($user->active ? 'Sí' : 'No') . "\n";
    echo "  - Verificado: " . ($user->verified ? 'Sí' : 'No') . "\n\n";
    
    // Actualizar el OTP con el user_id
    $otpRecord->parent_id = $user->id;
    $otpRecord->save();
    
    echo "✓ OTP actualizado con ID de usuario\n";
    echo "  - OTP ID: {$otpRecord->id}\n";
    echo "  - User ID: {$otpRecord->parent_id}\n\n";
    
    echo "✅ TEST 3 PASADO: Usuario creado y vinculado con OTP\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR en Test 3: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n\n";
    exit(1);
}

// ============================================================================
// RESUMEN FINAL
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║ RESUMEN DE PRUEBAS                                                       ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

echo "🎉 ¡TODOS LOS TESTS PASARON EXITOSAMENTE!\n\n";

echo "📊 Resultados:\n";
echo "  ✅ TEST 1: Generación de OTP\n";
echo "  ✅ TEST 2: Verificación de OTP y token temporal\n";
echo "  ✅ TEST 3: Creación de usuario\n\n";

echo "📝 Datos del test:\n";
echo "  - Teléfono: {$testPhone}\n";
echo "  - Código OTP: {$testCode}\n";
echo "  - Token temporal: {$testTempToken}\n";
echo "  - Usuario ID: {$testUserId}\n\n";

echo "✓ La lógica de registro con teléfono + OTP funciona correctamente\n";
echo "✓ La base de datos tiene todos los campos necesarios\n";
echo "✓ Los endpoints pueden implementarse sin problemas\n\n";

echo "🔄 Próximos pasos:\n";
echo "  1. ✅ Migración de base de datos ejecutada\n";
echo "  2. ✅ Lógica de negocio funcionando\n";
echo "  3. ⏳ Configurar Twilio para SMS real\n";
echo "  4. ⏳ Actualizar Flutter con las URLs del backend\n";
echo "  5. ⏳ Probar el flujo completo end-to-end\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n\n";

// Cleanup opcional: descomentar para eliminar el usuario de prueba
// echo "🗑️  Limpiando datos de prueba...\n";
// if($testUserId) {
//     App\User::destroy($testUserId);
//     echo "  ✓ Usuario {$testUserId} eliminado\n";
// }
// if($testPhone) {
//     App\Otp::where('phone', $testPhone)->delete();
//     echo "  ✓ OTPs del teléfono {$testPhone} eliminados\n\n";
// }
