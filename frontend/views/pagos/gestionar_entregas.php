<?php
session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (!tieneRol('admin')) {
    header('Location: ../../index.php?error=denegado');
    exit;
}

$db = (new Database())->getConnection();

// Obtener requisiciones en estado pagado
$query = "SELECT r.*, 
          u.cNombre as solicitante_nombre, 
          c.cProveedor as proveedor_nombre,
          c.deMonto as cotizacion_monto
          FROM requisiciones r
          LEFT JOIN usuarios u ON u.id = r.idSolicitante
          LEFT JOIN cotizaciones c ON c.idRequisicion = r.id AND c.bAprovada = 1
          WHERE r.lActivo = 1 AND r.estado = 'pagado'
          ORDER BY r.dFechaSolicitud DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$requisiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje_exito = $_SESSION['mensaje_exito'] ?? null;
$mensaje_error = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Entregas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f5f7; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header h1 { font-size: 28px; color: #1d1d1f; margin-bottom: 5px; }
        .header p { color: #86868b; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 20px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; transition: all 0.2s; }
        .btn:hover { background: #0077ed; transform: translateY(-1px); }
        .btn-secondary { background: #86868b; }
        .btn-secondary:hover { background: #6e6e73; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f5f5f7; }
        th { padding: 15px; text-align: left; font-weight: 600; color: #1d1d1f; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid #f5f5f7; color: #1d1d1f; }
        tr:hover { background: #fafafa; }
        .empty { text-align: center; padding: 60px 20px; color: #86868b; }
        .empty i { font-size: 48px; margin-bottom: 15px; display: block; }
        .monto { font-weight: 600; color: #10b981; }
        .actions { display: flex; gap: 10px; }
        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-warning { background: #f59e0b; }
        .btn-warning:hover { background: #d97706; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestionar Entregas</h1>
            <p>Cotizaciones pagadas pendientes de preparación y entrega</p>
        </div>

        <?php if ($mensaje_exito): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
        <?php endif; ?>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($mensaje_error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (empty($requisiciones)): ?>
                <div class="empty">
                    <i class="bi bi-inbox"></i>
                    <p>No hay cotizaciones pagadas pendientes de entregar</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Solicitante</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Fecha Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requisiciones as $req): ?>
                            <tr>
                                <td><?= htmlspecialchars($req['cFolio']) ?></td>
                                <td><?= htmlspecialchars($req['solicitante_nombre']) ?></td>
                                <td><?= htmlspecialchars($req['cDescripcion']) ?></td>
                                <td><?= htmlspecialchars($req['proveedor_nombre']) ?></td>
                                <td class="monto">$<?= number_format($req['cotizacion_monto'], 2) ?></td>
                                <td><?= date('d/m/Y', strtotime($req['dFechaSolicitud'])) ?></td>
                                <td class="actions">
                                    <a href="../requisiciones/ver.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <form method="POST" action="<?= SERVICES_URL ?>/marcar_por_entregar_service.php" style="display: inline;">
                                        <input type="hidden" name="id_requisicion" value="<?= $req['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('¿Marcar como lista para entregar?')">
                                            <i class="bi bi-box-seam"></i> Por Entregar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <a href="../../index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
