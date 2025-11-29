<?php
function tieneRol($rolRequerido) {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['cPuesto'])) {
        return false;
    }
    
    // Para jefe_mayor, verificar el puesto
    if ($rolRequerido === 'jefe_mayor') {
        return $_SESSION['usuario']['cPuesto'] === 'jefe_mayor';
    }
    
    // Para admin u otros roles
    return true;
}
?>