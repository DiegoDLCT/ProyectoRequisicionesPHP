<?php
require_once 'backend/config/database.php';
require_once 'backend/controllers/RequisicionController.php';

echo "✅ PROBANDO CREACIÓN DE REQUISICIÓN CON UNIDAD\n\n";

$controller = new RequisicionController();

// Datos de prueba
$datos_test = [
    'id_area' => 1,
    'descripcion' => 'Prueba de integración UNIDADES',
    'id_unidad' => 2,  // Kilogramo
    'maquina' => 'Excavadora CAT 320',
    'obra_ubicacion' => 'Obra Norte'
];

$id_usuario_test = 1;

try {
    echo "📝 Creando requisición con:\n";
    echo "   - Área: 1\n";
    echo "   - Unidad: 2 (Kilogramo)\n";
    echo "   - Descripción: " . $datos_test['descripcion'] . "\n";
    echo "   - Usuario: $id_usuario_test\n\n";
    
    $id_req = $controller->crearRequisicion($datos_test, $id_usuario_test);
    
    if ($id_req) {
        echo "✅ Requisición creada: ID=$id_req\n\n";
        
        // Verificar en BD
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT r.*, u.cNombre as solicitante, a.cNombre as area, 
                               COALESCE(un.cNombre, 'N/A') as unidad_nombre
                               FROM requisiciones r
                               LEFT JOIN usuarios u ON r.idSolicitante = u.id
                               LEFT JOIN areas a ON r.idArea = a.id
                               LEFT JOIN unidades un ON r.idUnidad = un.id
                               WHERE r.id = :id");
        $stmt->bindParam(':id', $id_req);
        $stmt->execute();
        $requisicion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "📋 DATOS GUARDADOS EN BD:\n";
        echo "   ID: " . $requisicion['id'] . "\n";
        echo "   Folio: " . $requisicion['cFolio'] . "\n";
        echo "   Solicitante: " . $requisicion['solicitante'] . "\n";
        echo "   Área: " . $requisicion['area'] . "\n";
        echo "   Descripción: " . $requisicion['cDescripcion'] . "\n";
        echo "   Unidad: " . $requisicion['unidad_nombre'] . " (ID: " . $requisicion['idUnidad'] . ")\n";
        echo "   Estado: " . $requisicion['estado'] . "\n";
        echo "   Máquina: " . ($requisicion['cMaquina'] ?? 'N/A') . "\n";
        echo "   Ubicación: " . $requisicion['cObraUbicacion'] . "\n";
        
        echo "\n✨ Integración UNIDADES completada exitosamente\n";
    } else {
        echo "❌ Error al crear requisición\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
