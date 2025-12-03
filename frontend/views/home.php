<?php
session_start();

require_once dirname(__DIR__, 2) . '/backend/utils/auth.php';

// Si no hay sesión de usuario, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header('Location: views/auth/login.php');
    exit();
}

$usuario = $_SESSION['usuario'];
// Manejo de mensajes por querystring (flash simple)
$flash = null;
    if (isset($_GET['error']) && $_GET['error'] === 'denegado') {
    $flash = [
        'type' => 'error',
        'text' => 'Acceso denegado. No tienes permisos para ver esa página.'
    ];
} elseif (isset($_GET['mensaje']) && $_GET['mensaje'] === 'aprobada') {
    $flash = [
        'type' => 'success',
        'text' => 'Acción completada exitosamente.'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - Sistema de Requisiciones</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        .home-container { max-width: 1100px; margin: 30px auto; padding: 20px; background: #fff; border-radius:8px; }
        .cards { display:flex; gap:18px; flex-wrap:wrap; }
        .card { flex:1 1 240px; padding:18px; border-radius:8px; background:#f8fafc; box-shadow:0 1px 4px rgba(0,0,0,.06); }
        .card a { text-decoration:none; color:#2563eb; font-weight:600; }
        .welcome { margin-bottom:18px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/shared/header.php'; ?>

    <div class="home-container">
        <?php if ($flash): ?>
            <div class="mensaje <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>" style="margin-bottom:16px;padding:12px;border-radius:6px;">
                <?php echo $flash['text']; ?>
            </div>
        <?php endif; ?>
        <div class="welcome">
            <h1>Bienvenido, <?php echo htmlspecialchars($usuario['cNombre']); ?></h1>
            <p>Has iniciado sesión como <strong><?php echo htmlspecialchars($usuario['cPuesto']); ?></strong>.</p>
        </div>

        <div class="cards">
            <!-- Acciones comunes para todos los usuarios -->
            <div class="card">
                <h3>Crear Requisición</h3>
                <p>Crear una nueva requisición para tu área.</p>
                <p><a href="requisiciones/crear.php">Ir a Crear</a></p>
            </div>

            <div class="card">
                <h3>Mis Requisiciones</h3>
                <p>Ver y dar seguimiento a tus requisiciones.</p>
                <p><a href="requisiciones/listar.php">Ver mis requisiciones</a></p>
            </div>

            <div class="card">
                <h3>Ver Requisiciones</h3>
                <p>Explorar requisiciones del sistema (según permisos).</p>
                <p><a href="requisiciones/ver.php">Ir a Requisiciones</a></p>
            </div>

            <!-- Solo jefe_mayor (o admin) puede aprobar cotizaciones -->
            <?php if (tieneRol('jefe_mayor') || tieneRol('admin')): ?>
            <div class="card">
                <h3>Aprobar Cotizaciones</h3>
                <p>Revisar y aprobar cotizaciones pendientes.</p>
                <p><a href="cotizaciones/aprobar.php">Aprobar cotizaciones</a></p>
            </div>
            <?php endif; ?>

            <!-- Opciones administrativas solo para admin -->
            <?php if (tieneRol('admin')): ?>
            <div class="card">
                <h3>Administración</h3>
                <p>Panel de administración del sistema.</p>
                <p><a href="dashboard/admin.php">Ir a Administración</a></p>
            </div>

            <div class="card">
                <h3>Usuarios</h3>
                <p>Invitar y administrar usuarios del sistema.</p>
                <p><a href="usuarios/invitar.php">Administrar usuarios</a></p>
            </div>

            <div class="card">
                <h3>Subir Cotizaciones</h3>
                <p>Subir cotizaciones asociadas a requisiciones.</p>
                <p><a href="cotizaciones/subir.php?id_requisicion=">Subir cotización</a></p>
            </div>

            <div class="card">
                <h3>Reportes</h3>
                <p>Generar reportes y estadísticas.</p>
                <p><a href="dashboard/admin.php">Ir a Reportes</a></p>
            </div>
            <?php endif; ?>
        </div>

        <div style="margin-top:20px;">
            <a href="auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <?php include __DIR__ . '/shared/footer.php'; ?>
</body>
</html>
