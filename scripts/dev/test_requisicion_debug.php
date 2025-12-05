<?php
// Activar mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 TEST MODELO REQUISICIÓN - MODO DEBUG<br>";

require_once 'backend/config/database.php';
require_once 'backend/models/Requisicion.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $requisicion = new Requisicion($db);
    
    echo "✅ MODELO REQUISICIÓN CARGADO CORRECTAMENTE<br>";
    
    // Probar creación de una requisición de prueba (más simple)
    $requisicion->idSolicitante = 1;
    $requisicion->idArea = 1;
    $requisicion->cDescripcion = "Requisición de prueba";
    $requisicion->bRequiereCotizacion = 1;
    $requisicion->cObraUbicacion = "Obra prueba";
    
    echo "📝 INTENTANDO CREAR REQUISICIÓN...<br>";
    
    $id_nueva_requisicion = $requisicion->crear();
    
    if ($id_nueva_requisicion) {
        echo "✅ REQUISICIÓN CREADA EXITOSAMENTE<br>";
        echo "📄 ID: " . $id_nueva_requisicion . "<br>";
        echo "📋 Folio: " . $requisicion->cFolio . "<br>";
    } else {
        echo "❌ ERROR AL CREAR REQUISICIÓN<br>";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR CAPTURADO: " . $e->getMessage() . "<br>";
    echo "📄 ARCHIVO: " . $e->getFile() . "<br>";
    echo "📝 LÍNEA: " . $e->getLine() . "<br>";
}

echo "🏁 FIN DE LA PRUEBA";
?>