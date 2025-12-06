<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/controllers/AprobacionController.php';
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';

if (!tieneRol('jefe_mayor') && !tieneRol('admin')) {
    header("Location: ../auth/login.php");
    exit;
}

$aprobacionController = new AprobacionController();
$requisiciones = $aprobacionController->obtenerAprobacionesPendientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Cotizaciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f5f7; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header h1 { font-size: 28px; color: #1d1d1f; margin-bottom: 5px; }
        .header p { color: #86868b; font-size: 14px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-info { background: #e0f2fe; color: #0369a1; border-left: 4px solid #0ea5e9; }
        .requisicion-section { background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .requisicion-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f5f5f7; }
        .requisicion-header h3 { font-size: 18px; color: #1d1d1f; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .estado-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #dbeafe; color: #1d4ed8; }
        .requisicion-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .info-item { color: #6b7280; }
        .info-label { font-weight: 600; color: #1d1d1f; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .cotizaciones-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
        .cotizacion-card { background: #f9fafb; padding: 20px; border-radius: 8px; border: 2px solid #e5e7eb; cursor: pointer; transition: all 0.2s ease; }
        .cotizacion-card:hover { border-color: #0071e3; box-shadow: 0 4px 12px rgba(0,113,227,0.1); }
        .cotizacion-card.seleccionada { background: #eff6ff; border-color: #0071e3; box-shadow: 0 0 0 3px rgba(0,113,227,0.1); }
        .radio-container { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .radio-container input[type="radio"] { width: 18px; height: 18px; cursor: pointer; }
        .cotizacion-card h5 { font-size: 16px; color: #1d1d1f; margin-bottom: 10px; }
        .cotizacion-details { font-size: 14px; margin: 10px 0; color: #6b7280; }
        .monto { font-size: 18px; font-weight: bold; color: #10b981; margin: 8px 0; }
        .preview-container { height: 300px; margin: 15px 0; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; }
        .preview-container iframe, .preview-container img { width: 100%; height: 100%; object-fit: contain; }
        .btn { display: inline-block; padding: 10px 20px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; transition: all 0.2s; }
        .btn:hover { background: #0077ed; transform: translateY(-1px); }
        .btn:disabled { background: #d1d5db; cursor: not-allowed; }
        .btn-success { background: #10b981; }
        .btn-success:hover:not(:disabled) { background: #059669; }
        .btn-secondary { background: #86868b; }
        .btn-secondary:hover { background: #6e6e73; }
        .empty { text-align: center; padding: 60px 20px; color: #86868b; }
        .empty i { font-size: 48px; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Aprobar Cotizaciones</h1>
            <p>Revisa y aprueba las cotizaciones de requisiciones en estado cotizado</p>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert" style="background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444;"><i class="bi bi-exclamation-circle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($requisiciones)): ?>
            <div class="empty">
                <i class="bi bi-inbox"></i>
                <p>No hay cotizaciones pendientes de aprobación</p>
            </div>
        <?php else: ?>
            <?php foreach($requisiciones as $requisicion): ?>
                <div class="requisicion-section">
                    <div class="requisicion-header">
                        <h3>
                            <i class="bi bi-file-earmark"></i> Requisición <?= htmlspecialchars($requisicion['cFolio']) ?>
                            <span class="estado-badge">Cotizado</span>
                        </h3>
                    </div>
                    <div class="requisicion-info">
                        <div class="info-item">
                            <div class="info-label">Descripción</div>
                            <div><?= htmlspecialchars($requisicion['cDescripcion']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Solicitante</div>
                            <div><?= htmlspecialchars($requisicion['solicitante']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Total de Cotizaciones</div>
                            <div><?= $requisicion['total_cotizaciones'] ?></div>
                        </div>
                    </div>

                    <?php
                    $cotizaciones = $aprobacionController->obtenerCotizacionesPorRequisicion($requisicion['id']);
                    ?>

                    <div class="cotizaciones-grid" id="cotizaciones-grupo-<?php echo $requisicion['id']; ?>">
                        <?php foreach($cotizaciones as $idx => $cotizacion): ?>
                            <div class="cotizacion-card" id="card-<?php echo $cotizacion['id']; ?>" onclick="seleccionarCotizacion(<?php echo $cotizacion['id']; ?>, <?php echo $requisicion['id']; ?>)">
                                <div class="radio-container">
                                    <input type="radio" id="cotizacion-<?php echo $cotizacion['id']; ?>" name="id_cotizacion_aprobada_<?php echo $requisicion['id']; ?>" value="<?php echo $cotizacion['id']; ?>" onchange="actualizarSeleccion(<?php echo $requisicion['id']; ?>)">
                                    <label for="cotizacion-<?php echo $cotizacion['id']; ?>" style="cursor: pointer; margin: 0; font-weight: 600;">
                                        <?php echo htmlspecialchars($cotizacion['cProveedor']) ?>
                                    </label>
                                </div>
                                <div class="monto">$<?php echo number_format($cotizacion['deMonto'], 2) ?> MXN</div>
                                <div class="cotizacion-details">
                                    <div><strong>N° Cotización:</strong> <?php echo htmlspecialchars($cotizacion['cNumcotizacion']) ?></div>
                                    <div><strong>Fecha:</strong> <?php echo isset($cotizacion['dFechacotizacion']) ? htmlspecialchars($cotizacion['dFechacotizacion']) : 'N/A' ?></div>
                                    <div><strong>Entrega:</strong> <?php echo isset($cotizacion['iDiasEntrega']) && $cotizacion['iDiasEntrega'] ? htmlspecialchars($cotizacion['iDiasEntrega']) : 'N/A' ?></div>
                                </div>
                                
                                <div class="preview-container">
                                    <?php if (!empty($cotizacion['cArchivourl'])): ?>
                                        <?php
                                        $extension = strtolower(pathinfo($cotizacion['cArchivourl'], PATHINFO_EXTENSION));
                                        $rutaArchivo = '/ProyectoPHP/uploads/' . htmlspecialchars($cotizacion['cArchivourl']);
                                        if ($extension === 'pdf'): ?>
                                            <iframe src="<?php echo $rutaArchivo ?>"></iframe>
                                        <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                            <img src="<?php echo $rutaArchivo ?>" alt="Cotización <?php echo htmlspecialchars($cotizacion['cProveedor']) ?>">
                                        <?php else: ?>
                                            <p style="padding: 20px; text-align: center;"><a href="<?php echo $rutaArchivo ?>" target="_blank" class="btn" style="display: inline-block;">Descargar archivo</a></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p style="padding: 20px; text-align: center; color: #ef4444;">No hay archivo adjunto</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form onsubmit="aprobarCotizacionAjax(event, <?php echo $requisicion['id']; ?>)" style="margin-top: 20px; text-align: center;">
                        <input type="hidden" name="id_cotizacion_aprobada" id="hidden-cotizacion-<?php echo $requisicion['id']; ?>">
                        <button type="submit" class="btn btn-success" id="btn-aprobar-<?php echo $requisicion['id']; ?>" disabled>
                            <i class="bi bi-check-circle"></i> Aprobar Cotización Seleccionada
                        </button>
                        <p id="mensaje-seleccion-<?php echo $requisicion['id']; ?>" style="color: #6b7280; margin-top: 10px;">Selecciona una cotización para habilitar la aprobación</p>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <button onclick="history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver Atrás
            </button>
            <a href="../../index.php" class="btn" style="background: #2563eb; margin-left: 10px;">
                <i class="bi bi-house"></i> Inicio
            </a>
        </div>
    </div>

    <script>
    function seleccionarCotizacion(cotizacionId, requisicionId) {
        const radio = document.getElementById('cotizacion-' + cotizacionId);
        const grupoCards = document.getElementById('cotizaciones-grupo-' + requisicionId);
        if (radio && !radio.disabled) {
            radio.checked = true;
            grupoCards.querySelectorAll('.cotizacion-card').forEach(card => {
                card.classList.remove('seleccionada');
            });
            document.getElementById('card-' + cotizacionId).classList.add('seleccionada');
            actualizarSeleccion(requisicionId);
        }
    }

    function actualizarSeleccion(requisicionId) {
        const radios = document.getElementsByName('id_cotizacion_aprobada_' + requisicionId);
        const hidden = document.getElementById('hidden-cotizacion-' + requisicionId);
        const btn = document.getElementById('btn-aprobar-' + requisicionId);
        const mensaje = document.getElementById('mensaje-seleccion-' + requisicionId);
        let seleccionado = '';
        radios.forEach(r => { if (r.checked) seleccionado = r.value; });
        hidden.value = seleccionado;
        btn.disabled = !seleccionado;
        mensaje.textContent = seleccionado ? 'Listo para aprobar la cotización seleccionada' : 'Selecciona una cotización para habilitar la aprobación';
    }

    function aprobarCotizacionAjax(event, requisicionId) {
        event.preventDefault();
        const btn = document.getElementById('btn-aprobar-' + requisicionId);
        const hiddenInput = document.getElementById('hidden-cotizacion-' + requisicionId);
        
        console.log('Requisición ID:', requisicionId);
        console.log('Hidden input value:', hiddenInput.value);
        console.log('Button disabled:', btn.disabled);
        
        if (!hiddenInput.value) {
            alert('Por favor selecciona una cotización');
            return;
        }
        
        btn.disabled = true;
        
        fetch('<?= SERVICES_URL ?>/aprobar_service.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_cotizacion_aprobada=' + encodeURIComponent(hiddenInput.value)
        })
        .then(r => r.text())
        .then(data => {
            console.log('Response:', data);
            const section = btn.closest('.requisicion-section');
            if (section) {
                section.style.opacity = '0.6';
                section.style.pointerEvents = 'none';
                section.insertAdjacentHTML('beforeend', '<div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:8px;text-align:center;font-weight:bold;margin-top:10px;">Cotización aprobada exitosamente</div>');
            }
            setTimeout(() => location.reload(), 1500);
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error al aprobar la cotización');
            btn.disabled = false;
        });
    }
    </script>
</body>
</html>