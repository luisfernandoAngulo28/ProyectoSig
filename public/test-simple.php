<?php
// Test muy simple - sin cargar Laravel
header('Content-Type: application/json');
http_response_code(200);

echo json_encode([
    'status' => true,
    'message' => 'PHP funcionando correctamente',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion()
]);
