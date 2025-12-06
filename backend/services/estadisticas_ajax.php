<?php
// Servicio AJAX para obtener estadísticas actualizadas
session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: application/json');

$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'];
$db = (new Database())->getConnection();

$estadisticas = [];

try {
    if ($rol === 'admin') {
        // Admin: todas las requisiciones
        $stmt = $db->query("SELECT estado, COUNT(*) as count FROM requisiciones WHERE lActivo = 1 GROUP BY estado");
        $estadisticas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Cotizaciones aprobadas de requisiciones en curso
        $stmtCot = $db->prepare("SELECT COUNT(*) as total FROM cotizaciones c 
                                 JOIN requisiciones r ON c.idRequisicion = r.id 
                                 WHERE c.bAprovada = 1 AND r.estado != 'entregado' AND r.lActivo = 1");
        $stmtCot->execute();
        $estadisticas['cotizaciones_aprobadas'] = $stmtCot->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
    } else if ($rol === 'jefe_area') {
        // Jefe de área: solo su área
        $stmtArea = $db->prepare("SELECT estado, COUNT(*) as count FROM requisiciones 
                                  WHERE idArea = :idArea AND lActivo = 1 
                                  GROUP BY estado");
        $stmtArea->bindParam(':idArea', $usuario['idArea']);
        $stmtArea->execute();
        $estadisticas = $stmtArea->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Cotizaciones aprobadas del área en curso
        $stmtCot = $db->prepare("SELECT COUNT(*) as total FROM cotizaciones c 
                                 JOIN requisiciones r ON c.idRequisicion = r.id 
                                 WHERE r.idArea = :idArea AND c.bAprovada = 1 AND r.estado != 'entregado' AND r.lActivo = 1");
        $stmtCot->bindParam(':idArea', $usuario['idArea']);
        $stmtCot->execute();
        $estadisticas['cotizaciones_aprobadas'] = $stmtCot->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
    } else if ($rol === 'jefe_mayor') {
        // Jefe mayor: todas las requisiciones
        $stmt = $db->query("SELECT estado, COUNT(*) as count FROM requisiciones WHERE lActivo = 1 GROUP BY estado");
        $estadisticas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
    } else if ($rol === 'contaduria') {
        // Contaduría: todas las requisiciones
        $stmt = $db->query("SELECT estado, COUNT(*) as count FROM requisiciones WHERE lActivo = 1 GROUP BY estado");
        $estadisticas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    echo json_encode([
        'success' => true,
        'estadisticas' => $estadisticas
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
?>
