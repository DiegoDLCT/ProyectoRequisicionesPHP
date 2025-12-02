<?php
function tieneRol($rolRequerido) {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['cPuesto'])) {
        return false;
    }
    
    // Verificar que el puesto del usuario coincida con el rol requerido
    return $_SESSION['usuario']['cPuesto'] === $rolRequerido;
}
?>