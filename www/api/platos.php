<?php
// 1. Cabeceras deben ir ANTES de cualquier echo o espacio en blanco
header("Content-Type: application/json; charset=utf-8");

// 2. Inclusiones (Rutas ajustadas a tu estructura de carpetas)
require_once __DIR__ . "/../vendor/autoload.php"; 
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../database.php";

require_once __DIR__ . "/../modelo/platos_modelo.php";
require_once __DIR__ . "/../controlador/platos_controlador.php";

require_once __DIR__ . "/auth.php"; 

try {
    $db = Database::getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit;
}

// 4. Instancias
$modelo = new PlatosModelo($db);
$controlador = new PlatosControlador($modelo);

// 5. Router y Lógica
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($method === 'GET') {
    // IMPORTANTE: Aquí solo obtenemos los datos, NO hacemos echo todavía
    if ($id !== null && $id > 0) {
        $respuesta = $controlador->verDetallado($id);
    } else {
        $respuesta = $controlador->listar();
    }

    // FINALMENTE: Hacemos el único echo del script
    echo json_encode($respuesta);

} else {
    // Métodos no soportados
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>