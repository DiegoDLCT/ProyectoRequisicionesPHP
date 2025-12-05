<?php
require 'backend/config/database.php';
$db = new Database();
$conn = $db->getConnection();

echo "🗑️ ELIMINAR TABLAS NO UTILIZADAS\n\n";

try {
    // Eliminar constraint si existe
    $conn->exec("ALTER TABLE transferencias DROP FOREIGN KEY transferencias_ibfk_1");
    $conn->exec("DROP TABLE IF EXISTS transferencias");
    echo "✅ Tabla transferencias eliminada\n";
    
    $conn->exec("ALTER TABLE detalle_requisicion DROP FOREIGN KEY detalle_requisicion_ibfk_1");
    $conn->exec("ALTER TABLE detalle_requisicion DROP FOREIGN KEY detalle_requisicion_ibfk_2");
    $conn->exec("DROP TABLE IF EXISTS detalle_requisicion");
    echo "✅ Tabla detalle_requisicion eliminada\n";
    
    echo "\n✨ Tablas eliminadas correctamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
