<?php
require_once 'backend/config/database.php';

echo "🗑️  ELIMINANDO TABLAS NO UTILIZADAS\n\n";

$db = new Database();
$conn = $db->getConnection();

try {
    // 1. Eliminar DETALLE_REQUISICION
    echo "1️⃣ Eliminando tabla DETALLE_REQUISICION...\n";
    $conn->exec("DROP TABLE IF EXISTS detalle_requisicion");
    echo "   ✅ Tabla eliminada\n\n";
    
    // 2. Eliminar TRANSFERENCIAS
    echo "2️⃣ Eliminando tabla TRANSFERENCIAS...\n";
    $conn->exec("DROP TABLE IF EXISTS transferencias");
    echo "   ✅ Tabla eliminada\n\n";
    
    // 3. Verificar tablas restantes
    echo "3️⃣ TABLAS RESTANTES EN BD:\n";
    $stmt = $conn->query("SHOW TABLES FROM sistema_requisiciones");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tablas as $tabla) {
        echo "   ✓ $tabla\n";
    }
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TABLAS ELIMINADAS EXITOSAMENTE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n📊 TOTAL DE TABLAS EN BD: " . count($tablas) . "/9\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
