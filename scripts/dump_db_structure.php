<?php
// Exporta la estructura de la base de datos usando la configuración de backend/config/database.php
// Ejecución: php scripts/dump_db_structure.php

require_once __DIR__ . '/../backend/config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo "Error: no se pudo conectar a la base de datos. Revisa backend/config/database.php\n";
    exit(1);
}

try {
    $tables = [];
    $stmt = $conn->query('SHOW TABLES');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $dump = "-- DB structure dump generated: " . date('c') . "\n\n";
    $dump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        $row = $conn->query("SHOW CREATE TABLE `" . $table . "`")->fetch(PDO::FETCH_ASSOC);
        $dump .= "-- Table: $table\n";
        $dump .= $row['Create Table'] . ";\n\n";
    }

    $dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    $outPath = __DIR__ . '/../migrations/db_structure_dump.sql';
    file_put_contents($outPath, $dump);

    echo "OK: Estructura exportada a: $outPath\n\n";
    echo $dump;

} catch (Exception $e) {
    echo "Error al generar dump: " . $e->getMessage() . "\n";
    exit(1);
}

?>
