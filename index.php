<?php
// SOLO para probar - sin redirecciones
echo "🎉 ¡SISTEMA DE REQUISICIONES FUNCIONANDO!<br>";
echo "<h3>Proyecto: ProyectoPHP</h3>";
echo "<hr>";

// Mostrar estructura disponible
echo "<h4>📁 ESTRUCTURA DEL PROYECTO:</h4>";
$estructura = [
    'backend/' => 'Lógica del sistema',
    'frontend/' => 'Interfaz de usuario', 
    'frontend/views/auth/login.php' => 'Página de login',
    'uploads/' => 'Archivos subidos'
];

foreach ($estructura as $ruta => $descripcion) {
    if (file_exists($ruta)) {
        echo "✅ <a href='$ruta'>$ruta</a> - $descripcion<br>";
    } else {
        echo "❌ $ruta - $descripcion<br>";
    }
}

echo "<hr>";
echo "<h4>🚀 ACCESO DIRECTO:</h4>";
echo "<a href='frontend/views/auth/login.php' style='font-size: 18px; color: blue;'>🔐 IR AL LOGIN</a>";
?>