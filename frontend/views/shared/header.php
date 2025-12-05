<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Ajustar ruta a la raíz del proyecto (subir 3 niveles desde views/shared)
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
$usuario = $_SESSION['usuario'] ?? null;
?>
<header style="background:#ffffff;border-bottom:1px solid #e5e7eb;padding:12px 20px;">
    <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="../../index.php" style="font-weight:700;color:#2563eb;text-decoration:none;font-size:18px;">Sistema de Requisiciones</a>
            <nav style="display:flex;gap:12px;align-items:center;">
                <a href="../../index.php" style="color:#374151;text-decoration:none;">Dashboard</a>
                <a href="../requisiciones/listar.php" style="color:#374151;text-decoration:none;">Mis Requisiciones</a>
                <a href="../requisiciones/crear.php" style="color:#374151;text-decoration:none;">Crear Requisición</a>
                <?php if (tieneRol('jefe_mayor')): ?>
                    <a href="../cotizaciones/aprobar.php" style="color:#374151;text-decoration:none;">Aprobar Cotizaciones</a>
                <?php endif; ?>
                <?php if (tieneRol('admin')): ?>
                    <a href="../usuarios/listar.php" style="color:#374151;text-decoration:none;">Usuarios</a>
                <?php endif; ?>
             </nav>
         </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <?php if ($usuario): ?>
                <div style="text-align:right;">
                    <div style="font-weight:600;color:#0f172a"><?php echo htmlspecialchars($usuario['cNombre']); ?></div>
                    <div style="font-size:12px;color:#6b7280"><?php echo htmlspecialchars($usuario['cPuesto'] ?? ''); ?></div>
                </div>
                <a href="../auth/logout.php" style="margin-left:12px;padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px;color:#ef4444;text-decoration:none;">Cerrar sesión</a>
            <?php else: ?>
                <a href="../auth/login.php" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px;color:#2563eb;text-decoration:none;">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</header>