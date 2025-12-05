<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/auth.php';

if (!isset($_SESSION['usuario']) || !tieneRol('admin')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = $_POST['id'] ?? null;
$estado = $_POST['estado'] ?? null;

if (!$id || !$estado) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

$db = (new Database())->getConnection();
$stmt = $db->prepare("UPDATE requisiciones SET estado = :estado WHERE id = :id AND lActivo = 1");
$stmt->bindParam(':estado', $estado);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
}
?>
