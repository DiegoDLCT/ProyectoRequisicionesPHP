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
    
    if ($pagoController->marcarPorEntregar($_POST['id_requisicion'])) {
        $_SESSION['mensaje_exito'] = 'Requisición marcada como lista para entregar.';
    } else {
        $_SESSION['mensaje_error'] = 'Error al actualizar el estado.';
    }
    header('Location: ../views/pagos/gestionar_entregas.php');
} else {
    $_SESSION['mensaje_error'] = 'Solicitud inválida.';
    header('Location: ../views/pagos/gestionar_entregas.php');
}
exit;
?>