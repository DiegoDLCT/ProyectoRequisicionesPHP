<?php
session_start();

// DEBUG TEMPORAL
echo "<!-- DEBUG: Usuario ID: " . $_SESSION['usuario_id'] . " -->";

session_start();
// DEBUG TEMPORAL
echo "<!-- DEBUG: Usuario ID: " . $_SESSION['usuario_id'] . " -->";
echo "<!-- DEBUG: Rol: " . $_SESSION['cPuesto'] . " -->";

require_once dirname(__DIR__, 2) . '/backend/config/database.php';
require_once dirname(__DIR__, 2) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 2) . '/backend/controllers/AprobacionController.php';

if (!tieneRol('jefe_mayor')) {
    header("Location: ../auth/login.php");
    exit;
}

$aprobacionController = new AprobacionController();
$requisiciones = $aprobacionController->obtenerAprobacionesPendientes();
// DEBUG: Mostrar lo que viene de la consulta
echo "<!-- DEBUG: Total requisiciones: " . count($requisiciones) . " -->";
foreach($requisiciones as $req) {
    echo "<!-- DEBUG Requisición: " . $req['cFolio'] . " - Estado: " . $req['estado'] . " - Cotizaciones: " . $req['total_cotizaciones'] . " -->";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Cotizaciones</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .cotizaciones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .cotizacion-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .preview-container {
            height: 400px;
            margin: 10px 0;
            border: 1px solid #eee;
        }
        .preview-container iframe, 
        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .btn-aprobar {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .requisicion-header {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .estado-pendiente {
            color: #856404;
            background: #fff3cd;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .estado-cotizado {
            color: #004085;
            background: #cce5ff;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .estado-aprobado {
            color: #155724;
            background: #d4edda;
            padding: 2px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include dirname(__DIR__) . '/shared/header.php'; ?>
    <div class="container">
        <h2>Aprobar Cotizaciones</h2>
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (empty($requisiciones)): ?>
            <div class="alert alert-info">No hay cotizaciones pendientes de aprobación</div>
        <?php else: ?>
            <?php foreach($requisiciones as $requisicion): ?>
                <div class="requisicion-header">
                    <h4>Requisición: <?= htmlspecialchars($requisicion['cFolio']) ?> 
                        <span class="estado-<?= $requisicion['estado'] ?>">
                            <?= ucfirst($requisicion['estado']) ?>
                        </span>
                    </h4>
                    <p><strong>Descripción:</strong> <?= htmlspecialchars($requisicion['cDescripcion']) ?></p>
                    <p><strong>Solicitante:</strong> <?= htmlspecialchars($requisicion['solicitante']) ?></p>
                    <p><strong>Total de cotizaciones pendientes:</strong> <?= $requisicion['total_cotizaciones'] ?></p>
                </div>
                <?php
                $cotizaciones = $aprobacionController->obtenerCotizacionesPorRequisicion($requisicion['id']);
                ?>
                <div class="cotizaciones-grid">
                    <?php foreach($cotizaciones as $cotizacion): ?>
                        <div class="cotizacion-card">
                            <h5><?= htmlspecialchars($cotizacion['cProveedor']) ?></h5>
                            <p><strong>Monto:</strong> $<?= number_format($cotizacion['deMonto'], 2) ?></p>
                            <p><strong>N° Cotización:</strong> <?= htmlspecialchars($cotizacion['cNumcotizacion']) ?></p>
                            <div class="preview-container">
                                <?php if (!empty($cotizacion['cArchivourl'])): ?>
                                    <?php
                                    $extension = strtolower(pathinfo($cotizacion['cArchivourl'], PATHINFO_EXTENSION));
                                    if ($extension === 'pdf'): ?>
                                        <iframe src="<?= dirname(__DIR__, 2) . '/uploads/cotizaciones/' . htmlspecialchars($cotizacion['cArchivourl']) ?>"></iframe>
                                    <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                        <img src="<?= dirname(__DIR__, 2) . '/uploads/cotizaciones/' . htmlspecialchars($cotizacion['cArchivourl']) ?>" alt="Cotización <?= htmlspecialchars($cotizacion['cProveedor']) ?>">
                                    <?php else: ?>
                                        <p><a href="<?= dirname(__DIR__, 2) . '/uploads/cotizaciones/' . htmlspecialchars($cotizacion['cArchivourl']) ?>" target="_blank">Descargar archivo</a></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p>No hay archivo adjunto</p>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="../../backend/services/aprobar_service.php">
                                <input type="hidden" name="idCotizacion" value="<?= $cotizacion['id'] ?>">
                                <button type="submit" class="btn-aprobar" onclick="return confirm('¿Estás seguro de aprobar esta cotización?')">
                                    Aprobar Esta Cotización
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr style="margin: 40px 0;">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>