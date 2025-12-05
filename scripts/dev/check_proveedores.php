<?php
require 'backend/config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query('DESCRIBE proveedores');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Columnas de proveedores:\n";
foreach($cols as $c) {
    echo $c['Field'] . ' - ' . $c['Type'] . ' (Null: ' . $c['Null'] . ')\n';
}
?>
