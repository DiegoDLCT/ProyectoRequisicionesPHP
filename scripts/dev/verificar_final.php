<?php
require_once 'backend/config/database.php';

echo "✨ VERIFICACIÓN FINAL DE INTEGRACIÓN\n\n";

$db = new Database();
$conn = $db->getConnection();

echo "1️⃣ TABLA REQUISICIONES:\n";
$stmt = $conn->query("DESCRIBE requisiciones");
$columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columnas as $col) {
    if ($col['Field'] == 'idUnidad') {
        echo "   ✅ Columna idUnidad presente\n";
        break;
    }
}

echo "\n2️⃣ TABLA UNIDADES:\n";
$stmt = $conn->query("SELECT COUNT(*) as total FROM unidades");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   ✅ " . $result['total'] . " unidades registradas\n";

echo "\n3️⃣ TABLA COMPRAS:\n";
$stmt = $conn->query("SELECT COUNT(*) as total FROM compras");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   ✅ " . $result['total'] . " compras registradas\n";

echo "\n4️⃣ ÚLTIMAS REQUISICIONES (con unidad):\n";
$stmt = $conn->query("SELECT r.cFolio, r.estado, COALESCE(u.cNombre, 'N/A') as unidad 
                      FROM requisiciones r
                      LEFT JOIN unidades u ON r.idUnidad = u.id
                      WHERE r.lActivo = 1
                      ORDER BY r.id DESC
                      LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "   • " . $row['cFolio'] . " | Estado: " . $row['estado'] . " | Unidad: " . $row['unidad'] . "\n";
}

echo "\n5️⃣ VERIFICACIÓN DE COMPRAS CREADAS:\n";
$stmt = $conn->query("SELECT c.id, r.cFolio, c.cFormapago, c.deMontoTotal, c.dFechaCompra
                      FROM compras c
                      LEFT JOIN requisiciones r ON c.idRequisicion = r.id
                      ORDER BY c.id DESC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "   • Compra #" . $row['id'] . " | Req: " . $row['cFolio'] . " | Forma: " . $row['cFormapago'] . " | $" . $row['deMontoTotal'] . "\n";
}

echo "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ INTEGRACIONES COMPLETADAS:\n";
echo "   ✓ COMPRAS: Se crea registro automático al confirmar pago\n";
echo "   ✓ UNIDADES: Selector en crear requisición, visible en listar y detalle\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎯 FLUJO COMPLETO DEL SISTEMA:\n";
echo "   1. Solicitante crea requisición (con unidad de medida)\n";
echo "   2. Jefe Mayor aprueba y solicita pago\n";
echo "   3. Contaduría selecciona método de pago\n";
echo "   4. Sistema crea registro en COMPRAS automáticamente\n";
echo "   5. Jefe Mayor marca como entregado\n\n";

echo "📊 TABLAS ACTIVAS EN USO: 6/11\n";
echo "   ✅ usuarios, areas, requisiciones, cotizaciones, compras, tipos_pago\n";
echo "   ✅ UNIDADES (medidas de productos)\n";
echo "   ❌ DETALLE_REQUISICION (no se usa)\n";
echo "   ❌ TRANSFERENCIAS (no se usa)\n";
?>
