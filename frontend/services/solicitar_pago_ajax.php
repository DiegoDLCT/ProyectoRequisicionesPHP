<?php
// Limpiar cualquier salida previa
if (ob_get_level()) ob_end_clean();
ob_start();

// Configurar headers antes de cualquier otra cosa
header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/../../backend/utils/auth.php';
require_once __DIR__ . '/../../backend/controllers/PagoController.php';

// Limpiar buffer y desactivar para evitar problemas
ob_end_clean();

try {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida.']);
        exit;
    }

    if (!tieneRol('jefe_mayor') && !tieneRol('admin')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para realizar esta acción.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_requisicion'])) {
        $pagoController = new PagoController();
        
        if ($pagoController->solicitarPago($_POST['id_requisicion'])) {
            echo json_encode(['success' => true, 'mensaje' => 'Pago solicitado correctamente.']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'mensaje' => 'Error al solicitar el pago.']);
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
