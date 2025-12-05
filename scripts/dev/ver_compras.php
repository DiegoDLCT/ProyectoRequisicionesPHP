<?php
require_once 'backend/config/database.php';

$db = (new Database())->getConnection();
$stmt = $db->query('SELECT * FROM compras');

echo "📊 REGISTROS EN TABLA COMPRAS:\n";
echo "Total: " . $stmt->rowCount() . " registros\n\n";

foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "─────────────────────────────\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Requisición: " . $row['idRequisicion'] . "\n";
    echo "Tipo Pago: " . $row['idTipoPago'] . "\n";
    echo "Forma Pago: " . $row['cFormapago'] . "\n";
    echo "Monto: $" . $row['deMontoTotal'] . "\n";
    echo "Fecha: " . $row['dFechaCompra'] . "\n";
    echo "Factura: " . ($row['cFacturaurl'] ? $row['cFacturaurl'] : 'N/A') . "\n";
}

echo "\n✅ Integración COMPRAS completada exitosamente\n";
?>
