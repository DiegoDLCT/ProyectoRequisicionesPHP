<?php
// Script pequeño para generar el hash de la contraseña que quieres insertar manualmente.
// Uso: ejecutar desde la raíz del proyecto con `php generar_hash.php`.

$password = 'jefearea123';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Salida en texto plano para copiar fácilmente
echo "HASH_PHP:" . PHP_EOL;
echo $hash . PHP_EOL . PHP_EOL;

// También muestro una consulta INSERT de ejemplo (ajusta campos según tu esquema)
echo "-- EJEMPLO SQL (ajusta cNombre, cCorreo, idArea según corresponda):" . PHP_EOL;
echo "INSERT INTO usuarios (cNombre, cCorreo, cContrasena, cPuesto, idArea, lActivo) VALUES ('Nombre Apellido', 'email@example.com', '" . $hash . "', 'jefe_area', 1, 1);" . PHP_EOL;

// Nota: cambia 'Nombre Apellido', 'email@example.com' y 'idArea' antes de ejecutar la query en tu base de datos.
?>