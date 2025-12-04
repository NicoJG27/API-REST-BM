<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config.php";
require __DIR__ . "/../database.php"; 

use Firebase\JWT\JWT;

// 1. Aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Usa POST."]);
    exit;
}

// 2. Obtener datos y LIMPIAR espacios (trim)
$input = json_decode(file_get_contents("php://input"), true) ?? [];
$email    = trim($input["email"] ?? "");
$password = trim($input["password"] ?? "");

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos (email o password)"]);
    exit;
}

try {
    // 3. Conexión a la Base de Datos
    $db = Database::getConnection();

    // 4. Buscar usuario
    $sql = "SELECT id_usuario, nombre, email, password, rol FROM Usuarios WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Verificar credenciales (Hash)
    if (!$usuario || !password_verify($password, $usuario['password'])) {
        http_response_code(401);
        echo json_encode(["error" => "Credenciales incorrectas"]);
        exit;
    }

    // 6. Generar Token
    // Nota: Usamos los roles exactos de tu BD ('admin' / 'user')
    $payload = [
        "id"    => $usuario['id_usuario'],
        "email" => $usuario['email'],
        "rol"   => $usuario['rol'], 
        "iat"   => time(),
        "exp"   => time() + 3600 // 1 hora
    ];

    $token = JWT::encode($payload, JWT_SECRET, 'HS256');

    http_response_code(200);
    echo json_encode([
        "mensaje" => "Login exitoso",
        "token"   => $token,
        "usuario" => [
            "id" => $usuario['id_usuario'],
            "nombre" => $usuario['nombre'],
            "rol" => $usuario['rol']
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión BD: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error inesperado: " . $e->getMessage()]);
}