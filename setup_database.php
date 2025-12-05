<?php
/**
 * Script de Setup - Sistema de Requisiciones v1.0
 * Crea la base de datos y ejecuta las migraciones automáticamente
 */

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     SETUP - Sistema de Requisiciones v1.0                 ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Configuración de la BD
$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$dbName = 'requisiciones_db';

echo "📋 Configuración:\n";
echo "   Host: $dbHost\n";
echo "   Usuario: $dbUser\n";
echo "   Base de datos: $dbName\n\n";

try {
    // 1. Conectar al servidor MySQL (sin BD específica)
    echo "🔗 Conectando a MySQL...\n";
    $conn = new PDO("mysql:host=$dbHost", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Conectado\n\n";

    // 2. Verificar si existe la BD
    echo "🔍 Verificando base de datos...\n";
    $stmt = $conn->query("SHOW DATABASES LIKE '$dbName'");
    $exists = $stmt->rowCount() > 0;

    if ($exists) {
        echo "   ⚠️  Base de datos ya existe. Recreando...\n";
        $conn->exec("DROP DATABASE `$dbName`");
        echo "   ✓ Base de datos eliminada\n";
    }

    // 3. Crear BD
    echo "\n📦 Creando base de datos...\n";
    $conn->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   ✓ Base de datos '$dbName' creada\n\n";

    // 4. Conectar a la nueva BD
    echo "🔗 Conectando a la base de datos nueva...\n";
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Conectado\n\n";

    // 5. Ejecutar migraciones
    echo "🗂️  Importando esquema...\n";
    $migrationFile = dirname(__FILE__) . '/migrations/db_structure_dump.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Archivo de migración no encontrado: $migrationFile");
    }

    $sql = file_get_contents($migrationFile);
    
    // Dividir por ;
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => !empty($s) && strpos($s, '--') !== 0
    );

    foreach ($statements as $statement) {
        $conn->exec($statement . ';');
    }
    echo "   ✓ Esquema importado\n\n";

    // 6. Crear usuario admin
    echo "👤 Creando usuario administrador...\n";
    $adminEmail = 'admin@admin.com';
    $adminPassword = 'password';
    $adminHash = password_hash($adminPassword, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("
        INSERT INTO usuarios (cNombre, cCorreo, cContrasena, cPuesto, idArea, lActivo) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        'Administrador',
        $adminEmail,
        $adminHash,
        'admin',
        1,
        1
    ]);
    
    echo "   ✓ Usuario admin creado\n";
    echo "     Email: $adminEmail\n";
    echo "     Contraseña: $adminPassword\n\n";

    // 7. Resumen
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║                  ✅ SETUP COMPLETADO                      ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";

    echo "📝 Próximos pasos:\n";
    echo "   1. Verifica que MySQL está ejecutándose\n";
    echo "   2. Coloca la carpeta en C:\\xampp\\htdocs\\\n";
    echo "   3. Accede a: http://localhost/ProyectoPHP/\n";
    echo "   4. Inicia sesión con: admin@admin.com / password\n\n";

    echo "📚 Documentación: Consulta SETUP.md para más información\n";

} catch (PDOException $e) {
    echo "\n❌ Error de base de datos:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "💡 Soluciones:\n";
    echo "   • Verifica que MySQL está corriendo\n";
    echo "   • Comprueba el usuario y contraseña\n";
    echo "   • Intenta nuevamente\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Error:\n";
    echo "   " . $e->getMessage() . "\n\n";
    exit(1);
}
?>
