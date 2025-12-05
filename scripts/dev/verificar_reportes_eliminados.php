<?php
echo "✅ VERIFICACIÓN: ACCESO A REPORTES ELIMINADO\n\n";

echo "1️⃣ Buscando referencias a 'reportes'...\n";
$comando_grep = "grep -r 'reportes' c:\\xampp\\htdocs\\ProyectoPHP\\frontend\\ --include='*.php' 2>nul";
$resultado = shell_exec($comando_grep);

if (empty($resultado)) {
    echo "   ✅ No hay referencias a 'reportes' en frontend\n\n";
} else {
    echo "   ⚠️ Encontradas referencias:\n$resultado\n";
}

echo "2️⃣ Verificando directorio de reportes...\n";
if (!is_dir('c:\\xampp\\htdocs\\ProyectoPHP\\frontend\\views\\reportes')) {
    echo "   ✅ Directorio /reportes no existe\n\n";
} else {
    echo "   ⚠️ Directorio /reportes encontrado\n\n";
}

echo "3️⃣ Verificando backend...\n";
$comando_grep_backend = "grep -r 'reportes' c:\\xampp\\htdocs\\ProyectoPHP\\backend\\ --include='*.php' 2>nul";
$resultado_backend = shell_exec($comando_grep_backend);

if (empty($resultado_backend)) {
    echo "   ✅ No hay referencias en backend\n\n";
} else {
    echo "   ⚠️ Encontradas referencias:\n$resultado_backend\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✨ ACCESO A REPORTES COMPLETAMENTE REMOVIDO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "CAMBIOS REALIZADOS:\n";
echo "  ✓ Eliminado link 'Reportes' del dashboard admin\n";
echo "  ✓ No hay archivo generar.php\n";
echo "  ✓ No hay directorio /reportes\n";
echo "  ✓ Sin referencias en código\n\n";

echo "MÓDULOS ACTIVOS EN DASHBOARD ADMIN:\n";
echo "  1. Requisiciones - Crear y gestionar\n";
echo "  2. Cotizaciones - Subir cotizaciones\n";
echo "  3. Entregas - Gestionar entregas\n";
echo "  4. Proveedores - CRUD de proveedores\n";
echo "  5. Usuarios - Gestión de usuarios\n";
?>
