<?php
session_start();
require_once __DIR__ . '/../../backend/utils/auth.php';
require_once __DIR__ . '/../../backend/controllers/PagoController.php';

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = 'Sesión no válida.';
    header('Location: ../index.php?error=denegado');
    exit;
}

if (!tieneRol('admin')) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para realizar esta acción.';
    header('Location: ../index.php?error=denegado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_requisicion'])) {
    $pagoController = new PagoController();
    
    if ($pagoController->marcarEntregado($_POST['id_requisicion'])) {
        $_SESSION['mensaje_exito'] = 'Requisición marcada como entregada. Ciclo finalizado.';
    } else {
        $_SESSION['mensaje_error'] = 'Error al finalizar la requisición.';
    }
    header('Location: ../views/pagos/finalizar_entregas.php');
} else {
    $_SESSION['mensaje_error'] = 'Solicitud inválida.';
    header('Location: ../views/pagos/finalizar_entregas.php');
}
exit;
?>