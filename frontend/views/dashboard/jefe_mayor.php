<?php
// Verificar sesión y que sea jefe_mayor
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['cPuesto'] != 'jefe_mayor') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../../backend/controllers/CotizacionController.php';

$usuario = $_SESSION['usuario'];
$cotizacionController = new CotizacionController();

// Obtener requisiciones con cotizaciones reales
$requisicionesConCotizaciones = $cotizacionController->obtenerRequisicionesConCotizaciones();

// Procesar aprobación si se envió el formulario
if ($_POST && isset($_POST['id_cotizacion_aprobada'])) {
    $resultado = $cotizacionController->aprobarCotizacion($_POST['id_cotizacion_aprobada']);
    if ($resultado) {
        header('Location: jefe_mayor.php?mensaje=aprobada');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Jefe Mayor</title>
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
            margin: 0 auto;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 20px;
        }
        .user-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
        }
        .requisicion-card {
            background: #f8fafc;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #d97706;
        }
        .cotizaciones-list {
            margin: 20px 0;
        }
        .cotizacion-item {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .cotizacion-item:hover {
            border-color: #2563eb;
        }
        .cotizacion-seleccionada {
            background: #f0f9ff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #bfdbfe;
        }
        .cotizacion-aprobada {
            background: #d1fae5;
            border-color: #10b981;
        }
        .btn {
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-success {
            background: #059669;
        }
        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        .btn:hover:not(:disabled) {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .logout {
            color: #dc2626;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #dc2626;
            border-radius: 5px;
        }
        .monto {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        .proveedor {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .radio-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        .radio-container input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .archivo-link {
            color: #2563eb;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
        }
        .archivo-link:hover {
            text-decoration: underline;
        }
        .mensaje-exito {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>👑 Panel Jefe Mayor</h1>
        
        <div class="user-info">
            <strong>👤 Usuario:</strong> <?php echo $usuario['cNombre']; ?><br>
            <strong>📧 Email:</strong> <?php echo $usuario['cCorreo']; ?><br>
            <strong>💼 Puesto:</strong> <?php echo $usuario['cPuesto']; ?>
        </div>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'aprobada'): ?>
            <div class="mensaje-exito">
                ✅ Cotización aprobada exitosamente
            </div>
        <?php endif; ?>

        <h2>📋 Cotizaciones Pendientes de Aprobación</h2>

        <?php if (empty($requisicionesConCotizaciones)): ?>
            <div class="empty-state">
                <h3>✅ No hay cotizaciones pendientes</h3>
                <p>Todas las cotizaciones han sido aprobadas o no hay requisiciones en estado "cotizado"</p>
            </div>
        <?php else: ?>
            <?php foreach ($requisicionesConCotizaciones as $requisicion): ?>
                <div class="requisicion-card">
                    <h3>📄 <?php echo $requisicion['cFolio']; ?></h3>
                    <p><strong>📝 Descripción:</strong> <?php echo $requisicion['cDescripcion']; ?></p>
                    <p><strong>👤 Solicitante:</strong> <?php echo $requisicion['solicitante_nombre']; ?></p>
                    <p><strong>🏢 Área:</strong> <?php echo $requisicion['area_nombre']; ?></p>
                    
                    <form method="POST" class="cotizaciones-list" id="form-<?php echo $requisicion['id']; ?>">
                        <input type="hidden" name="id_requisicion" value="<?php echo $requisicion['id']; ?>">
                        
                        <h4>🏷️ Selecciona la cotización ganadora:</h4>
                        
                        <?php if (empty($requisicion['cotizaciones'])): ?>
                            <p style="color: #6b7280; font-style: italic;">No hay cotizaciones registradas</p>
                        <?php else: ?>
                            <?php 
                            $tiene_aprobada = false;
                            foreach ($requisicion['cotizaciones'] as $cotizacion): 
                                if ($cotizacion['bAprovada']) $tiene_aprobada = true;
                            ?>
                                <div class="cotizacion-item <?php echo $cotizacion['bAprovada'] ? 'cotizacion-aprobada' : ''; ?>" 
                                     onclick="document.getElementById('cotizacion-<?php echo $cotizacion['id']; ?>').checked = true; updateSelection(<?php echo $requisicion['id']; ?>)">
                                    <div class="radio-container">
                                        <input type="radio" 
                                               id="cotizacion-<?php echo $cotizacion['id']; ?>" 
                                               name="id_cotizacion_aprobada" 
                                               value="<?php echo $cotizacion['id']; ?>"
                                               <?php echo $cotizacion['bAprovada'] ? 'checked disabled' : ''; ?>
                                               onchange="updateSelection(<?php echo $requisicion['id']; ?>)">
                                        <label for="cotizacion-<?php echo $cotizacion['id']; ?>" style="cursor: pointer; margin: 0;">
                                            <div class="proveedor">🏢 <?php echo $cotizacion['cProveedor']; ?></div>
                                        </label>
                                    </div>
                                    
                                    <p class="monto">💰 $<?php echo number_format($cotizacion['deMonto'], 2); ?> MXN</p>
                                    <p>📅 Fecha: <?php echo $cotizacion['dFechaCotizacion']; ?></p>
                                    
                                    <?php if ($cotizacion['cNumCotizacion']): ?>
                                        <p>📋 Número: <?php echo $cotizacion['cNumCotizacion']; ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($cotizacion['cArchivoURI']): ?>
                                        <a href="../../../uploads/<?php echo $cotizacion['cArchivoURI']; ?>" 
                                           class="archivo-link" target="_blank">
                                           📎 Ver PDF de cotización
                                        </a>
                                    <?php else: ?>
                                        <p style="color: #ef4444;">⚠️ No hay archivo PDF adjunto</p>
                                    <?php endif; ?>
                                    
                                    <?php if ($cotizacion['bAprovada']): ?>
                                        <p style="color: #059669; font-weight: bold; margin-top: 10px;">✅ COTIZACIÓN APROBADA</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (!$tiene_aprobada): ?>
                                <div style="margin-top: 20px; text-align: center;">
                                    <button type="submit" class="btn btn-success" id="btn-aprobar-<?php echo $requisicion['id']; ?>" disabled>
                                        ✅ Aprobar Cotización Seleccionada
                                    </button>
                                    <p id="mensaje-seleccion-<?php echo $requisicion['id']; ?>" style="color: #6b7280; margin-top: 10px;">
                                        👆 Selecciona una cotización para habilitar la aprobación
                                    </p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="../auth/logout.php" class="logout">🚪 Cerrar Sesión</a>
    </div>

    <script>
        function updateSelection(requisicionId) {
            const form = document.getElementById('form-' + requisicionId);
            const radio = form.querySelector('input[name="id_cotizacion_aprobada"]:checked');
            const btn = document.getElementById('btn-aprobar-' + requisicionId);
            const mensaje = document.getElementById('mensaje-seleccion-' + requisicionId);
            
            // Habilitar/deshabilitar botón
            btn.disabled = !radio;
            
            // Actualizar mensaje
            if (radio) {
                mensaje.innerHTML = '✅ Listo para aprobar la cotización seleccionada';
                mensaje.style.color = '#059669';
            } else {
                mensaje.innerHTML = '👆 Selecciona una cotización para habilitar la aprobación';
                mensaje.style.color = '#6b7280';
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
                updateSelection(<?php echo $requisicion['id']; ?>);
            <?php endforeach; ?>
        });
    </script>
</body>
</html>