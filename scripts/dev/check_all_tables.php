<?php
require 'backend/config/database.php';
$db = new Database();
$conn = $db->getConnection();

echo "📊 TABLAS EN LA BASE DE DATOS\n\n";

$stmt = $conn->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sistema_requisiciones'");
$tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tablas as $tabla) {
    echo "=== " . strtoupper($tabla) . " ===\n";
    $describe = $conn->query("DESCRIBE " . $tabla);
    $cols = $describe->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cols as $col) {
        echo "  " . $col['Field'] . " (" . $col['Type'] . ")" . ($col['Null'] === 'NO' ? ' NOT NULL' : ' NULLABLE') . "\n";
    }
    echo "\n";
}
?>
