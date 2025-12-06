<?php
// Verificar sesión
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Verificar rol (solo admin puede subir cotizaciones)
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
if (!tieneRol('admin')) {
    header('Location: ../index.php?error=denegado');
    exit();
}

if (!isset($_GET['id_requisicion'])) {
    header('Location: ../requisiciones/listar.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/backend/controllers/CotizacionController.php';
require_once dirname(__DIR__, 3) . '/backend/controllers/RequisicionController.php';

$usuario = $_SESSION['usuario'];
$cotizacionController = new CotizacionController();
$requisicionController = new RequisicionController();

$id_requisicion = $_GET['id_requisicion'];
$id_cotizacion = $_GET['id_cotizacion'] ?? null; // Para editar

$requisicion = $requisicionController->obtenerPorId($id_requisicion);
$cotizacion_actual = null;

if (!$requisicion) {
    header('Location: ../requisiciones/listar.php');
    exit();
}

// Si es edición, obtener la cotización actual
if ($id_cotizacion) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT * FROM cotizaciones WHERE id = ?");
    $stmt->execute([$id_cotizacion]);
    $cotizacion_actual = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cotizacion_actual) {
        header('Location: ./seguimiento.php');
        exit();
    }
}

$mensaje = '';
$error = '';

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar campos requeridos
        if (empty($_POST['proveedor'])) {
            throw new Exception("El proveedor es requerido");
        }
        if (empty($_POST['dias_entrega'])) {
            throw new Exception("El tiempo de entrega es requerido");
        }
        
        // Manejar subida de archivo
        $archivo_nombre = null;
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            // Validar que sea PDF
            $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
            if ($extension !== 'pdf') {
                throw new Exception("Solo se aceptan archivos PDF");
            }
            
            // Validar tamaño (máx 5MB)
            if ($_FILES['archivo']['size'] > 5 * 1024 * 1024) {
                throw new Exception("El archivo no puede exceder 5MB");
            }
            
            $archivo_nombre = 'cotizacion_' . $id_requisicion . '_' . time() . '.pdf';
            $ruta_destino = __DIR__ . '/../../../uploads/cotizaciones/' . $archivo_nombre;
            
            // Verificar que la carpeta existe
            if (!is_dir(__DIR__ . '/../../../uploads/cotizaciones/')) {
                mkdir(__DIR__ . '/../../../uploads/cotizaciones/', 0777, true);
            }
            
            if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
                throw new Exception("Error al subir el archivo. Verifica los permisos de la carpeta uploads/");
            }
            $archivo_nombre = 'cotizaciones/' . $archivo_nombre;
        }

        $datos = [
            'id_requisicion' => $id_requisicion,
            'proveedor' => $_POST['proveedor'],
            'num_cotizacion' => $_POST['num_cotizacion'] ?? null,
            'monto' => $_POST['monto'] ?? null,
            'fecha_cotizacion' => $_POST['fecha_cotizacion'] ?? date('Y-m-d'),
            'dias_entrega' => $_POST['dias_entrega'] ?? null,
            'archivo_uri' => $archivo_nombre
        ];
        
        // Si es edición, actualizar; si no, crear
        if ($id_cotizacion) {
            // Actualizar cotización existente
            $db = (new Database())->getConnection();
            $sql = "UPDATE cotizaciones SET cProveedor = ?, cNumCotizacion = ?, deMonto = ?, 
                    dFechaCotizacion = ?, iDiasEntrega = ?";
            $params = [$datos['proveedor'], $datos['num_cotizacion'], $datos['monto'], 
                      $datos['fecha_cotizacion'], $datos['dias_entrega']];
            
            // Si hay nuevo archivo, actualizar también
            if ($archivo_nombre) {
                $sql .= ", cArchivourl = ?";
                $params[] = $archivo_nombre;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id_cotizacion;
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['mensaje_exito'] = "Cotización actualizada correctamente.";
        } else {
            // Crear nueva cotización
            $id_cotizacion = $cotizacionController->subirCotizacion($datos);
            
            if (!$id_cotizacion) {
                throw new Exception("Error al subir la cotización");
            }
            
            $_SESSION['mensaje_exito'] = "Cotización subida correctamente.";
        }
        
        // Redirigir a seguimiento
        header('Location: ./seguimiento.php?id_requisicion=' . $id_requisicion);
        exit();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $id_cotizacion ? 'Editar Cotización' : 'Subir Cotización'; ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 0 auto;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 20px;
        }
        .requisicion-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2563eb;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group:has(.btn) {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #374151;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn {
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            margin-right: 10px;
            white-space: nowrap;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #6b7280;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .mensaje {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $id_cotizacion ? 'Editar Cotización' : 'Subir Cotización'; ?></h1>
        
        <?php if ($mensaje): ?>
            <div class="mensaje success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mensaje error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="requisicion-info">
            <strong>Requisición:</strong> <?php echo $requisicion['cFolio']; ?><br>
            <strong>Descripción:</strong> <?php echo $requisicion['cDescripcion']; ?><br>
            <strong>Área:</strong> <?php echo $requisicion['area_nombre']; ?>
        </div>

        <form id="formCotizacion" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                  <label>Proveedor *</label>
                <input type="text" name="proveedor" value="<?php echo ($cotizacion_actual && isset($cotizacion_actual['cProveedor'])) ? htmlspecialchars($cotizacion_actual['cProveedor']) : (isset($_POST['proveedor']) ? htmlspecialchars($_POST['proveedor']) : ''); ?>" 
                       placeholder="Nombre del proveedor" required>
            </div>

            <div class="form-group">
                  <label>Número de Cotización</label>
                <input type="text" name="num_cotizacion" value="<?php echo ($cotizacion_actual && isset($cotizacion_actual['cNumCotizacion'])) ? htmlspecialchars($cotizacion_actual['cNumCotizacion']) : (isset($_POST['num_cotizacion']) ? htmlspecialchars($_POST['num_cotizacion']) : ''); ?>" 
                       placeholder="Ej: COT-2024-001">
            </div>

            <div class="form-group">
                  <label>Monto</label>
                <input type="number" name="monto" step="0.01" value="<?php echo ($cotizacion_actual && isset($cotizacion_actual['deMonto'])) ? htmlspecialchars($cotizacion_actual['deMonto']) : (isset($_POST['monto']) ? htmlspecialchars($_POST['monto']) : ''); ?>" 
                       placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Fecha de Cotización</label>
                <input type="date" name="fecha_cotizacion" value="<?php echo ($cotizacion_actual && isset($cotizacion_actual['dFechaCotizacion'])) ? htmlspecialchars($cotizacion_actual['dFechaCotizacion']) : (isset($_POST['fecha_cotizacion']) ? htmlspecialchars($_POST['fecha_cotizacion']) : date('Y-m-d')); ?>">
            </div>

            <div class="form-group">
                <label>Tiempo de Entrega *</label>
                <input type="text" name="dias_entrega" value="<?php echo ($cotizacion_actual && isset($cotizacion_actual['iDiasEntrega'])) ? htmlspecialchars($cotizacion_actual['iDiasEntrega']) : (isset($_POST['dias_entrega']) ? htmlspecialchars($_POST['dias_entrega']) : ''); ?>" 
                       placeholder="Ej: Inmediata, 24 horas, 3 días, 1 semana" required>
            </div>

            <div class="form-group">
                <label>Archivo (PDF)</label>
                <input type="file" name="archivo">
                <small style="color: #6b7280;">Formatos aceptados: PDF (Máx. 5MB)</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Subir Cotización</button>
                <button type="button" class="btn btn-secondary" onclick="history.back()">Cancelar</button>
            </div>
            
            <div id="mensaje-resultado" style="margin-top: 15px;"></div>
        </form>
    </div>
</body>
</html>