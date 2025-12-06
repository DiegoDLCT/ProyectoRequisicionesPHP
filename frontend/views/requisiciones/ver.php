<?php
// Verificar sesión
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: listar.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/backend/controllers/RequisicionController.php';
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';

$usuario = $_SESSION['usuario'];
$requisicionController = new RequisicionController();
$requisicion = $requisicionController->obtenerPorId($_GET['id']);

if (!$requisicion) {
    header('Location: listar.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Requisición <?php echo $requisicion['cFolio']; ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 10px;
        }
        .folio {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }
        .estado-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }
        .estado-pendiente { background: #fef3c7; color: #d97706; }
        .estado-cotizado { background: #dbeafe; color: #1d4ed8; }
        .estado-solicitar_pago { background: #fed7aa; color: #c2410c; }
        .estado-pago_solicitado { background: #e0e7ff; color: #4338ca; }
        .estado-pagado { background: #f3e8ff; color: #7c3aed; }
        .estado-por_entregar { background: #fed7aa; color: #c2410c; }
        .estado-entregado { background: #dcfce7; color: #166534; }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .info-value {
            color: #6b7280;
        }
        .descripcion {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #6b7280;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Detalle de Requisición</h1>
                <div class="folio">
                    <?php echo $requisicion['cFolio']; ?>
                    <span class="estado-badge estado-<?php echo $requisicion['estado']; ?>">
                        <?php 
                        $estados = [
                            'pendiente' => 'Pendiente',
                            'cotizado' => 'Cotizado', 
                            'solicitar_pago' => 'Solicitar Pago',
                            'pago_solicitado' => 'Pago Solicitado',
                            'pagado' => 'Pagado',
                            'por_entregar' => 'Por Entregar',
                            'entregado' => 'Entregado'
                        ];
                        echo $estados[$requisicion['estado']] ?? $requisicion['estado'];
                        ?>
                    </span>
                </div>
            </div>
            <div>
                <button onclick="history.back()" class="btn btn-secondary">Volver</button>
                <a href="../../index.php" class="btn" style="background: #2563eb; margin-left: 10px;"><i class="bi bi-house"></i> Inicio</a>
                <?php if ($requisicion['estado'] == 'pendiente' && !$requisicion['bRequiereCotizacion']): ?>
                    <button onclick="marcarParaSolicitarPago(<?php echo $requisicion['id']; ?>)" class="btn" style="background: #f59e0b;">
                        ✓ Marcar para solicitar pago
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-grid">
                <div class="info-card">
                <div class="info-label">Fecha de Solicitud</div>
                <div class="info-value"><?php echo $requisicion['dFechaSolicitud']; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Solicitante</div>
                <div class="info-value"><?php echo $requisicion['solicitante_nombre']; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Área</div>
                <div class="info-value"><?php echo $requisicion['area_nombre']; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Unidad de Medida</div>
                <div class="info-value"><?php echo $requisicion['unidad_nombre'] ?? 'N/A'; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Cotización</div>
                <div class="info-value">
                    <?php 
                    if (in_array($requisicion['estado'], ['cotizado', 'solicitar_pago', 'pago_solicitado', 'pagado', 'por_entregar', 'entregado'])) {
                        echo 'Requerida y procesada';
                    } else {
                        echo $requisicion['bRequiereCotizacion'] ? 'Requerida' : 'No requerida';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="descripcion">
            <div class="info-label">Descripción</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($requisicion['cDescripcion'])); ?></div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <small style="color: #6b7280;">
                ID: <?php echo $requisicion['id']; ?> | 
                Creado: <?php echo $requisicion['dFechaSolicitud']; ?>
            </small>
        </div>
    </div>

    <script>
    function marcarParaSolicitarPago(requisicionId) {
        if (!confirm('¿Estás seguro de marcar esta requisición para solicitar pago?')) return;
        
        fetch('<?= SERVICES_URL ?>/cambiar_estado.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + requisicionId + '&estado=solicitar_pago'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Requisición marcada para solicitar pago');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo actualizar'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error al procesar la solicitud');
        });
    }
    </script>