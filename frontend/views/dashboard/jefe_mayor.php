<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
require_once dirname(__DIR__, 3) . '/backend/controllers/AprobacionController.php';

if (!isset($_SESSION['usuario']) || !tieneRol('jefe_mayor')) {
    header('Location: ../auth/login.php');
    exit();
}

$usuario = $_SESSION['usuario'];
$db = (new Database())->getConnection();
$aprobacionController = new AprobacionController();
$requisicionesConCotizaciones = $aprobacionController->obtenerTodasLasRequisicionesConCotizaciones();

// Estadísticas - Solo lo que ve jefe_mayor (cotizadas, pagos solicitados, por entregar)
$stmt = $db->query("SELECT estado, COUNT(*) as count FROM requisiciones WHERE lActivo = 1 GROUP BY estado");
$estadisticas_datos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Función para formatear rol
function formatearRol($rol) {
    $roles = [
        'admin' => 'Administrador',
        'jefe_mayor' => 'Jefe Mayor',
        'contaduria' => 'Contaduría',
        'jefe_area' => 'Jefe de Área',
        'solicitante' => 'Solicitante'
    ];
    return $roles[$rol] ?? ucfirst(str_replace('_', ' ', $rol));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Jefe Mayor</title>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Jefe Mayor</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary);
        }

        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.quoted { border-left-color: #3b82f6; }
        .stat-card.warning { border-left-color: #ef4444; }
        .stat-card.success { border-left-color: #10b981; }
        .stat-card.info { border-left-color: #8b5cf6; }

        .stat-label {
            font-size: 13px;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark);
        }

        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .shortcut-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            border: 2px solid transparent;
            cursor: pointer;
        }

        .shortcut-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        .shortcut-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            color: var(--primary);
        }

        .shortcut-card.primary .shortcut-icon { color: #3b82f6; }
        .shortcut-card.success .shortcut-icon { color: #10b981; }
        .shortcut-card.warning .shortcut-icon { color: #f59e0b; }
        .shortcut-card.danger .shortcut-icon { color: #ef4444; }
        .shortcut-card.info .shortcut-icon { color: #8b5cf6; }

        .shortcut-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .shortcut-subtitle {
            font-size: 12px;
            color: var(--gray);
        }

        @media (max-width: 768px) {
            .user-card {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .shortcuts-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .shortcuts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Panel Jefe Mayor</h1>
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
                    <div class="user-value"><?php echo htmlspecialchars(formatearRol($usuario['cPuesto'])); ?></div>
                </div>
            </div>
            <a href="../auth/logout.php" class="btn btn-sm" style="background: #ef4444;">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>

        <!-- Estadísticas - Solo lo que ve jefe_mayor -->
        <div class="stats-grid">
            <?php 
            // Jefe Mayor solo ve: cotizado, pago_solicitado, por_entregar
            $estadoLabels = [
                'cotizado' => ['label' => 'Por Aprobar', 'color' => 'quoted'],
                'pago_solicitado' => ['label' => 'Pago Solicitado', 'color' => 'warning'],
                'por_entregar' => ['label' => 'Por Entregar', 'color' => 'info']
            ];
            
            foreach ($estadoLabels as $estado => $config): 
                $cantidad = $estadisticas_datos[$estado] ?? 0;
            ?>
                <div class="stat-card <?php echo $config['color']; ?>">
                    <div class="stat-label"><?php echo $config['label']; ?></div>
                    <div class="stat-value"><?php echo $cantidad; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Accesos Directos -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 18px;">
                <i class="bi bi-lightning-fill" style="color: #f59e0b;"></i> Accesos Rápidos
            </h3>
            
            <div class="shortcuts-grid">
                <!-- Aprobar Cotizaciones -->
                <a href="../cotizaciones/aprobar.php" class="shortcut-card success">
                    <i class="bi bi-file-earmark-check shortcut-icon"></i>
                    <div class="shortcut-title">Aprobar Cotizaciones</div>
                    <div class="shortcut-subtitle">Revisar presupuestos</div>
                </a>

                <!-- Solicitar Pagos -->
                <a href="../pagos/solicitar.php" class="shortcut-card warning">
                    <i class="bi bi-cash-coin shortcut-icon"></i>
                    <div class="shortcut-title">Solicitar Pagos</div>
                    <div class="shortcut-subtitle">Gestionar pagos</div>
                </a>

                <!-- Ver Requisiciones -->
                <a href="../requisiciones/listar.php" class="shortcut-card primary">
                    <i class="bi bi-list-check shortcut-icon"></i>
                    <div class="shortcut-title">Requisiciones</div>
                    <div class="shortcut-subtitle">Ver solicitudes</div>
                </a>

                <!-- Seguimiento de Cotizaciones -->
                <a href="../cotizaciones/seguimiento.php" class="shortcut-card info">
                    <i class="bi bi-eye shortcut-icon"></i>
                    <div class="shortcut-title">Seguimiento</div>
                    <div class="shortcut-subtitle">Todas las cotizaciones</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>