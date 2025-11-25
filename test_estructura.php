<?php
// Colocar en la raíz del proyecto
echo "=== PRUEBA DE ESTRUCTURA ===<br>";

// 1. Probar includes
echo "1. Probando includes... ";
require_once 'backend/config/database.php';
require_once 'backend/utils/Session.php';
echo "✅ OK<br>";

// 2. Probar conexión BD
echo "2. Probando conexión BD... ";
$database = new Database();
$conn = $database->getConnection();
if ($conn) {
    echo "✅ CONEXIÓN EXITOSA<br>";
} else {
    echo "❌ ERROR EN CONEXIÓN<br>";
}

// 3. Probar sesiones
echo "3. Probando sesiones... ";
Session::set('test', 'funciona');
echo Session::get('test') . "<br>";

// 4. Probar constantes
echo "4. Probando constantes... ";
require_once 'backend/config/constants.php';
echo ROL_ADMIN . "<br>";

echo "=== PRUEBA COMPLETADA ===";
?>