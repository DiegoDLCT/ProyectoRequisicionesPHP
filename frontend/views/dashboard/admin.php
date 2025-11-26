<?php
// Verificar que el usuario esté logueado
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background: #f5f5f5;
        }
        .dashboard {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 20px;
        }
        .user-info {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
        }
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .module-card {
            background: #f8fafc;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #1f2937;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .module-card:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: translateY(-2px);
        }
        .module-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .module-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .module-desc {
            font-size: 14px;
            color: #6b7280;
        }
        .module-card:hover .module-desc {
            color: #e5e7eb;
        }
        .logout {
            color: #dc2626;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            border: 1px solid #dc2626;
            border-radius: 5px;
        }
        .logout:hover {
            background: #dc2626;
            color: white;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>🛠️ Panel de Administrador</h1>
        
        <div class="user-info">
            <strong>👤 Usuario:</strong> <?php echo $usuario['cNombre']; ?><br>
            <strong>📧 Email:</strong> <?php echo $usuario['cCorreo']; ?><br>
            <strong>💼 Puesto:</strong> <?php echo $usuario['cPuesto']; ?>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number" id="total-requisiciones">--</div>
                <div class="stat-label">Total Requisiciones</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="requisiciones-pendientes">--</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="requisiciones-cotizadas">--</div>
                <div class="stat-label">En Cotización</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="requisiciones-aprobadas">--</div>
                <div class="stat-label">Aprobadas</div>
            </div>
        </div>

        <!-- Módulos del sistema -->
        <h2>🚀 Módulos del Sistema</h2>
        <div class="modules-grid">
            <a href="../requisiciones/crear.php" class="module-card">
                <div class="module-icon">📝</div>
                <div class="module-title">Nueva Requisición</div>
                <div class="module-desc">Crear nueva solicitud de materiales</div>
            </a>
            
            <a href="../requisiciones/listar.php" class="module-card">
                <div class="module-icon">📋</div>
                <div class="module-title">Ver Requisiciones</div>
                <div class="module-desc">Lista completa y seguimiento</div>
            </a>
            
            <a href="../cotizaciones/pendientes.php" class="module-card">
                <div class="module-icon">📎</div>
                <div class="module-title">Cotizaciones</div>
                <div class="module-desc">Gestionar cotizaciones pendientes</div>
            </a>
            
            <a href="../proveedores/listar.php" class="module-card">
                <div class="module-icon">🏢</div>
                <div class="module-title">Proveedores</div>
                <div class="module-desc">Gestión de proveedores</div>
            </a>
            
            <a href="../usuarios/listar.php" class="module-card">
                <div class="module-icon">👥</div>
                <div class="module-title">Usuarios</div>
                <div class="module-desc">Gestión de usuarios del sistema</div>
            </a>
            
            <a href="../reportes/generar.php" class="module-card">
                <div class="module-icon">📊</div>
                <div class="module-title">Reportes</div>
                <div class="module-desc">Estadísticas y reportes</div>
            </a>

            <a href="../usuarios/invitar.php" class="module-card">
                <div class="module-icon">👥</div>
                <div class="module-title">Invitar Usuario</div>
                <div class="module-desc">Agregar nuevos usuarios al sistema</div>
            </a>
        </div>

        <a href="../auth/logout.php" class="logout">🚪 Cerrar Sesión</a>
    </div>
            

    <script>
        // Cargar estadísticas (puedes implementar esto después)
        document.addEventListener('DOMContentLoaded', function() {
            // Por ahora mostramos placeholders
            document.getElementById('total-requisiciones').textContent = '3';
            document.getElementById('requisiciones-pendientes').textContent = '1';
            document.getElementById('requisiciones-cotizadas').textContent = '1';
            document.getElementById('requisiciones-aprobadas').textContent = '1';
        });
    </script>
</body>
</html>