<?php
require_once __DIR__ . '/backend/config/database.php';

try {
    $db = (new Database())->getConnection();
    
    // Actualizar todas las requisiciones a entregado
    $sql = "UPDATE requisiciones SET estado = 'entregado' WHERE lActivo = 1";
    $stmt = $db->prepare($sql);
    $resultado = $stmt->execute();
    
    if ($resultado) {
        echo "<h2>✅ Éxito</h2>";
        echo "<p>Todas las requisiciones han sido actualizadas a estado 'entregado'.</p>";
        
        // Mostrar conteo
        $sql_count = "SELECT COUNT(*) as total FROM requisiciones WHERE estado = 'entregado'";
        $stmt_count = $db->prepare($sql_count);
        $stmt_count->execute();
        $row = $stmt_count->fetch(PDO::FETCH_ASSOC);
        echo "<p><strong>Total requisiciones completadas: {$row['total']}</strong></p>";
        echo "<p><a href='frontend/index.php'>Volver al sistema</a></p>";
    }
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
