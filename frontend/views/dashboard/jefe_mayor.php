<?php
// Verificar sesión y que sea jefe_mayor
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['cPuesto'] != 'jefe_mayor') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../../backend/controllers/CotizacionController.php';

$usuario = $_SESSION['usuario'];
//$cotizacionController = new CotizacionController();

// SOLUCIÓN - Obtener requisiciones con cotizaciones pendientes (VERSIÓN PDO)
require_once __DIR__ . '/../../../backend/config/database.php';

function obtenerTodasLasRequisicionesConCotizaciones() {
    $db = (new Database())->getConnection();
    
    $sql = "SELECT 
                r.id, r.cFolio, r.cDescripcion, r.estado,
                u.cNombre as solicitante_nombre,
                a.cNombre as area_nombre
            FROM requisiciones r
            JOIN usuarios u ON r.idSolicitante = u.id
            JOIN areas a ON r.idArea = a.id
            WHERE r.id IN (SELECT DISTINCT idRequisicion FROM cotizaciones WHERE bAprovada = 0)
            ORDER BY r.dFechaSolicitud DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $requisiciones = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reqId = $row['id'];
        
        // Obtener cotizaciones para esta requisición
        $sqlCotizaciones = "SELECT 
                            id, idRequisicion, cProveedor, cNumcotizacion, deMonto, 
                            dFechacotizacion, cArchivourl, bAprovada 
                            FROM cotizaciones 
                            WHERE idRequisicion = ? AND bAprovada = 0
                            ORDER BY deMonto ASC";
        $stmtCotizaciones = $db->prepare($sqlCotizaciones);
        $stmtCotizaciones->execute([$reqId]);
        $cotizaciones = $stmtCotizaciones->fetchAll(PDO::FETCH_ASSOC);
        
        $requisiciones[] = [
            'id' => $row['id'],
            'cFolio' => $row['cFolio'],
            'cDescripcion' => $row['cDescripcion'],
            'estado' => $row['estado'],
            'solicitante_nombre' => $row['solicitante_nombre'],
            'area_nombre' => $row['area_nombre'],
            'cotizaciones' => $cotizaciones
        ];
    }
    
         return $requisiciones;
        $requisicionesConCotizaciones = obtenerTodasLasRequisicionesConCotizaciones();
}
// Procesar aprobación si se envió el formulario
//if ($_POST && isset($_POST['id_cotizacion_aprobada'])) {
  //  $resultado = $cotizacionController->aprobarCotizacion($_POST['id_cotizacion_aprobada']);
    //if ($resultado) {
      //  header('Location: jefe_mayor.php?mensaje=aprobada');
        //exit();
    //}
//}
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
            cursor: pointer;
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
            transition: all 0.3s ease;
        }
        .btn-success {
            background: #059669;
        }
        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none !important;
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
        .mensaje-seleccion {
            color: #6b7280;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        .mensaje-seleccion.activo {
            color: #059669;
            font-weight: bold;
        }
        .preview-container {
            height: 300px;
            margin: 10px 0;
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        .preview-container iframe, 
        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>👑 Panel Jefe Mayor</h1>
        
        <div class="user-info">
            <strong>👤 Usuario:</strong> <?php echo htmlspecialchars($usuario['cNombre']); ?><br>
            <strong>📧 Email:</strong> <?php echo htmlspecialchars($usuario['cCorreo']); ?><br>
            <strong>💼 Puesto:</strong> <?php echo htmlspecialchars($usuario['cPuesto']); ?>
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
                <p>Todas las cotizaciones han sido aprobadas</p>
            </div>
        <?php else: ?>
            <?php foreach ($requisicionesConCotizaciones as $requisicion): ?>
                <div class="requisicion-card">
                    <h3>📄 <?php echo htmlspecialchars($requisicion['cFolio']); ?></h3>
                    <p><strong>📝 Descripción:</strong> <?php echo htmlspecialchars($requisicion['cDescripcion']); ?></p>
                    <p><strong>👤 Solicitante:</strong> <?php echo htmlspecialchars($requisicion['solicitante_nombre']); ?></p>
                    <p><strong>🏢 Área:</strong> <?php echo htmlspecialchars($requisicion['area_nombre']); ?></p>
                    <p><strong>📊 Estado:</strong> <?php echo htmlspecialchars($requisicion['estado']); ?></p>
                    
                    <div class="cotizaciones-list" id="form-<?php echo $requisicion['id']; ?>">
    <input type="hidden" name="id_requisicion" value="<?php echo $requisicion['id']; ?>">
    
    <h4>🏷️ Selecciona la cotización ganadora:</h4>
    
    <?php if (empty($requisicion['cotizaciones'])): ?>
        <p style="color: #6b7280; font-style: italic;">No hay cotizaciones pendientes</p>
    <?php else: ?>
        <?php foreach ($requisicion['cotizaciones'] as $cotizacion): ?>
            <div class="cotizacion-item" 
                 onclick="seleccionarCotizacion(<?php echo $cotizacion['id']; ?>, <?php echo $requisicion['id']; ?>)">
                <div class="radio-container">
                    <input type="radio" 
                           id="cotizacion-<?php echo $cotizacion['id']; ?>" 
                           name="id_cotizacion_aprobada_<?php echo $requisicion['id']; ?>" 
                           value="<?php echo $cotizacion['id']; ?>"
                           onchange="actualizarSeleccion(<?php echo $requisicion['id']; ?>)">
                    <label for="cotizacion-<?php echo $cotizacion['id']; ?>" style="cursor: pointer; margin: 0;">
                        <div class="proveedor">🏢 <?php echo htmlspecialchars($cotizacion['cProveedor']); ?></div>
                    </label>
                </div>
                
                <p class="monto">💰 $<?php echo number_format($cotizacion['deMonto'], 2); ?> MXN</p>
                <p>📅 Fecha: <?php echo htmlspecialchars($cotizacion['dFechacotizacion']); ?></p>
                
                <?php if ($cotizacion['cNumcotizacion']): ?>
                    <p>📋 Número: <?php echo htmlspecialchars($cotizacion['cNumcotizacion']); ?></p>
                <?php endif; ?>
                
                <?php if ($cotizacion['cArchivourl']): ?>
                    <div class="preview-container">
                        <?php
                        $extension = strtolower(pathinfo($cotizacion['cArchivourl'], PATHINFO_EXTENSION));
                        if (in_array($extension, ['pdf'])): 
                        ?>
                            <iframe src="../../../uploads/cotizaciones/<?php echo htmlspecialchars($cotizacion['cArchivourl']); ?>"></iframe>
                        <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="../../../uploads/cotizaciones/<?php echo htmlspecialchars($cotizacion['cArchivourl']); ?>" 
                                 alt="Cotización <?php echo htmlspecialchars($cotizacion['cProveedor']); ?>">
                        <?php else: ?>
                            <p>📎 <a href="../../../uploads/cotizaciones/<?php echo htmlspecialchars($cotizacion['cArchivourl']); ?>" 
                                   target="_blank" class="archivo-link">Descargar archivo</a></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #ef4444;">⚠️ No hay archivo adjunto</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <form method="POST" action=""/ProyectoPHP/backend/services/aprobar_service.php" style="margin-top: 20px; text-align: center;">
            <input type="hidden" name="id_cotizacion_aprobada" id="hidden-cotizacion-<?php echo $requisicion['id']; ?>">
            <button type="submit" class="btn btn-success" id="btn-aprobar-<?php echo $requisicion['id']; ?>" disabled>
                ✅ Aprobar Cotización Seleccionada
            </button>
            <p id="mensaje-seleccion-<?php echo $requisicion['id']; ?>" class="mensaje-seleccion">
                👆 Selecciona una cotización para habilitar la aprobación
            </p>
        </form>
    <?php endif; ?>
</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="../auth/logout.php" class="logout">🚪 Cerrar Sesión</a>
    </div>

<script>
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
                mensaje.textContent = '✅ Listo para aprobar la cotización seleccionada';
                mensaje.classList.add('activo');
            } else {
                mensaje.textContent = '👆 Selecciona una cotización para habilitar la aprobación';
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