<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');
ob_end_clean();

session_start();

try {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'mensaje' => 'No autorizado']);
        exit();
    }

    require_once dirname(__DIR__, 2) . '/backend/controllers/RequisicionController.php';

    $usuario = $_SESSION['usuario'];
    $requisicionController = new RequisicionController();

    // Validar datos
    if (empty($_POST['id_area']) || empty($_POST['descripcion']) || empty($_POST['id_unidad']) || empty($_POST['obra_ubicacion'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensaje' => 'Faltan campos requeridos']);
        exit();
    }

    $datos = [
        'id_area' => $_POST['id_area'],
        'descripcion' => $_POST['descripcion'],
        'id_unidad' => $_POST['id_unidad'],
        'maquina' => $_POST['maquina'] ?? null,
        'obra_ubicacion' => $_POST['obra_ubicacion']
    ];

    $id_requisicion = $requisicionController->crearRequisicion($datos, $usuario['id']);

    if ($id_requisicion) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'mensaje' => 'Requisición creada correctamente',
            'id' => $id_requisicion
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'mensaje' => 'Error al crear la requisición']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
