<?php
// Activar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 TEST LISTA REQUISICIONES - MODO DEBUG<br>";

require_once 'backend/config/database.php';
require_once 'backend/controllers/RequisicionController.php';

try {
    $requisicionController = new RequisicionController();
    echo "✅ CONTROLADOR CARGADO<br>";
    
    $requisiciones = $requisicionController->obtenerTodas();
    echo "✅ MÉTODO obtenerTodas() EJECUTADO<br>";
    
    echo "📊 TOTAL REQUISICIONES: " . count($requisiciones) . "<br>";
    
    if (count($requisiciones) > 0) {
        echo "📋 PRIMERAS REQUISICIONES:<br>";
        foreach (array_slice($requisiciones, 0, 3) as $req) {
            echo " - " . $req['cFolio'] . " | " . $req['solicitante_nombre'] . " | " . $req['estado'] . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
    echo "📄 ARCHIVO: " . $e->getFile() . "<br>";
    echo "📝 LÍNEA: " . $e->getLine() . "<br>";
}
?>