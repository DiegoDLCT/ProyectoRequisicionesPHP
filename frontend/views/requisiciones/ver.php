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
        .estado-aprobado { background: #d1fae5; color: #065f46; }
        .estado-pagado { background: #f3e8ff; color: #7c3aed; }
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
                            'aprobado' => 'Aprobado',
                            'pagado' => 'Pagado',
                            'entregado' => 'Entregado'
                        ];
                        echo $estados[$requisicion['estado']] ?? $requisicion['estado'];
                        ?>
                    </span>
                </div>
            </div>
            <div>
                <a href="../../index.php" class="btn btn-secondary">Volver</a>
                <?php if ($requisicion['estado'] == 'pendiente'): ?>
                    <!-- Editar no implementado; volver a la lista en su lugar -->
                    <a href="listar.php" class="btn">Editar</a>
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
                <div class="info-label">Cotización</div>
                <div class="info-value">
                    <?php echo $requisicion['bRequiereCotizacion'] ? 'Requerida' : 'No requerida'; ?>
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
</body>
</html>