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

// Estadísticas rápidas
$stmt = $db->query("SELECT estado, COUNT(*) as count FROM requisiciones WHERE lActivo = 1 GROUP BY estado");
$estadisticas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$pendientes = $estadisticas['pendiente'] ?? 0;
$cotizadas = $estadisticas['cotizado'] ?? 0;
$aprobadas = $estadisticas['solicitar_pago'] ?? 0;
$pendientes_aprobacion = count($requisicionesConCotizaciones);
$sin_completar = $db->query("SELECT COUNT(*) FROM requisiciones WHERE lActivo = 1 AND estado <> 'entregado'")->fetchColumn();

// Folder del proyecto para fetch dinámico
$uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$projectFolder = $uriParts[0] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Jefe Mayor</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background: #f5f5f5;
        }
        .dashboard {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin: 0 auto 40px;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 20px;
        }
        h2 { color: #1f2937; margin: 20px 0 10px; }
        .user-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
        }
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0 30px;
        }
        .module-card {
            background: #f8fafc;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #1f2937;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .module-card:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: translateY(-2px);
        }
        .module-title { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .module-desc { font-size: 14px; color: #6b7280; }
        .module-card:hover .module-desc { color: #e5e7eb; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 10px 0 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number { font-size: 24px; font-weight: bold; color: #2563eb; }
        .stat-label { font-size: 14px; color: #6b7280; }
        .section-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            margin-top: 10px;
        }
        .requisicion-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .cotizacion-item {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin: 12px 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .cotizacion-item:hover { border-color: #2563eb; }
        .radio-container { display: flex; align-items: center; gap: 10px; }
        .monto { font-size: 18px; font-weight: bold; color: #059669; margin: 6px 0; }
        .proveedor { font-size: 16px; font-weight: bold; color: #1f2937; }
        .preview-container { height: 220px; border: 1px solid #eee; border-radius: 6px; overflow: hidden; margin-top: 8px; }
        .preview-container iframe, .preview-container img { width: 100%; height: 100%; object-fit: contain; }
        .btn { background: #2563eb; color: #fff; padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: all 0.2s; }
        .btn-success { background: #059669; }
        .btn:disabled { background: #9ca3af; cursor: not-allowed; }
        .mensaje-seleccion { color: #6b7280; margin-top: 6px; }
        .empty-state { text-align: center; color: #6b7280; padding: 20px; }
        .logout {
            color: #dc2626;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            border: 1px solid #dc2626;
            border-radius: 5px;
        }
        .logout:hover { background: #dc2626; color: white; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Panel de Jefe Mayor</h1>
        <div class="user-info">
            <strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['cNombre']); ?><br>
            <strong>Email:</strong> <?php echo htmlspecialchars($usuario['cCorreo']); ?><br>
            <strong>Puesto:</strong> <?php echo htmlspecialchars($usuario['cPuesto']); ?>
        </div>

        <div class="stats">
            <div class="stat-card"><div class="stat-number"><?php echo $sin_completar; ?></div><div class="stat-label">Requisiciones sin completar</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $pendientes; ?></div><div class="stat-label">Sin Cotizar</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $cotizadas; ?></div><div class="stat-label">Cotizadas</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $pendientes_aprobacion; ?></div><div class="stat-label">Pendientes de Aprobación</div></div>
        </div>

        <h2>Módulos</h2>
        <div class="modules-grid">
            <a href="../cotizaciones/aprobar.php" class="module-card">
                <div class="module-title">Aprobar Cotizaciones</div>
                <div class="module-desc">Revisar y aprobar cotizaciones pendientes</div>
            </a>
            <a href="../pagos/solicitar.php" class="module-card">
                <div class="module-title">Solicitar Pagos</div>
                <div class="module-desc">Pedir pagos de cotizaciones ganadoras</div>
            </a>
            <a href="../requisiciones/listar.php" class="module-card">
                <div class="module-title">Ver Requisiciones</div>
                <div class="module-desc">Consulta y seguimiento</div>
            </a>
            <a href="../cotizaciones/seguimiento.php" class="module-card">
                <div class="module-title">Seguimiento de Cotizaciones</div>
                <div class="module-desc">Todas las cotizaciones activas</div>
            </a>
        </div>

        <a href="../auth/logout.php" class="logout">Cerrar Sesión</a>
    </div>

    <script>
    function aprobarCotizacionAjax(event, requisicionId) {
        event.preventDefault();
        const btn = document.getElementById('btn-aprobar-' + requisicionId);
        const hiddenInput = document.getElementById('hidden-cotizacion-' + requisicionId);
        if (!hiddenInput.value) return;
        btn.disabled = true;
        fetch('/' + '<?php echo $projectFolder; ?>' + '/backend/services/aprobar_service.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_cotizacion_aprobada=' + encodeURIComponent(hiddenInput.value)
        })
        .then(r => r.text())
        .then(() => {
            document.getElementById('success-message').style.display = 'block';
            document.getElementById('success-message').textContent = 'Cotización aprobada exitosamente';
            const card = btn.closest('.requisicion-card');
            if (card) card.remove();
        })
        .catch(() => {
            document.getElementById('success-message').style.display = 'block';
            document.getElementById('success-message').textContent = 'Error al aprobar la cotización';
        });
    }

    function seleccionarCotizacion(cotizacionId, requisicionId) {
        const radio = document.getElementById('cotizacion-' + cotizacionId);
        if (radio && !radio.disabled) {
            radio.checked = true;
            actualizarSeleccion(requisicionId);
        }
    }

        function actualizarSeleccion(requisicionId) {
            const form = document.getElementById('form-' + requisicionId);
            if (!form) return;
            
            const radio = form.querySelector('input[name="id_cotizacion_aprobada_' + requisicionId + '"]:checked');
            const btn = document.getElementById('btn-aprobar-' + requisicionId);
            const mensaje = document.getElementById('mensaje-seleccion-' + requisicionId);
            const hiddenInput = document.getElementById('hidden-cotizacion-' + requisicionId);
            
            // Actualizar hidden input y botón
            if (radio && hiddenInput) {
                hiddenInput.value = radio.value;
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
            
            // Actualizar mensaje
            if (mensaje) {
                if (radio) {
                    mensaje.textContent = 'Listo para aprobar la cotización seleccionada';
                    mensaje.classList.add('activo');
                } else {
                    mensaje.textContent = 'Selecciona una cotización para habilitar la aprobación';
                    mensaje.classList.remove('activo');
                }
            }
            
            // Actualizar estilos visuales
            const items = form.querySelectorAll('.cotizacion-item');
            items.forEach(item => {
                item.classList.remove('cotizacion-seleccionada');
                const radioInItem = item.querySelector('input[type="radio"]');
                if (radioInItem && radioInItem.checked) {
                    item.classList.add('cotizacion-seleccionada');
                }
            });
        }
        
        // Inicializar estado de los formularios
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($requisicionesConCotizaciones as $requisicion): ?>
                actualizarSeleccion(<?php echo $requisicion['id']; ?>);
            <?php endforeach; ?>
        });
    </script>   
</body>
</html>