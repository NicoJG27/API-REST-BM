<?php
// fix_admin.php
header("Content-Type: text/html; charset=utf-8");
require_once "config.php";
require_once "database.php";

echo "<h2>🔧 Reparando Usuario Administrador...</h2>";

try {
    $db = Database::getConnection();

    // 1. Definimos los datos correctos
    $email = 'admin@test.com';
    $passwordPlana = '1234';
    
    // 2. Generamos el hash compatible con TU servidor
    $hashSeguro = password_hash($passwordPlana, PASSWORD_DEFAULT);

    // 3. Borramos el usuario si existe (para evitar conflictos)
    $stmtDelete = $db->prepare("DELETE FROM Usuarios WHERE email = :email");
    $stmtDelete->bindParam(':email', $email);
    $stmtDelete->execute();
    echo "✅ Usuario antiguo eliminado (limpieza)...<br>";

    // 4. Insertamos el usuario nuevo limpio
    $sqlInsert = "INSERT INTO Usuarios (nombre, email, password, rol) 
                  VALUES ('Super Admin', :email, :pass, 'admin')";
    
    $stmt = $db->prepare($sqlInsert);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':pass', $hashSeguro);
    $stmt->execute();

    echo "✅ <b>¡Usuario Creado Exitosamente!</b><br><hr>";
    echo "<h3>Datos para Loguearte en tester.html:</h3>";
    echo "<ul>";
    echo "<li><b>Email:</b> " . $email . "</li>";
    echo "<li><b>Contraseña:</b> " . $passwordPlana . "</li>";
    echo "<li><b>Hash generado:</b> " . $hashSeguro . "</li>";
    echo "</ul>";
    echo "<p><a href='tester.html'>Volver al Tester</a></p>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>Error de Base de Datos:</h3>";
    echo $e->getMessage();
}
?>