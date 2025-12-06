<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

if (!isset($_SESSION['usuario']) || !tieneRol('Contaduria')) {
    header('Location: ../auth/login.php');
    exit();
}

$usuario = $_SESSION['usuario'];
$db = (new Database())->getConnection();

// Obtener mensajes de sesión
$mensaje_exito = $_SESSION['mensaje_exito'] ?? null;
$mensaje_error = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);

// Estadísticas rápidas
$pago_solicitado = $db->query("SELECT COUNT(*) FROM requisiciones WHERE lActivo = 1 AND estado = 'pago_solicitado'")->fetchColumn();
$pagado = $db->query("SELECT COUNT(*) FROM requisiciones WHERE lActivo = 1 AND estado = 'pagado'")->fetchColumn();

// Folder del proyecto para fetch dinámico
$uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$projectFolder = $uriParts[0] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Contaduría</title>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Contaduría</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        .dashboard-header {
            background: white;
            border-bottom: 1px solid var(--border);
            margin-bottom: 30px;
            padding: 20px;
            border-radius: var(--radius-lg);
        }

        .user-card {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .user-field {
            font-size: 13px;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .user-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        @media (max-width: 768px) {
            .user-card {
                grid-template-columns: 1fr;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Dashboard Contaduría</h1>
            <a href="../../index.php" class="btn btn-secondary">Volver</a>
        </div>

        <!-- Información del usuario -->
        <div class="dashboard-header">
            <div class="user-card">
                <div>
                    <div class="user-field">Usuario</div>
                    <div class="user-value"><?php echo htmlspecialchars($usuario['cNombre']); ?></div>
                </div>
                <div>
                    <div class="user-field">Email</div>
                    <div class="user-value"><?php echo htmlspecialchars($usuario['cCorreo']); ?></div>
                </div>
                <div>
                    <div class="user-field">Rol</div>
                    <div class="user-value"><?php echo htmlspecialchars($usuario['cPuesto']); ?></div>
                </div>
            </div>
        </div>

        <!-- Mensajes de sesión -->
        <?php if ($mensaje_exito): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje_exito); ?></div>
        <?php endif; ?>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($mensaje_error); ?></div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Estadísticas</h2>
        <div class="grid grid-2 mb-20">
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $pago_solicitado; ?></div>
                <div class="stat-card-label">Pago solicitado</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $pagado; ?></div>
                <div class="stat-card-label">Pagado</div>
            </div>
        </div>

        <!-- Módulos -->
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Módulos</h2>
        <div class="modules-grid">
            <a href="../pagos/confirmar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-credit-card"></i></div>
                <div class="module-title">Confirmar Pagos</div>
                <div class="module-desc">Verificar y confirmar pagos</div>
            </a>
            <a href="../proveedores/listar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-building"></i></div>
                <div class="module-title">Proveedores</div>
                <div class="module-desc">Gestión de proveedores</div>
            </a>
        </div>

        <a href="../auth/logout.php" class="btn btn-danger" style="display: inline-block; margin-top: 30px;">Cerrar Sesión</a>
    </div>
</body>
</html>
</html>
