<?php
function tieneRol($rolRequerido) {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['cPuesto'])) {
        return false;
    }
    // Normalizar a minúsculas para comparación
    $puesto = strtolower($_SESSION['usuario']['cPuesto']);
    $rolRequerido = strtolower($rolRequerido);

    // El admin tiene acceso a todo
    if ($puesto === 'admin') {
        return true;
    }

    // Verificar que el puesto del usuario coincida con el rol requerido
    return $puesto === $rolRequerido;
}
?>