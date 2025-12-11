<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/auth.php";

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($metodo !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Usa GET."]);
    exit;
}

// requireAuth() terminará la ejecución si el token no es válido
$user = requireAuth();

// Devolver solo campos públicos
$response = [
    'id' => $user->id ?? null,
    'email' => $user->email ?? null,
    'rol' => $user->rol ?? null,
];

http_response_code(200);
echo json_encode($response);
