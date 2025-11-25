<?php
require_once 'backend/config/database.php';

echo "📝 CREANDO REQUISICIONES DE PRUEBA<br>";

$database = new Database();
$conn = $database->getConnection();

// Datos de prueba - SOLO columnas que EXISTEN en tu tabla
$requisiciones_prueba = [
    [
        'cFolio' => 'REQ202411001',
        'dFechaSolicitud' => '2024-11-15',
        'idSolicitante' => 1,
        'idArea' => 1,
        'cDescripcion' => 'Material para mantenimiento preventivo',
        'bRequiereCotizacion' => 1,
        'estado' => 'pendiente',
        'lActivo' => 1
    ],
    [
        'cFolio' => 'REQ202411002', 
        'dFechaSolicitud' => '2024-11-16',
        'idSolicitante' => 1,
        'idArea' => 2,
        'cDescripcion' => 'Materiales para construcción de oficinas',
        'bRequiereCotizacion' => 0,
        'estado' => 'cotizado',
        'lActivo' => 1
    ],
    [
        'cFolio' => 'REQ202411003',
        'dFechaSolicitud' => '2024-11-17', 
        'idSolicitante' => 1,
        'idArea' => 1,
        'cDescripcion' => 'Refacciones urgentes para equipo',
        'bRequiereCotizacion' => 1,
        'estado' => 'aprobado',
        'lActivo' => 1
    ]
];

$insertados = 0;
foreach ($requisiciones_prueba as $req) {
    $query = "INSERT INTO requisiciones 
          (cFolio, dFechaSolicitud, idSolicitante, idArea, cDescripcion, 
           bRequiereCotizacion, estado, lActivo) 
          VALUES 
          (:cFolio, :dFechaSolicitud, :idSolicitante, :idArea, :cDescripcion, 
           :bRequiereCotizacion, :estado, :lActivo)";
           
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute($req)) {
        $insertados++;
        echo "✅ " . $req['cFolio'] . " - CREADA<br>";
    } else {
        echo "❌ " . $req['cFolio'] . " - ERROR<br>";
        // Mostrar error específico
        $errorInfo = $stmt->errorInfo();
        echo "   Error: " . $errorInfo[2] . "<br>";
    }
}

echo "<br>🎉 TOTAL CREADAS: " . $insertados . " requisiciones de prueba<br>";

// Verificar que se crearon
$stmt = $conn->query("SELECT COUNT(*) as total FROM requisiciones");
$total = $stmt->fetch(PDO::FETCH_ASSOC);
echo "📊 TOTAL EN BD: " . $total['total'] . " requisiciones<br>";

// Enlace para ver la lista
echo "<br><a href='frontend/views/requisiciones/listar.php' style='color: #2563eb;'>📋 Ver Lista de Requisiciones</a>";
?>