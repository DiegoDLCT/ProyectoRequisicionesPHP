<?php
// Script para mover archivos de prueba a carpeta dev

$archivosAMover = [
    'check_all_tables.php',
    'check_proveedores.php',
    'check_requisiciones_cols.php',
    'check_tipos.php',
    'check_tipos_pagos.php',
    'crear_compra_prueba.php',
    'crear_datos_prueba_contaduria.php',
    'crear_datos_prueba_jefe_mayor.php',
    'crear_requisiciones_prueba.php',
    'crear_tabla_proveedores.php',
    'crear_usuario_contaduria.php',
    'eliminar_tablas.php',
    'eliminar_tablas_no_usadas.php',
    'integrar_unidades.php',
    'limpiar_requisiciones.php',
    'preparar_usuario.php',
    'resumen_final.php',
    'test_compras_flow.php',
    'test_conexion.php',
    'test_estructura.php',
    'test_lista_requisiciones.php',
    'test_modelo_usuario.php',
    'test_requisicion.php',
    'test_requisicion_debug.php',
    'test_unidades.php',
    'test_usuarios.php',
    'verificar_botones_cancelar.php',
    'verificar_final.php',
    'verificar_reportes_eliminados.php',
    'ver_compras.php',
    'UI_IMPROVEMENTS.md'
];

$rootDir = dirname(__DIR__);
$devDir = $rootDir . '/scripts/dev';

echo "Moviendo archivos de prueba a scripts/dev/\n";
echo "===========================================\n\n";

foreach ($archivosAMover as $archivo) {
    $origen = $rootDir . '/' . $archivo;
    $destino = $devDir . '/' . $archivo;
    
    if (file_exists($origen)) {
        if (rename($origen, $destino)) {
            echo "✓ Movido: $archivo\n";
        } else {
            echo "✗ Error moviendo: $archivo\n";
        }
    }
}

echo "\n✅ Limpieza completada\n";
echo "Archivos de prueba ahora están en: scripts/dev/\n";
echo "Archivos importantes mantenidos en raíz:\n";
echo "  - generar_hash.php\n";
echo "  - index.php\n";
echo "  - .htaccess_backup\n";
echo "  - README.md\n";
?>
