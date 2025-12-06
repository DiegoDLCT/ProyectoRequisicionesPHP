<?php
require_once dirname(__DIR__, 2) . '/backend/config/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // Estados que no son "entregado"
    $estadosEnCurso = ['pendiente', 'cotizado', 'pago_solicitado', 'pagado', 'por_entregar'];
    
    // Placeholders para la consulta
    $placeholders = implode(',', array_fill(0, count($estadosEnCurso), '?'));
    
    // Actualizar todas las requisiciones en curso a "entregado"
    $sql = "UPDATE requisiciones SET estado = 'entregado' 
            WHERE estado IN ($placeholders) AND lActivo = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($estadosEnCurso);
    
    $afectadas = $stmt->rowCount();
    
    echo "✅ Script completado\n";
    echo "Requisiciones actualizadas a 'entregado': $afectadas\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
