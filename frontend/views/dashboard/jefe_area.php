<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

// Función para formatear estados
function formatearEstado($estado) {
    $estados = [
        'pendiente' => 'Pendiente',
        'cotizado' => 'Cotizado',
        'pago_solicitado' => 'Pago Solicitado',
        'pagado' => 'Pagado',
        'por_entregar' => 'Por Entregar',
        'entregado' => 'Entregado'
    ];
    return $estados[$estado] ?? ucfirst(str_replace('_', ' ', $estado));
}

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

if (!tieneRol('jefe_area') && !tieneRol('admin')) {
    header('Location: ../../index.php?error=denegado');
    exit();
}

$usuario = $_SESSION['usuario'];
$areaId = $usuario['idArea'] ?? null;

$db = (new Database())->getConnection();

// Obtener cotizaciones aprobadas del área EN CURSO
if ($areaId) {
    $stmtCot = $db->prepare("SELECT COUNT(*) as total FROM cotizaciones c 
                             JOIN requisiciones r ON c.idRequisicion = r.id 
                             WHERE c.bAprovada = 1 AND r.idArea = ? AND r.estado != 'entregado' AND r.lActivo = 1");
    $stmtCot->execute([$areaId]);
    $cotizacionesAprobadas = $stmtCot->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
}

// Solo mostrar estadísticas del ÁREA del jefe_area - por estado
$estadisticas = [];
$areaNombre = '';

if ($areaId) {
    // Obtener nombre del área
    $sqlArea = "SELECT cNombre FROM areas WHERE id = ? LIMIT 1";
    $stmtArea = $db->prepare($sqlArea);
    $stmtArea->execute([$areaId]);
    $area = $stmtArea->fetch(PDO::FETCH_ASSOC);
    $areaNombre = $area['cNombre'] ?? 'N/D';

    // Obtener requisiciones de su área por estado
    $sqlStats = "SELECT estado, COUNT(*) as cantidad FROM requisiciones 
                WHERE idArea = ? AND lActivo = 1 
                GROUP BY estado";
    $stmt = $db->prepare($sqlStats);
    $stmt->execute([$areaId]);
    $estadisticas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Jefe de Área</title>
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
        .stat-card.quoted { border-left-color: #3b82f6; }
        .stat-card.total { border-left-color: #10b981; }

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

        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            margin: 0;
            font-size: 18px;
            color: var(--dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--light-bg);
            padding: 12px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            color: var(--dark);
        }

        tr:hover {
            background: var(--light-bg);
        }

        .estado-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .estado-pendiente { background: #fef3c7; color: #92400e; }
        .estado-cotizado { background: #dbeafe; color: #1e40af; }
        .estado-pago_solicitado { background: #fecaca; color: #991b1b; }
        .estado-pagado { background: #dcfce7; color: #166534; }
        .estado-por_entregar { background: #f3e8ff; color: #6b21a8; }
        .estado-entregado { background: #d1fae5; color: #065f46; }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .empty i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 30px;
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
            <h1 class="page-title">Panel Jefe de Área</h1>
        </div>

        <!-- Información del usuario -->
        <div class="dashboard-header">
            <div class="user-card">
                <div>
                    <div class="user-field">Usuario</div>
                    <div class="user-value"><?php echo htmlspecialchars($usuario['cNombre']); ?></div>
                </div>
                <div>
                    <div class="user-field">Área</div>
                    <div class="user-value"><?php echo htmlspecialchars($areaNombre); ?></div>
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

        <!-- Estadísticas - Jefe Area ve TODO el proceso de sus requisiciones -->
        <div class="stats-grid">
            <?php 
            // Jefe Area ve: pendiente, cotizado, pago_solicitado, pagado, por_entregar
            $estadoLabels = [
                'pendiente' => ['label' => 'Pendiente', 'color' => 'pending'],
                'cotizado' => ['label' => 'Cotizado', 'color' => 'quoted'],
                'pago_solicitado' => ['label' => 'Pago Solicitado', 'color' => 'warning'],
                'pagado' => ['label' => 'Pagado', 'color' => 'success'],
                'por_entregar' => ['label' => 'Por Entregar', 'color' => 'info']
            ];
            
            foreach ($estadoLabels as $estado => $config): 
                $cantidad = $estadisticas[$estado] ?? 0;
            ?>
                <div class="stat-card <?php echo $config['color']; ?>">
                    <div class="stat-label"><?php echo $config['label']; ?></div>
                    <div class="stat-value"><?php echo $cantidad; ?></div>
                </div>
            <?php endforeach; ?>
            <!-- Cotizaciones Aprobadas -->
            <div class="stat-card" style="border-left-color: #3b82f6;">
                <div class="stat-label">Cotizaciones Aprobadas</div>
                <div class="stat-value"><?php echo $cotizacionesAprobadas; ?></div>
            </div>
        </div>

        <!-- Accesos Directos -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: var(--dark); margin-bottom: 20px; font-size: 18px;">
                <i class="bi bi-lightning-fill" style="color: #f59e0b;"></i> Accesos Rápidos
            </h3>
            
            <div class="shortcuts-grid">
                <!-- Ver Requisiciones -->
                <a href="../requisiciones/listar.php" class="shortcut-card primary">
                    <i class="bi bi-list-check shortcut-icon"></i>
                    <div class="shortcut-title">Ver Requisiciones</div>
                    <div class="shortcut-subtitle">Todas tus solicitudes</div>
                </a>

                <!-- Crear Requisición -->
                <a href="../requisiciones/crear.php" class="shortcut-card success">
                    <i class="bi bi-plus-circle shortcut-icon"></i>
                    <div class="shortcut-title">Nueva Requisición</div>
                    <div class="shortcut-subtitle">Crear solicitud</div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Verificar si hay estadísticas actualizadas desde requisicion creada
        document.addEventListener('DOMContentLoaded', function() {
            const estadisticasActualizadas = sessionStorage.getItem('estadisticas_actualizadas');
            
            if (estadisticasActualizadas) {
                const estadisticas = JSON.parse(estadisticasActualizadas);
                actualizarEstadisticasEnPantalla(estadisticas);
                sessionStorage.removeItem('estadisticas_actualizadas');
            }
        });

        function actualizarEstadisticasEnPantalla(estadisticas) {
            // Mapeo de estados
            const estadoLabels = {
                'pendiente': 0,
                'cotizado': 1,
                'pago_solicitado': 2,
                'pagado': 3,
                'por_entregar': 4,
                'entregado': 5,
                'cotizaciones_aprobadas': 6
            };

            const statCards = document.querySelectorAll('.stat-card');

            for (const [estado, cantidad] of Object.entries(estadisticas)) {
                if (estadoLabels.hasOwnProperty(estado) && estadoLabels[estado] < statCards.length) {
                    const cardIndex = estadoLabels[estado];
                    if (cardIndex < statCards.length) {
                        const statValue = statCards[cardIndex].querySelector('.stat-value');
                        if (statValue) {
                            statValue.textContent = cantidad;
                            // Animación de actualización
                            statValue.style.transition = 'all 0.3s ease';
                            statValue.style.color = '#10b981';
                            setTimeout(() => {
                                statValue.style.color = 'var(--dark)';
                            }, 1500);
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>
