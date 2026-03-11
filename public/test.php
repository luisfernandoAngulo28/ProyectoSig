<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'OK',
    'message' => 'PHP server working',
    'php_version' => PHP_VERSION
]);
