<?php
header("Content-Type: application/json; charset=utf-8");

// Carga automáticamente todas las clases de las librerías
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config.php";

use Firebase\JWT\JWT;

// 1. Aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Método no permitido. Usa POST."]);
    exit;
}

// 2. Leer JSON de entrada
$input = json_decode(file_get_contents("php://input"), true) ?? [];

$email    = $input["email"]    ?? "";
$password = $input["password"] ?? "";

// 3. Simulación de usuarios (HARDCODED para pruebas)
// Definimos dos usuarios válidos para probar tus permisos
$adminEmail = "admin@bm.com";
$userEmail  = "user@bm.com";
$passGeneral = "1234"; // La misma contraseña para facilitar las pruebas

// Validamos credenciales
if ($password !== $passGeneral) {
    http_response_code(401); 
    echo json_encode(["error" => "Contraseña incorrecta"]);
    exit;
}

// Determinamos el rol según el email ingresado
$rol = "";
$userId = 0;

if ($email === $adminEmail) {
    $rol = "administrador";
    $userId = 1;
} elseif ($email === $userEmail) {
    $rol = "usuario";
    $userId = 2;
} else {
    // Si el email no es ninguno de los dos permitidos
    http_response_code(401);
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

// 4. Crear el Payload (Datos dentro del token)
$payload = [
    "id"    => $userId,      // ID simulado
    "email" => $email,
    "rol"   => $rol,         // <--- AQUÍ está la clave para tu auth.php
    "iat"   => time(),       // Fecha de emisión
    "exp"   => time() + 3600 // Caduca en 1 hora
];

// 5. Generar token firmado
try {
    $token = JWT::encode($payload, JWT_SECRET, 'HS256');

    // 6. Devolver token
    http_response_code(200);
    echo json_encode([
        "mensaje" => "Login exitoso",
        "rol" => $rol, // Devolvemos el rol para que veas visualmente cuál te tocó
        "token" => $token
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al generar el token"]);
}