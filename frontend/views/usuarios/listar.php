<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/controllers/UsuarioController.php';

// Solo admins pueden ver usuarios
if (!tieneRol('admin')) {
    header('Location: ../index.php?error=denegado');
    exit();
}

$usuarioController = new UsuarioController();
$usuarios = $usuarioController->listarUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        body { background:#f8fafc; margin:0; font-family: 'Segoe UI', Arial, sans-serif; }
        .page { max-width:1100px; margin:0 auto; padding:24px; }
        h1 { margin:0 0 16px; color:#0f172a; }
        .table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; box-shadow:0 10px 25px rgba(15,23,42,0.05); }
        .table th, .table td { padding:12px 14px; text-align:left; border-bottom:1px solid #f1f5f9; }
        .table th { background:#f8fafc; font-size:14px; color:#475569; font-weight:600; }
        .table tr:last-child td { border-bottom:none; }
        .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
        .badge-admin { background:#dbeafe; color:#1d4ed8; }
        .badge-jefe-area { background:#dcfce7; color:#15803d; }
        .badge-jefe-mayor { background:#fef9c3; color:#a16207; }
        .pill { background:#e2e8f0; color:#334155; padding:4px 10px; border-radius:999px; font-size:12px; }
        .top-actions { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        a.link { color:#2563eb; text-decoration:none; font-weight:600; }
        a.link:hover { text-decoration:underline; }
        .empty { padding:20px; text-align:center; color:#6b7280; background:#fff; border:1px solid #e5e7eb; border-radius:8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="top-actions">
        <h1>Usuarios</h1>
        <a class="link" href="../dashboard/admin.php">← Volver al dashboard</a>
    </div>

    <?php if (empty($usuarios)): ?>
        <div class="empty">No hay usuarios registrados.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Puesto</th>
                    <th>Área</th>
                    <th>Activo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['cNombre']); ?></td>
                        <td><?php echo htmlspecialchars($u['cCorreo']); ?></td>
                        <td>
                            <?php
                                $puesto = strtolower($u['cPuesto'] ?? '');
                                $label = $puesto;
                                $cls = 'badge';
                                if ($puesto === 'admin') { $cls .= ' badge-admin'; $label = 'Admin'; }
                                elseif ($puesto === 'jefe_area') { $cls .= ' badge-jefe-area'; $label = 'Jefe de área'; }
                                elseif ($puesto === 'jefe_mayor') { $cls .= ' badge-jefe-mayor'; $label = 'Jefe mayor'; }
                            ?>
                            <span class="<?php echo $cls; ?>"><?php echo htmlspecialchars($label); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($u['area_nombre'] ?? ''); ?></td>
                        <td><span class="pill">Activo</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../shared/footer.php'; ?>
</body>
</html>
