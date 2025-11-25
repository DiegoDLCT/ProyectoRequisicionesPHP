<?php
require_once 'backend/config/database.php';
require_once 'backend/models/Usuario.php';

echo "🔍 TEST MODELO USUARIO<br>";

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);

// Probar búsqueda por email
$email = "admin@admin.com";
$usuarioEncontrado = $usuarioModel->buscarPorEmail($email);

if ($usuarioEncontrado) {
    echo "✅ USUARIO ENCONTRADO POR EMAIL<br>";
    echo "📧 Email: " . $usuarioEncontrado['cCorreo'] . "<br>";
    echo "👤 Nombre: " . $usuarioEncontrado['cNombre'] . "<br>";
    echo "💼 Puesto: " . $usuarioEncontrado['cPuesto'] . "<br>";
    
    // Probar verificación de contraseña
    $password_correcta = "admin123";
    $password_incorrecta = "wrongpassword";
    
    echo "<br>🔐 PROBAR CONTRASEÑAS:<br>";
    echo "Contraseña correcta: " . (password_verify($password_correcta, $usuarioEncontrado['cContrasena']) ? "✅ VÁLIDA" : "❌ INVÁLIDA") . "<br>";
    echo "Contraseña incorrecta: " . (password_verify($password_incorrecta, $usuarioEncontrado['cContrasena']) ? "✅ VÁLIDA" : "❌ INVÁLIDA") . "<br>";
    
} else {
    echo "❌ USUARIO NO ENCONTRADO<br>";
}
?>