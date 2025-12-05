<?php
require 'backend/config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query('DESCRIBE requisiciones');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    echo $c['Field'] . ' - ' . $c['Type'] . PHP_EOL;
}
?>
