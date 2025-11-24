<?php
// test_db.php

// 1. Incluimos tu configuración para ver si las constantes funcionan
require_once 'config.php';

echo "<h1>Test de Conexión a Base de Datos</h1>";
echo "<p>Intentando conectar a: <strong>" . DB_DSN . "</strong>...</p>";

try {
    // 2. Intentamos crear la conexión usando PDO (esto prueba el driver pdo_mysql)
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    
    // Configurar PDO para que nos lance errores si algo falla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green'>✅ <strong>¡ÉXITO!</strong> La conexión se ha establecido correctamente.</p>";
    echo "<p>El driver PDO MySQL está instalado y funcionando.</p>";

    // 3. Prueba de Fuego: Leer datos reales
    // Intentamos sacar las tablas de la base de datos para ver si coinciden con lo que ves en phpMyAdmin
    echo "<h3>Tablas encontradas en la base de datos:</h3>";
    echo "<ul>";
    
    $stmt = $pdo->query("SHOW TABLES");
    
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";

} catch (PDOException $e) {
    // Si algo falla, aquí te dirá exactamente por qué
    echo "<p style='color:red'>❌ <strong>ERROR DE CONEXIÓN:</strong></p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    
    echo "<hr>";
    echo "<h4>Posibles causas:</h4>";
    echo "<ul>
            <li>¿Has puesto <code>host=db</code> en el config.php? (No uses localhost)</li>
            <li>¿Coincide el nombre de la BD en docker-compose y config.php?</li>
            <li>¿Son correctos el usuario y la contraseña?</li>
          </ul>";
}
?>