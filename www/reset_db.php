<?php
// reset_db.php
require_once "config.php";
require_once "database.php";

try {
    $db = Database::getConnection();

    // 1. Borramos los usuarios viejos para evitar conflictos
    $db->exec("DELETE FROM Usuarios WHERE email IN ('admin@test.com', 'user@test.com')");

    // 2. Generamos el hash NUEVO y FRESCO de "1234"
    $passHash = password_hash("1234", PASSWORD_DEFAULT);

    // 3. Insertamos el Admin
    $sqlAdmin = "INSERT INTO Usuarios (nombre, email, password, rol) VALUES ('Admin Jefe', 'admin@test.com', :pass, 'admin')";
    $stmt = $db->prepare($sqlAdmin);
    $stmt->bindParam(':pass', $passHash);
    $stmt->execute();

    // 4. Insertamos el User
    $sqlUser = "INSERT INTO Usuarios (nombre, email, password, rol) VALUES ('Cliente', 'user@test.com', :pass, 'user')";
    $stmt = $db->prepare($sqlUser);
    $stmt->bindParam(':pass', $passHash);
    $stmt->execute();

    echo "<h1>✅ Usuarios Reseteados Correctamente</h1>";
    echo "<p>Se han borrado y vuelto a crear los usuarios.</p>";
    echo "<ul>";
    echo "<li><b>Admin:</b> admin@test.com / 1234</li>";
    echo "<li><b>User:</b> user@test.com / 1234</li>";
    echo "</ul>";
    echo "<p>HASH Generado: $passHash</p>";
    echo "<a href='tester.html'>--> IR A PROBAR EL LOGIN AHORA</a>";

} catch (PDOException $e) {
    die("Error en la BD: " . $e->getMessage());
}
?>