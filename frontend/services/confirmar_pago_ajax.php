<?php
// Limpiar cualquier salida previa
if (ob_get_level()) ob_end_clean();
ob_start();

// Configurar headers antes de cualquier otra cosa
header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/../../backend/utils/auth.php';
require_once __DIR__ . '/../../backend/controllers/PagoController.php';

// Limpiar buffer
ob_end_clean();

try {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida.']);
        exit;
    }

    if (!tieneRol('contaduria')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para realizar esta acción.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_requisicion']) && isset($_POST['id_tipo_pago'])) {
        $id_requisicion = $_POST['id_requisicion'];
        $id_tipo_pago = $_POST['id_tipo_pago'];
        
        // Mapear el tipo de pago a método de pago
        $tipos_pago_map = [
            'Efectivo' => 'efectivo',
            'Transferencia' => 'transferencia',
            'Tarjeta de débito' => 'tarjeta'
        ];
        
        $pagoController = new PagoController();
        
        if ($pagoController->marcarPagado($id_requisicion, $id_tipo_pago)) {
            echo json_encode(['success' => true, 'mensaje' => 'Pago confirmado correctamente.']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'mensaje' => 'Error al confirmar el pago.']);
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
