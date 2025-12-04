<?php
// sabotaje.php
require_once "config.php";
require_once "database.php";

try {
    $db = Database::getConnection();

    // Vamos a cambiar el precio de TODOS los platos a 99.99
    // Esto es un cambio directo en la tabla MySQL
    $db->exec("UPDATE Platos SET precio = 99.99");

    echo "<h1>😈 ¡SABOTAJE REALIZADO! 😈</h1>";
    echo "<p>He entrado en tu base de datos MySQL y he puesto todos los precios a <b>99.99</b>.</p>";
    echo "<p>Si tu API está conectada de verdad, ahora cuando hagas un GET, deberías ver ese precio.</p>";
    echo "<a href='tester.html'>--> VOLVER AL TESTER PARA COMPROBAR</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>