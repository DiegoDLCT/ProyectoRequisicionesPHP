<?php
require_once 'backend/config/database.php';

echo "👤 CREAR USUARIO CONTADURÍA<br><br>";

$database = new Database();
$conn = $database->getConnection();

// Datos del usuario
$nombre = "Contaduría General";
$email = "contaduria@empresa.com";
$contraseña = "contaduria123";
$puesto = "contaduría";
$id_area = 1; // Asignar a área 1 por defecto

// Hashear la contraseña
$password_hasheada = password_hash($contraseña, PASSWORD_DEFAULT);

// Verificar si el usuario ya existe
$verificar = "SELECT id FROM usuarios WHERE cCorreo = :email";
$stmt = $conn->prepare($verificar);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "⚠️ El usuario ya existe. Actualizando...<br>";
    
    // Actualizar usuario existente
    $query = "UPDATE usuarios SET cNombre = :nombre, cContrasena = :contrasena, cPuesto = :puesto, lActivo = 1 
              WHERE cCorreo = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":contrasena", $password_hasheada);
    $stmt->bindParam(":puesto", $puesto);
    $stmt->bindParam(":email", $email);
    
    if ($stmt->execute()) {
        echo "✅ Usuario actualizado correctamente<br>";
    } else {
        echo "❌ Error al actualizar usuario<br>";
        print_r($stmt->errorInfo());
    }
} else {
    echo "✨ Creando nuevo usuario...<br>";
    
    // Crear nuevo usuario
    $query = "INSERT INTO usuarios (cNombre, cCorreo, cContrasena, cPuesto, idArea, lActivo) 
              VALUES (:nombre, :email, :contrasena, :puesto, :id_area, 1)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":contrasena", $password_hasheada);
    $stmt->bindParam(":puesto", $puesto);
    $stmt->bindParam(":id_area", $id_area);
    
    if ($stmt->execute()) {
        echo "✅ Usuario creado correctamente<br>";
        $user_id = $conn->lastInsertId();
        echo "🆔 ID: " . $user_id . "<br>";
    } else {
        echo "❌ Error al crear usuario<br>";
        print_r($stmt->errorInfo());
    }
}

echo "<br>📧 Email: " . $email . "<br>";
echo "🔑 Contraseña: " . $contraseña . "<br>";
echo "👔 Puesto: " . $puesto . "<br>";
?>
