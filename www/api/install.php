<?php
// www/api/install.php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../database.php";

try {
    $conn = Database::getConnection();
    
    // 1. Crear tabla usuarios si no existe
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol VARCHAR(20) NOT NULL DEFAULT 'user'
    )";
    $conn->exec($sql);
    echo "✅ Tabla 'usuarios' revisada.<br>";

    // 2. Crear usuario Admin (admin@test.com / 1234)
    // Generamos el hash seguro de '1234'
    $passHash = password_hash("1234", PASSWORD_DEFAULT);
    $email = "admin@test.com";
    
    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 0) {
        $insert = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $insert->execute(["Administrador", $email, $passHash, "admin"]);
        echo "✅ Usuario Admin creado correctamente (Email: $email, Pass: 1234)<br>";
    } else {
        echo "ℹ️ El usuario Admin ya existe.<br>";
    }

} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
?>