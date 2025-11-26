<?php
$password = "jefe123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "🔐 HASH GENERADO PARA 'jefe123':<br>";
echo "<strong>" . $hash . "</strong>";

echo "<br><br>📋 QUERY PARA INSERTAR:<br>";
echo "<code>INSERT INTO usuarios (cNombre, cCorreo, cContrasena, cPuesto, idArea, lActivo) VALUES ('Jefe Mayor', 'jefemayor@empresa.com', '" . $hash . "', 'jefe_mayor', 1, 1);</code>";
?>