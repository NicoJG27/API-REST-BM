<?php
// www/api/platos.php

// --- AQUI SIGUE TU CÓDIGO NORMAL ---
header("Content-Type: application/json; charset=utf-8");
// ... headers, requires, etc ...
require_once __DIR__ . "/../vendor/autoload.php";
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../modelo/platos_modelo.php";
require_once __DIR__ . "/../controlador/platos_controlador.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/logger.php";

// Inicializamos
$db = Database::getConnection();
$modelo = new PlatosModelo($db);
$controlador = new PlatosControlador($modelo);

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$esDetallado = isset($_GET['detallado']) || (isset($_GET['include']) && $_GET['include'] === 'categorias');
$esConteo = isset($_GET['count']);

switch ($metodo) {
    case 'GET':
        // PÚBLICO
        $pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limite = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $busqueda = isset($_GET['search']) ? $_GET['search'] : null;
        $orden = isset($_GET['order']) ? $_GET['order'] : 'id_plato';
        $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
        
        // /api/platos/count -> estadísticas básicas
        if ($esConteo && !$id) {
            echo json_encode($controlador->estadisticas());
            break;
        }

        if ($id) {
            if ($esDetallado) {
                echo json_encode($controlador->verDetallado($id));
            } else {
                echo json_encode($controlador->ver($id));
            }
        } else {
            echo json_encode($controlador->listar($pagina, $limite, $busqueda, $orden, $dir));
        }
        break;

    case 'POST':
        // AUTENTICADO: admin o user
        $user = requireAuth(); 
        
        // Verificamos roles exactos de tu BD
        if ($user->rol !== 'admin' && $user->rol !== 'user') {
            http_response_code(403); 
            echo json_encode(["error" => "No tienes permisos. Rol necesario: user o admin."]);
            exit;
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        echo json_encode($controlador->crear($datos, $user->id));
        break;

    case 'PUT':
        // SOLO ADMIN
        $user = requireAuth();

        if ($user->rol !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acceso denegado. Solo admin puede editar."]);
            exit;
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        echo json_encode($controlador->actualizar($id, $datos, $user->id));
        break;

    case 'DELETE':
        // SOLO ADMIN
        $user = requireAuth();

        if ($user->rol !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acceso denegado. Solo admin puede eliminar."]);
            exit;
        }

        echo json_encode($controlador->eliminar($id, $user->id));
        break;

    case 'OPTIONS':
        // Para evitar problemas de CORS en navegadores
        http_response_code(200);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}