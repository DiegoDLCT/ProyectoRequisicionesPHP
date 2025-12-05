<?php
require_once 'backend/config/database.php';

echo "🔐 PREPARAR USUARIO PARA LOGIN<br>";

$database = new Database();
$conn = $database->getConnection();

// Hashear la contraseña "admin123"
$password = "admin123";
$password_hasheada = password_hash($password, PASSWORD_DEFAULT);

// Actualizar el usuario admin con contraseña hasheada
$query = "UPDATE usuarios SET cContrasena = :contrasena WHERE id = 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(":contrasena", $password_hasheada);

if ($stmt->execute()) {
    echo "✅ CONTRASEÑA ACTUALIZADA CORRECTAMENTE<br>";
    echo "📧 Email: admin@admin.com<br>";
    echo "🔑 Contraseña: admin123<br>";
} else {
    echo "❌ ERROR AL ACTUALIZAR CONTRASEÑA<br>";
}
?>