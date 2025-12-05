<?php
require 'backend/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔧 CREANDO TABLA PROVEEDORES\n\n";

try {
    $sql = "CREATE TABLE IF NOT EXISTS proveedores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cNombre VARCHAR(150) NOT NULL UNIQUE,
        cCorreo VARCHAR(150) NOT NULL UNIQUE,
        cTelefono VARCHAR(20) NULL,
        cDireccion VARCHAR(255) NULL,
        cCiudad VARCHAR(100) NULL,
        cPais VARCHAR(100) NULL,
        cContacto VARCHAR(150) NULL,
        dFechaRegistro DATE DEFAULT CURRENT_DATE,
        lActivo TINYINT(1) NOT NULL DEFAULT 1,
        INDEX (cNombre),
        INDEX (cCorreo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $conn->exec($sql);
    echo "✅ Tabla proveedores creada exitosamente\n";
    
    // Insertar algunos proveedores de ejemplo
    $proveedores = [
        ['nombre' => 'Distribuidor ABC', 'correo' => 'contacto@distribuidor-abc.com', 'telefono' => '2241234567', 'direccion' => 'Av. Principal 123', 'ciudad' => 'San José', 'pais' => 'Costa Rica', 'contacto' => 'Juan Pérez'],
        ['nombre' => 'Suministros XYZ', 'correo' => 'info@suministros-xyz.com', 'telefono' => '2245678901', 'direccion' => 'Calle Central 456', 'ciudad' => 'San José', 'pais' => 'Costa Rica', 'contacto' => 'María García'],
        ['nombre' => 'Proveedora del Centro', 'correo' => 'ventas@provcentro.com', 'telefono' => '2240987654', 'direccion' => 'Paseo Colón 789', 'ciudad' => 'San José', 'pais' => 'Costa Rica', 'contacto' => 'Carlos López'],
        ['nombre' => 'Importadora Global', 'correo' => 'global@importadora.com', 'telefono' => '2243216549', 'direccion' => 'Barrio Escalante 321', 'ciudad' => 'San José', 'pais' => 'Costa Rica', 'contacto' => 'Ana Rodríguez'],
        ['nombre' => 'Comercial del Sur', 'correo' => 'comercial@delsur.com', 'telefono' => '2249876543', 'direccion' => 'San Pedro 654', 'ciudad' => 'San Pedro', 'pais' => 'Costa Rica', 'contacto' => 'Roberto Martínez'],
    ];
    
    $query = "INSERT INTO proveedores (cNombre, cCorreo, cTelefono, cDireccion, cCiudad, cPais, cContacto) 
              VALUES (:nombre, :correo, :telefono, :direccion, :ciudad, :pais, :contacto)";
    $stmt = $conn->prepare($query);
    
    foreach ($proveedores as $prov) {
        $stmt->bindParam(':nombre', $prov['nombre']);
        $stmt->bindParam(':correo', $prov['correo']);
        $stmt->bindParam(':telefono', $prov['telefono']);
        $stmt->bindParam(':direccion', $prov['direccion']);
        $stmt->bindParam(':ciudad', $prov['ciudad']);
        $stmt->bindParam(':pais', $prov['pais']);
        $stmt->bindParam(':contacto', $prov['contacto']);
        
        if ($stmt->execute()) {
            echo "✅ Proveedor insertado: " . $prov['nombre'] . "\n";
        } else {
            echo "❌ Error al insertar: " . $prov['nombre'] . "\n";
        }
    }
    
    echo "\n✨ Se agregaron 5 proveedores de ejemplo\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
