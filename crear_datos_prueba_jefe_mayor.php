<?php
require_once 'backend/config/database.php';

echo "📊 CREANDO DATOS DE PRUEBA PARA JEFE MAYOR<br>";

$database = new Database();
$conn = $database->getConnection();

// 1. Crear una requisición en estado "cotizado"
$query_requisicion = "INSERT INTO requisiciones 
    (cFolio, dFechaSolicitud, idSolicitante, idArea, cDescripcion, bRequiereCotizacion, estado, lActivo) 
    VALUES 
    ('REQ202411050', '2024-11-20', 1, 1, 'Material de oficina para nuevo departamento', 1, 'cotizado', 1)";

$conn->exec($query_requisicion);
$id_requisicion = $conn->lastInsertId();
echo "✅ Requisición creada: REQ202411050<br>";

// 2. Crear cotizaciones para esa requisición
$cotizaciones = [
    [
        'idRequisicion' => $id_requisicion,
        'cProveedor' => 'Office Depot',
        'cNumcotizacion' => 'COT-OFF-001',
        'deMonto' => 12500.50,
        'dFechacotizacion' => '2024-11-21',
        'cArchivourl' => 'cotizaciones/office_depot.pdf',
        'bAprovada' => 0
    ],
    [
        'idRequisicion' => $id_requisicion,
        'cProveedor' => 'Office Depot',
        'cNumcotizacion' => 'COT-OFF-001',
        'deMonto' => 12500.50,
        'dFechacotizacion' => '2024-11-21',
        'bAprovada' => 0
    ],
    [
        'idRequisicion' => $id_requisicion,
        'cProveedor' => 'Staples', 
        'cNumcotizacion' => 'COT-STA-002',
        'deMonto' => 11800.00,
        'dFechacotizacion' => '2024-11-21',
        'bAprovada' => 0
    ],
    [
        'idRequisicion' => $id_requisicion,
        'cProveedor' => 'Costco',
        'cNumcotizacion' => 'COT-COS-003',
        'deMonto' => 13200.75,
        'dFechacotizacion' => '2024-11-22',
        'bAprovada' => 0
    ]
];

foreach ($cotizaciones as $cotizacion) {
    $query = "INSERT INTO cotizaciones 
        (idRequisicion, cProveedor, cNumcotizacion, deMonto, dFechacotizacion, cArchivourl, bAprovada)
        VALUES 
        (:idRequisicion, :cProveedor, :cNumcotizacion, :deMonto, :dFechacotizacion, :cArchivourl, :bAprovada)";
    
    $stmt = $conn->prepare($query);
    // execute() recibirá el array asociativo con claves que coinciden con los placeholders
    $stmt->execute($cotizacion);
    echo "✅ Cotización creada: {$cotizacion['cProveedor']} - $" . number_format($cotizacion['deMonto'], 2) . "<br>";
}

echo "<br>🎉 DATOS DE PRUEBA CREADOS EXITOSAMENTE<br>";
echo "👉 <a href='frontend/views/dashboard/jefe_mayor.php' style='color: #2563eb;'>Ver Panel Jefe Mayor</a>";
?>