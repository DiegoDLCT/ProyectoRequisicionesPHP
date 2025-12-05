<?php
require_once 'backend/config/database.php';

echo "⚙️ INTEGRANDO TABLA UNIDADES\n\n";

$db = (new Database())->getConnection();

try {
    // 1. Verificar si la columna idUnidad ya existe
    $check = $db->query("DESCRIBE requisiciones")->fetchAll(PDO::FETCH_ASSOC);
    $columnas = array_map(fn($col) => $col['Field'], $check);
    
    if (!in_array('idUnidad', $columnas)) {
        echo "1️⃣ Agregando columna idUnidad a requisiciones...\n";
        $db->exec("ALTER TABLE requisiciones ADD COLUMN idUnidad INT(11) DEFAULT NULL");
        echo "✅ Columna agregada\n\n";
    } else {
        echo "✓ Columna idUnidad ya existe\n\n";
    }
    
    // 2. Verificar datos en unidades
    $stmt = $db->query("SELECT COUNT(*) as total FROM unidades");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['total'] == 0) {
        echo "2️⃣ Poblando tabla unidades con valores estándar...\n";
        
        $unidades_prueba = [
            'Unidad',
            'Kilogramo (kg)',
            'Litro (L)',
            'Metro (m)',
            'Metro cuadrado (m²)',
            'Metro cúbico (m³)',
            'Pieza',
            'Docena',
            'Caja',
            'Hora',
            'Día',
            'Mes'
        ];
        
        foreach ($unidades_prueba as $unidad) {
            $db->exec("INSERT INTO unidades (cNombre) VALUES ('$unidad')");
        }
        
        echo "✅ Insertadas " . count($unidades_prueba) . " unidades\n\n";
    } else {
        echo "✓ Tabla unidades ya tiene " . $result['total'] . " registros\n\n";
    }
    
    // 3. Mostrar unidades disponibles
    echo "3️⃣ UNIDADES DISPONIBLES:\n";
    $stmt = $db->query("SELECT * FROM unidades ORDER BY id");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $unidad) {
        echo "   ID: " . $unidad['id'] . " → " . $unidad['cNombre'] . "\n";
    }
    
    echo "\n✨ Integración completada. Ahora edita crear.php para agregar selector de unidad\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
