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
$requisicion = $requisicionController->obtenerPorId($id_requisicion);

if (!$requisicion) {
    header('Location: ../requisiciones/listar.php');
    exit();
}

$mensaje = '';
$error = '';

// Procesar envío del formulario
if ($_POST) {
    try {
        // Manejar subida de archivo
        $archivo_nombre = null;
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
            $archivo_nombre = 'cotizacion_' . $id_requisicion . '_' . time() . '.' . $extension;
            $ruta_destino = __DIR__ . '/../../../uploads/cotizaciones/' . $archivo_nombre;
            
            if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
                $archivo_nombre = 'cotizaciones/' . $archivo_nombre;
            } else {
                throw new Exception("Error al subir el archivo");
            }
        }

        $datos = [
            'id_requisicion' => $id_requisicion,
            'proveedor' => $_POST['proveedor'],
            'num_cotizacion' => $_POST['num_cotizacion'] ?? null,
            'monto' => $_POST['monto'] ?? null,
            'fecha_cotizacion' => $_POST['fecha_cotizacion'] ?? date('Y-m-d'),
            'archivo_uri' => $archivo_nombre
        ];
        
        $id_cotizacion = $cotizacionController->subirCotizacion($datos);
        
        if ($id_cotizacion) {
            $_SESSION['mensaje_exito'] = "Cotización subida correctamente.";
            // Redirigir a listar requisiciones
            header('Location: ../requisiciones/listar.php');
            exit();
        } else {
            $error = "Error al subir la cotización";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subir Cotización</title>
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
            max-width: 600px;
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
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
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
        <h1>Subir Cotización</h1>
        
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

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                  <label>Proveedor *</label>
                <input type="text" name="proveedor" value="<?php echo $_POST['proveedor'] ?? ''; ?>" 
                       placeholder="Nombre del proveedor" required>
            </div>

            <div class="form-group">
                  <label>Número de Cotización</label>
                <input type="text" name="num_cotizacion" value="<?php echo $_POST['num_cotizacion'] ?? ''; ?>" 
                       placeholder="Ej: COT-2024-001">
            </div>

            <div class="form-group">
                  <label>Monto</label>
                <input type="number" name="monto" step="0.01" value="<?php echo $_POST['monto'] ?? ''; ?>" 
                       placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Fecha de Cotización</label>
                <input type="date" name="fecha_cotizacion" value="<?php echo $_POST['fecha_cotizacion'] ?? date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label>Archivo (PDF)</label>
                <input type="file" name="archivo" accept=".pdf,.PDF">
                <small style="color: #6b7280;">Formatos aceptados: PDF (Máx. 5MB)</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Subir Cotización</button>
                <button type="button" class="btn btn-secondary" onclick="history.back()">Cancelar</button>
            </div>
        </form>
    </div>
</body>
</html>