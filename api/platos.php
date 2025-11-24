<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../modelo/plato_modelo.php";
require_once __DIR__ . "/../controlador/platos_controlador.php";
require_once __DIR__ . "/auth.php";


$user = requireAuth(); // Obtenemos el payload.
// Crear conexión, modelo y controlador.
$db = Database::getConnection();
$modelo = new PlatosModelo($db);
$controlador = new PlatosControlador($modelo);

$methos = $_SERVER['REQUEST_METHOD'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

?>