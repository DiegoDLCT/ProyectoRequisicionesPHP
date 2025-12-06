<?php
// Limpiar cualquier salida previa
if (ob_get_level()) ob_end_clean();
ob_start();

// Configurar headers antes de cualquier otra cosa
header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/../../backend/utils/auth.php';
require_once __DIR__ . '/../../backend/controllers/CotizacionController.php';
require_once __DIR__ . '/../../backend/controllers/RequisicionController.php';
require_once __DIR__ . '/../../backend/config/database.php';

// Limpiar buffer
ob_end_clean();

try {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida.']);
        exit;
    }

    if (!tieneRol('admin')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para realizar esta acción.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_requisicion = $_POST['id_requisicion'] ?? null;
        $id_cotizacion = (!empty($_POST['id_cotizacion']) && $_POST['id_cotizacion'] !== 'null') ? $_POST['id_cotizacion'] : null;
        
        if (!$id_requisicion) {
            http_response_code(400);
            echo json_encode(['success' => false, 'mensaje' => 'Requisición no especificada.']);
            exit;
        }

        // Manejar subida de archivo
        $archivo_nombre = null;
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
            $archivo_nombre = 'cotizacion_' . $id_requisicion . '_' . time() . '.' . $extension;
            $ruta_destino = __DIR__ . '/../../uploads/cotizaciones/' . $archivo_nombre;
            
            if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
                throw new Exception("Error al subir el archivo");
            }
            $archivo_nombre = 'cotizaciones/' . $archivo_nombre;
        }

        $datos = [
            'id_requisicion' => $id_requisicion,
            'proveedor' => $_POST['proveedor'] ?? null,
            'num_cotizacion' => $_POST['num_cotizacion'] ?? null,
            'monto' => $_POST['monto'] ?? null,
            'fecha_cotizacion' => $_POST['fecha_cotizacion'] ?? date('Y-m-d'),
            'dias_entrega' => $_POST['dias_entrega'] ?? null,
            'archivo_uri' => $archivo_nombre
        ];

        $db = (new Database())->getConnection();
        
        if ($id_cotizacion) {
            // Actualizar cotización existente
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
            
            echo json_encode(['success' => true, 'mensaje' => 'Cotización actualizada correctamente.']);
        } else {
            // Crear nueva cotización
            $cotizacionController = new CotizacionController();
            $id_cotizacion_nueva = $cotizacionController->subirCotizacion($datos);
            
            if (!$id_cotizacion_nueva) {
                throw new Exception("Error al subir la cotización");
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Cotización subida correctamente.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensaje' => 'Solicitud inválida.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
exit;
?>
