<?php
session_start();
$base_path = dirname(__FILE__, 2);
require_once $base_path . '/config/database.php';
require_once $base_path . '/utils/auth.php';

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = 'Sesión no válida';
    header('Location: /ProyectoPHP/frontend/index.php');
    exit();
}

// Validar rol
if (!tieneRol('Contaduria')) {
    $_SESSION['mensaje_error'] = 'Acceso denegado';
    header('Location: /ProyectoPHP/frontend/index.php');
    exit();
}

// Validar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_error'] = 'Solicitud no válida';
    header('Location: /ProyectoPHP/frontend/views/dashboard/contaduria.php');
    exit();
}

// Obtener ID de requisición y tipo de pago
$id_requisicion = $_POST['id_requisicion'] ?? null;
$id_tipo_pago = $_POST['id_tipo_pago'] ?? null;

if (!$id_requisicion || !$id_tipo_pago) {
    $_SESSION['mensaje_error'] = 'Datos incompletos';
    header('Location: /ProyectoPHP/frontend/views/dashboard/contaduria.php');
    exit();
}

try {
    $db = (new Database())->getConnection();
    
    // Obtener datos de la requisición para COMPRAS
    $query_req = "SELECT r.id, r.cDescripcion, c.deMonto, t.CNombre
                  FROM requisiciones r
                  LEFT JOIN cotizaciones c ON c.idRequisicion = r.id AND c.bAprovada = 1
                  LEFT JOIN tipos_pago t ON t.id = :id_tipo_pago
                  WHERE r.id = :id";
    $stmt_req = $db->prepare($query_req);
    $stmt_req->bindParam(':id', $id_requisicion);
    $stmt_req->bindParam(':id_tipo_pago', $id_tipo_pago);
    $stmt_req->execute();
    $requisicion_data = $stmt_req->fetch(PDO::FETCH_ASSOC);
    
    if (!$requisicion_data) {
        $_SESSION['mensaje_error'] = 'No se encontraron datos de la requisición';
        throw new Exception('Requisición no encontrada');
    }
    
    // Cambiar estado de pago_solicitado a pagado
    $query_update = "UPDATE requisiciones SET estado = 'pagado' WHERE id = :id AND estado = 'pago_solicitado'";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':id', $id_requisicion);
    
    if (!$stmt_update->execute()) {
        $_SESSION['mensaje_error'] = 'Error al confirmar el pago';
        throw new Exception('Error al actualizar estado');
    }
    
    // Crear registro en tabla COMPRAS
    $query_compra = "INSERT INTO compras (idRequisicion, idTipoPago, cFormapago, deMontoTotal, dFechaCompra)
                     VALUES (:idRequisicion, :idTipoPago, :cFormapago, :deMontoTotal, NOW())";
    $stmt_compra = $db->prepare($query_compra);
    $stmt_compra->bindParam(':idRequisicion', $id_requisicion);
    $stmt_compra->bindParam(':idTipoPago', $id_tipo_pago);
    $stmt_compra->bindParam(':cFormapago', $requisicion_data['CNombre']);
    $stmt_compra->bindParam(':deMontoTotal', $requisicion_data['deMonto']);
    
    if ($stmt_compra->execute()) {
        $_SESSION['mensaje_exito'] = 'Pago confirmado exitosamente y registrado en COMPRAS';
    } else {
        $_SESSION['mensaje_error'] = 'Pago confirmado pero error al registrar en COMPRAS';
    }
    
} catch (Exception $e) {
    $_SESSION['mensaje_error'] = 'Error: ' . $e->getMessage();
}

header('Location: /ProyectoPHP/frontend/views/dashboard/contaduria.php');
exit();
?>
