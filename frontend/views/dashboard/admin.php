<?php
// Verificar que el usuario esté logueado
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

// Solo admin puede acceder a este dashboard
if (!tieneRol('admin')) {
    header('Location: ../index.php?error=denegado');
    exit();
}

$usuario = $_SESSION['usuario'];

// Obtener estadísticas
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT estado, COUNT(*) as count FROM requisiciones WHERE lActivo = 1 GROUP BY estado");
$estadisticas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$pendientes = $estadisticas['pendiente'] ?? 0;
$cotizadas = $estadisticas['cotizado'] ?? 0;
$pago_solicitado = $estadisticas['pago_solicitado'] ?? 0;
$sin_completar = $db->query("SELECT COUNT(*) FROM requisiciones WHERE lActivo = 1 AND estado <> 'entregado'")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
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
            margin-bottom: 30px;
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

        .logout-btn {
            display: inline-block;
            margin-top: 30px;
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
            <h1 class="page-title">Dashboard Administrador</h1>
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

        <!-- Estadísticas rápidas -->
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Estadísticas</h2>
        <div class="grid grid-4 mb-20">
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $sin_completar; ?></div>
                <div class="stat-card-label">Sin completar</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $pendientes; ?></div>
                <div class="stat-card-label">Por cotizar</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $cotizadas; ?></div>
                <div class="stat-card-label">Cotizadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?php echo $pago_solicitado; ?></div>
                <div class="stat-card-label">Pago solicitado</div>
            </div>
        </div>

        <!-- Módulos del Sistema -->
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Módulos</h2>
        <div class="modules-grid">
            <a href="../requisiciones/crear.php" class="module-card">
                <div class="module-icon"><i class="bi bi-plus-circle"></i></div>
                <div class="module-title">Nueva Requisición</div>
                <div class="module-desc">Crear solicitud de materiales</div>
            </a>
            
            <a href="../requisiciones/listar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-list-check"></i></div>
                <div class="module-title">Requisiciones</div>
                <div class="module-desc">Ver todas las requisiciones</div>
            </a>
            
            <a href="../cotizaciones/aprobar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-check-lg"></i></div>
                <div class="module-title">Aprobar Cotizaciones</div>
                <div class="module-desc">Revisar y aprobar cotizaciones</div>
            </a>
            
            <a href="../cotizaciones/seguimiento.php" class="module-card">
                <div class="module-icon"><i class="bi bi-eye"></i></div>
                <div class="module-title">Seguimiento de Cotizaciones</div>
                <div class="module-desc">Todas las cotizaciones activas</div>
            </a>

            <a href="../pagos/solicitar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="module-title">Solicitar Pagos</div>
                <div class="module-desc">Pedir pagos de cotizaciones ganadoras</div>
            </a>
            
            <a href="../pagos/confirmar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-credit-card"></i></div>
                <div class="module-title">Confirmar Pagos</div>
                <div class="module-desc">Verificar y confirmar pagos</div>
            </a>
            
            <a href="../pagos/gestionar_entregas.php" class="module-card">
                <div class="module-icon"><i class="bi bi-box-seam"></i></div>
                <div class="module-title">Preparar Entregas</div>
                <div class="module-desc">Cotizaciones pagadas</div>
            </a>
            
            <a href="../pagos/finalizar_entregas.php" class="module-card">
                <div class="module-icon"><i class="bi bi-check-circle"></i></div>
                <div class="module-title">Finalizar Entregas</div>
                <div class="module-desc">Confirmar entregas</div>
            </a>
            
            <a href="../proveedores/listar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-building"></i></div>
                <div class="module-title">Proveedores</div>
                <div class="module-desc">Gestión de proveedores</div>
            </a>
            
            <a href="../usuarios/listar.php" class="module-card">
                <div class="module-icon"><i class="bi bi-people"></i></div>
                <div class="module-title">Usuarios</div>
                <div class="module-desc">Gestión de usuarios</div>
            </a>
        </div>

        <a href="../auth/logout.php" class="btn btn-danger logout-btn">Cerrar Sesión</a>
    </div>
</body>
</html>