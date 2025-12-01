<?php
// ... (Tus require_once anteriores se mantienen igual) ...
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../modelo/platos_modelo.php";
require_once __DIR__ . "/../controlador/platos_controlador.php";
require_once __DIR__ . "/auth.php";

$db = Database::getConnection();
$modelo = new PlatosModelo($db);
$controlador = new PlatosControlador($modelo);

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$esDetallado = isset($_GET['detallado']);

switch ($metodo) {
    case 'GET':
        // PÚBLICO: Cualquiera puede ver los platos
        $pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limite = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $busqueda = isset($_GET['search']) ? $_GET['search'] : null;
        $orden = isset($_GET['order']) ? $_GET['order'] : 'id_plato';
        $dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';
        
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
        // AUTENTICADO: Admin o Usuario
        $user = requireAuth(); // Si falla, se corta aquí y devuelve 401

        // Verificamos el rol
        if ($user->rol !== 'administrador' && $user->rol !== 'usuario') {
            http_response_code(403); // Forbidden (Prohibido)
            echo json_encode(["error" => "No tienes permisos para crear platos. Rol necesario: usuario o administrador."]);
            exit;
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        echo json_encode($controlador->crear($datos));
        break;

    case 'PUT':
        // SOLO ADMIN
        $user = requireAuth();

        if ($user->rol !== 'administrador') {
            http_response_code(403);
            echo json_encode(["error" => "Acceso denegado. Solo los administradores pueden editar."]);
            exit;
        }

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        echo json_encode($controlador->actualizar($id, $datos));
        break;

    case 'DELETE':
        // SOLO ADMIN
        $user = requireAuth();

        if ($user->rol !== 'administrador') {
            http_response_code(403);
            echo json_encode(["error" => "Acceso denegado. Solo los administradores pueden eliminar."]);
            exit;
        }

        echo json_encode($controlador->eliminar($id));
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}