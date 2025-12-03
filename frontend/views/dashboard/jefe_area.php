<?php
// Dashboard para Jefe de Área
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

if (!tieneRol('jefe_area') && !tieneRol('admin')) {
    header('Location: ../../index.php?error=denegado');
    exit();
}

$usuario = $_SESSION['usuario'];
$areaId = $usuario['idArea'] ?? null;

$db = (new Database())->getConnection();

$estadisticas = ['total' => 0, 'pendientes' => 0, 'aprobadas' => 0];
$requisiciones = [];

if ($areaId) {
    $sqlStats = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobadas
        FROM requisiciones WHERE idArea = ? AND lActivo = 1";
    $stmt = $db->prepare($sqlStats);
    $stmt->execute([$areaId]);
    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

    $sqlList = "SELECT r.*, u.cNombre as solicitante_nombre, a.cNombre as area_nombre
        FROM requisiciones r
        LEFT JOIN usuarios u ON r.idSolicitante = u.id
        LEFT JOIN areas a ON r.idArea = a.id
        WHERE r.idArea = ? AND r.lActivo = 1
        ORDER BY r.dFechaSolicitud DESC";
    $stmt2 = $db->prepare($sqlList);
    $stmt2->execute([$areaId]);
    $requisiciones = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Jefe de Área</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 20px; }
        .dashboard { max-width: 1100px; margin: 0 auto; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .stats { display:flex; gap:12px; margin-bottom:20px; }
        .stat { background:white; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.04); flex:1 }
        .requis-table { background:white; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.04); }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px; border-bottom:1px solid #eef2f7; text-align:left; }
        th { background:#fbfcfe; }
        .empty { text-align:center; color:#6b7280; padding:30px; }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div>
                <h1>Panel Jefe de Área</h1>
                <div>Usuario: <?php echo htmlspecialchars($usuario['cNombre']); ?> — Área: <?php echo htmlspecialchars($usuario['idArea'] ?? 'N/D'); ?></div>
            </div>
            <div>
                <a href="../../index.php" style="text-decoration:none;color:#2563eb;">Ir al inicio</a>
                &nbsp;|&nbsp;
                <a href="../auth/logout.php" style="text-decoration:none;color:#ef4444;">Cerrar sesión</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat">
                <div style="font-size:24px;font-weight:700;"><?php echo $estadisticas['total'] ?? 0; ?></div>
                <div style="color:#6b7280;">Total requisiciones</div>
            </div>
            <div class="stat">
                <div style="font-size:24px;font-weight:700;"><?php echo $estadisticas['pendientes'] ?? 0; ?></div>
                <div style="color:#6b7280;">Pendientes</div>
            </div>
            <div class="stat">
                <div style="font-size:24px;font-weight:700;"><?php echo $estadisticas['aprobadas'] ?? 0; ?></div>
                <div style="color:#6b7280;">Aprobadas</div>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <a href="../requisiciones/crear.php" class="btn" style="background:#2563eb;color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none;">Crear Requisición</a>
            <a href="../requisiciones/listar.php" class="btn" style="margin-left:8px;background:#6b7280;color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none;">Mis Requisiciones</a>
        </div>

        <div class="requis-table">
            <h3>Requisiciones del área</h3>
            <?php if (empty($areaId)): ?>
                <div class="empty">No tienes un área asignada. Contacta al administrador.</div>
            <?php elseif (empty($requisiciones)): ?>
                <div class="empty">No hay requisiciones para esta área.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Solicitante</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requisiciones as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['cFolio']); ?></td>
                            <td><?php echo htmlspecialchars($r['dFechaSolicitud']); ?></td>
                            <td><?php echo htmlspecialchars($r['solicitante_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($r['cObraUbicacion'] ?: '--'); ?></td>
                            <td><?php echo htmlspecialchars($r['estado']); ?></td>
                            <td><a href="../../views/requisiciones/ver.php?id=<?php echo $r['id']; ?>">Ver</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
