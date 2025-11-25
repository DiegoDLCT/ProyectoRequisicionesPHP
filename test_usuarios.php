<?php
require_once 'backend/config/database.php';

echo "🔍 TEST ESTRUCTURA USUARIOS<br>";

$database = new Database();
$conn = $database->getConnection();

// Verificar si la tabla usuarios existe y tiene datos
$query = "SELECT COUNT(*) as total FROM usuarios";
$stmt = $conn->prepare($query);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

echo "👥 TOTAL USUARIOS EN LA BD: " . $resultado['total'] . "<br>";

// Mostrar algunos usuarios si existen
if ($resultado['total'] > 0) {
    $query = "SELECT id, cNombre, cCorreo, cPuesto FROM usuarios LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 PRIMEROS USUARIOS:<br>";
    foreach ($usuarios as $usuario) {
        echo " - ID: {$usuario['id']} | {$usuario['cNombre']} | {$usuario['cCorreo']} | {$usuario['cPuesto']}<br>";
    }
} else {
    echo "ℹ️ No hay usuarios en la base de datos<br>";
}
?>