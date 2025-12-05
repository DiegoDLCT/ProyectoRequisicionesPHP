<?php
require_once 'backend/config/database.php';

echo "📦 CREANDO REQUISICIÓN Y COTIZACIÓN PARA PROBAR COMPRAS<br><br>";

$database = new Database();
$conn = $database->getConnection();

try {
    // 1. Crear requisición en estado 'pago_solicitado'
    $folio_random = 'REQ-COMPRA-' . rand(10000, 99999);
    $query_req = "INSERT INTO requisiciones 
                  (cFolio, dFechaSolicitud, idSolicitante, idArea, cDescripcion, 
                   bRequiereCotizacion, estado, lActivo) 
                  VALUES 
                  ('$folio_random', NOW(), 1, 1, 'Test para COMPRAS', 1, 'pago_solicitado', 1)";
    
    $conn->exec($query_req);
    $requisicion_id = $conn->lastInsertId();
    echo "✅ Requisición creada: ID=$requisicion_id, FOLIO=$folio_random, ESTADO=pago_solicitado<br>";
    
    // 2. Crear cotización aprobada para esa requisición
    $query_cot = "INSERT INTO cotizaciones 
                  (idRequisicion, cProveedor, deMonto, dFechacotizacion, bAprovada) 
                  VALUES 
                  ($requisicion_id, 'Proveedor Prueba', 5000.00, NOW(), 1)";
    
    $conn->exec($query_cot);
    $cotizacion_id = $conn->lastInsertId();
    echo "✅ Cotización creada: ID=$cotizacion_id, MONTO=5000.00, APROBADA=1<br><br>";
    
    // 3. Mostrar estado actual
    echo "📊 ESTADO ACTUAL EN BD:<br>";
    $stmt = $conn->query("SELECT * FROM requisiciones WHERE id=$requisicion_id");
    $req = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Requisición: ";
    var_dump($req);
    
    $stmt = $conn->query("SELECT * FROM cotizaciones WHERE idRequisicion=$requisicion_id");
    $cot = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<br>Cotización: ";
    var_dump($cot);
    
    echo "<br><br>✨ Ya puedes confirmar el pago en: /ProyectoPHP/frontend/views/pagos/confirmar.php<br>";
    echo "ID de requisición para prueba: $requisicion_id<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
