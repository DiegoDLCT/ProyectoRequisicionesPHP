<?php
// ProyectoPHP/test_conexion.php
require_once 'backend/config/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "✅ CONEXIÓN EXITOSA";
} else {
    echo "❌ ERROR EN CONEXIÓN";
}
?>