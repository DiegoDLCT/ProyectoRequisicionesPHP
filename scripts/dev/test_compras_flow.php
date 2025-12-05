<?php
require_once 'backend/config/database.php';

$db = (new Database())->getConnection();

echo "📊 ANTES DE CONFIRMAR PAGO:\n";
$stmt = $db->query('SELECT COUNT(*) as total FROM compras');
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "COMPRAS: " . $result['total'] . " registros\n\n";

// Obtener datos de la requisición para COMPRAS
$id_requisicion = 16;
$id_tipo_pago = 1;

echo "🔄 PROCESANDO CONFIRMACIÓN DE PAGO...\n";
echo "ID Requisición: $id_requisicion\n";
echo "ID Tipo Pago: $id_tipo_pago\n\n";

try {
    // Obtener datos de la requisición
    $query_req = "SELECT r.id, r.cDescripcion, c.deMonto, t.CNombre
                  FROM requisiciones r
                  LEFT JOIN cotizaciones c ON c.idRequisicion = r.id AND c.bAprovada = 1
                  LEFT JOIN tipos_pago t ON t.id = :id_tipo_pago
                  WHERE r.id = :id";
    $stmt_req = $db->prepare($query_req);
    $stmt_req->bindParam(':id', $id_requisicion);
    $stmt_req->bindParam(':id_tipo_pago', $id_tipo_pago);
    $stmt_req->execute();
    $requisicion_data = $stmt_req->fetch(PDO::FETCH_ASSOC);
    
    if (!$requisicion_data) {
        echo "❌ No se encontraron datos de la requisición\n";
        exit;
    }
    
    echo "✅ Datos obtenidos de requisición:\n";
    var_dump($requisicion_data);
    
    // Cambiar estado de pago_solicitado a pagado
    $query_update = "UPDATE requisiciones SET estado = 'pagado' WHERE id = :id AND estado = 'pago_solicitado'";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':id', $id_requisicion);
    
    if (!$stmt_update->execute()) {
        echo "\n❌ Error al actualizar estado\n";
        exit;
    }
    
    echo "\n✅ Estado actualizado a 'pagado'\n";
    
    // Crear registro en tabla COMPRAS
    $query_compra = "INSERT INTO compras (idRequisicion, idTipoPago, cFormapago, deMontoTotal, dFechaCompra)
                     VALUES (:idRequisicion, :idTipoPago, :cFormapago, :deMontoTotal, NOW())";
    $stmt_compra = $db->prepare($query_compra);
    $stmt_compra->bindParam(':idRequisicion', $id_requisicion);
    $stmt_compra->bindParam(':idTipoPago', $id_tipo_pago);
    $stmt_compra->bindParam(':cFormapago', $requisicion_data['CNombre']);
    $stmt_compra->bindParam(':deMontoTotal', $requisicion_data['deMonto']);
    
    if ($stmt_compra->execute()) {
        echo "✅ Registro insertado en COMPRAS\n";
    } else {
        echo "❌ Error al insertar en COMPRAS\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit;
}

echo "\n\n📊 DESPUÉS DE CONFIRMAR PAGO:\n";

$stmt = $db->query('SELECT * FROM compras WHERE idRequisicion = 16');
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if ($compra) {
    echo "✅ COMPRA REGISTRADA:\n";
    echo "ID: " . $compra['id'] . "\n";
    echo "idRequisicion: " . $compra['idRequisicion'] . "\n";
    echo "idTipoPago: " . $compra['idTipoPago'] . "\n";
    echo "cFormapago: " . $compra['cFormapago'] . "\n";
    echo "deMontoTotal: $" . $compra['deMontoTotal'] . "\n";
    echo "dFechaCompra: " . $compra['dFechaCompra'] . "\n";
    echo "cFacturaurl: " . ($compra['cFacturaurl'] ? $compra['cFacturaurl'] : 'NULL') . "\n";
} else {
    echo "❌ No se creó registro en COMPRAS\n";
}

$stmt = $db->query('SELECT estado FROM requisiciones WHERE id = 16');
$req = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\n✅ Requisición ID 16 - Estado: " . $req['estado'] . "\n";
?>
