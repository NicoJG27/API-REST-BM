<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config.php";   // Asegúrate de que config.php está en la carpeta superior
require_once __DIR__ . "/../database.php"; // Asegúrate de que database.php está en la carpeta superior

// CORRECCIÓN 1: Nombres de archivo coincidiendo con lo que subiste (plurales)
require_once __DIR__ . "/../modelo/platos_modelo.php"; 
require_once __DIR__ . "/../controlador/platos_controlador.php";
require_once __DIR__ . "/auth.php";

// Inicializamos base de datos y controlador
$db = Database::getConnection();
$modelo = new PlatosModelo($db);
$controlador = new PlatosControlador($modelo);

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$esDetallado = isset($_GET['detallado']);

// CORRECCIÓN 2: El switch que faltaba para manejar la lógica
switch ($metodo) {
    case 'GET':
        if ($id) {
            if ($esDetallado) {
                 echo json_encode($controlador->verDetallado($id));
            } else {
                 echo json_encode($controlador->ver($id));
            }
        } else {
            echo json_encode($controlador->listar());
        }
        break;

    case 'POST':
        $user = requireAuth();
        
        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        echo json_encode($controlador->crear($datos));
        break;

    case 'PUT':
        $user = requireAuth(); 

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        echo json_encode($controlador->actualizar($id, $datos));
        break;

    case 'DELETE':
        $user = requireAuth(); 

        echo json_encode($controlador->eliminar($id));
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
