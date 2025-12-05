<?php
// Verificar sesión
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}
require_once dirname(__DIR__, 3) . '/backend/controllers/RequisicionController.php';
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';

$usuario = $_SESSION['usuario'];
$requisicionController = new RequisicionController();

// Obtener requisiciones según rol
if (tieneRol('admin') || tieneRol('jefe_mayor')) {
    // Admin y jefe mayor ven todo
    $requisiciones = $requisicionController->obtenerTodas();
} else {
    // Usuarios normales y futuros jefe_area ven solo sus propias requisiciones
    $requisiciones = $requisicionController->obtenerRequisicionesUsuario($usuario['id']);
}

$esUsuarioNormal = !tieneRol('admin') && !tieneRol('jefe_mayor') && !tieneRol('contaduria');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Requisiciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .table th {
            background: #f8fafc;
            font-weight: bold;
            color: #374151;
        }
        .table tr:hover {
            background: #f9fafb;
        }
        .estado {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .estado-pendiente { background: #fef3c7; color: #d97706; }
        .estado-cotizado { background: #dbeafe; color: #1d4ed8; }
        .estado-solicitar_pago { background: #fed7aa; color: #c2410c; }
        .estado-aprobado { background: #d1fae5; color: #065f46; }
        .estado-pago_solicitado { background: #e0e7ff; color: #4338ca; }
        .estado-pagado { background: #f3e8ff; color: #7c3aed; }
        .estado-por_entregar { background: #fed7aa; color: #c2410c; }
        .estado-entregado { background: #dcfce7; color: #166534; }
        .acciones a {
            margin-right: 10px;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .ver { background: #dbeafe; color: #1d4ed8; }
        .editar { background: #fef3c7; color: #d97706; }
        .cotizar { background: #d1fae5; color: #065f46; }
        .ver-cotizaciones { background: #e0e7ff; color: #3730a3; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .header-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            Lista de Requisiciones
            <div class="header-buttons">
                <button id="scrollToTop" class="btn" title="Volver al inicio" style="background: #10b981;">
                    <i class="bi bi-arrow-left"></i> Atrás
                </button>
                <a href="crear.php" class="btn">Nueva Requisición</a>
            </div>
        </h1>

        <?php if (!empty($_SESSION['mensaje_exito'])): ?>
            <div style="background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;padding:12px;border-radius:5px;margin-bottom:20px;">
                <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($requisiciones)): ?>
            <div class="empty-state">
                <h3>No hay requisiciones registradas</h3>
                <p>Crea la primera requisición del sistema</p>
                <a href="crear.php" class="btn">Crear Primera Requisición</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <?php if (!$esUsuarioNormal): ?><th>Solicitante</th><?php endif; ?>
                        <th>Ubicación</th>
                        <th>Unidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requisiciones as $req): ?>
                    <tr>
                        <td><strong><?php echo $req['cFolio']; ?></strong></td>
                        <td><?php echo $req['dFechaSolicitud']; ?></td>
                        <?php if (!$esUsuarioNormal): ?><td><?php echo $req['solicitante_nombre']; ?></td><?php endif; ?>
                        <td><?php echo $req['cObraUbicacion'] ?: '--'; ?></td>
                        <td><?php echo $req['unidad_nombre'] ?? 'N/A'; ?></td>
                        <td>
                            <span class="estado estado-<?php echo $req['estado']; ?>">
                                <?php 
                                $estados = [
                                    'pendiente' => 'Sin Cotizar',
                                    'cotizado' => 'Cotizado', 
                                    'solicitar_pago' => 'Solicitar Pago',
                                    'pago_solicitado' => 'Pago Solicitado',
                                    'pagado' => 'Pagado',
                                    'por_entregar' => 'Por Entregar',
                                    'entregado' => 'Entregado'
                                ];
                                echo $estados[$req['estado']] ?? $req['estado'];
                                ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <a href="ver.php?id=<?php echo $req['id']; ?>" class="ver">Detalle</a>
                            <?php if ($req['estado'] == 'pendiente'): ?>
                                <a href="../cotizaciones/subir.php?id_requisicion=<?php echo $req['id']; ?>" class="cotizar">Cotizar</a>
                            <?php endif; ?>
                            <?php if ($req['estado'] == 'cotizado'): ?>
                                <a href="../cotizaciones/seguimiento.php?id_requisicion=<?php echo $req['id']; ?>" class="ver-cotizaciones">Ver Cotiz.</a>
                                <a href="../cotizaciones/subir.php?id_requisicion=<?php echo $req['id']; ?>" class="cotizar">Agregar Cotiz.</a>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
                    
            </table>
            
            <div style="margin-top: 20px; color: #6b7280; font-size: 14px;">
                Total: <?php echo count($requisiciones); ?> requisiciones
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="../../index.php" class="btn" style="background: #6b7280;">← Volver al Dashboard</a>
        </div>
    </div>

    <script>
        // Botón atrás - volver a página anterior
        document.getElementById('scrollToTop').addEventListener('click', function() {
            history.back();
        });
    </script>
</body>
</html>