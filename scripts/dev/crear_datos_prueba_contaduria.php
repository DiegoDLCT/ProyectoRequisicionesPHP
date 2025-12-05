<?php
require 'backend/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔧 CREAR DATOS DE PRUEBA PARA CONTADURÍA\n\n";

try {
    // 1. Crear una requisición en estado pago_solicitado
    $query = "INSERT INTO requisiciones (cFolio, dFechaSolicitud, idSolicitante, idArea, cDescripcion, bRequiereCotizacion, estado, lActivo)
              VALUES (:folio, NOW(), :solicitante, :area, :desc, 1, 'pago_solicitado', 1)";
    $stmt = $conn->prepare($query);
    
    $folio = 'PRUEBA-' . date('YmdHis');
    $solicitante = 1; // admin
    $area = 1;
    $desc = 'Requisición de prueba para contaduría';
    
    $stmt->bindParam(':folio', $folio);
    $stmt->bindParam(':solicitante', $solicitante);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':desc', $desc);
    
    if ($stmt->execute()) {
        $id_requisicion = $conn->lastInsertId();
        echo "✅ Requisición creada: ID $id_requisicion, Folio: $folio\n";
        
        // 2. Crear cotización aprobada
        $query_cot = "INSERT INTO cotizaciones (idRequisicion, cProveedor, deMonto, bAprovada, cArchivourl, dFechacotizacion, cNumcotizacion)
                      VALUES (:req_id, :proveedor, :monto, 1, :archivo, NOW(), :numcot)";
        $stmt_cot = $conn->prepare($query_cot);
        
        $proveedor = 'Proveedor Prueba';
        $monto = 5000.00;
        $archivo = 'prueba.pdf';
        $numcot = 'COT-' . date('YmdHis');
        
        $stmt_cot->bindParam(':req_id', $id_requisicion);
        $stmt_cot->bindParam(':proveedor', $proveedor);
        $stmt_cot->bindParam(':monto', $monto);
        $stmt_cot->bindParam(':archivo', $archivo);
        $stmt_cot->bindParam(':numcot', $numcot);
        
        if ($stmt_cot->execute()) {
            $id_cotizacion = $conn->lastInsertId();
            echo "✅ Cotización creada: ID $id_cotizacion\n";
            echo "\n📋 Datos de la requisición de prueba:\n";
            echo "   - Folio: $folio\n";
            echo "   - Proveedor: $proveedor\n";
            echo "   - Monto: \$$monto\n";
            echo "   - Estado: pago_solicitado\n";
            echo "\n✨ Ahora puedes ir a 'Confirmar Pagos' en el dashboard de contaduría\n";
        } else {
            echo "❌ Error al crear cotización\n";
            print_r($stmt_cot->errorInfo());
        }
    } else {
        echo "❌ Error al crear requisición\n";
        print_r($stmt->errorInfo());
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
