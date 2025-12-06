<?php
session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
require_once dirname(__DIR__, 3) . '/backend/models/Proveedor.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (!tieneRol('admin') && !tieneRol('Contaduria')) {
    header('Location: ../../index.php?error=denegado');
    exit;
}

$db = (new Database())->getConnection();
$proveedorModel = new Proveedor($db);
$proveedores = $proveedorModel->obtenerTodos();

$mensaje_exito = $_SESSION['mensaje_exito'] ?? null;
$mensaje_error = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f5f7; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 28px; color: #1d1d1f; margin: 0; }
        .header p { color: #86868b; font-size: 14px; margin: 5px 0 0; }
        .header-content { flex: 1; }
        .btn { display: inline-block; padding: 10px 20px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; transition: all 0.2s; }
        .btn:hover { background: #0077ed; transform: translateY(-1px); }
        .btn-secondary { background: #86868b; }
        .btn-secondary:hover { background: #6e6e73; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f5f5f7; }
        th { padding: 15px; text-align: left; font-weight: 600; color: #1d1d1f; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid #f5f5f7; color: #1d1d1f; }
        tr:hover { background: #fafafa; }
        .empty { text-align: center; padding: 60px 20px; color: #86868b; }
        .empty i { font-size: 48px; margin-bottom: 15px; display: block; }
        .actions { display: flex; gap: 10px; }
        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-edit { background: #f59e0b; }
        .btn-edit:hover { background: #d97706; }
        .btn-delete { background: #ef4444; }
        .btn-delete:hover { background: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>Proveedores</h1>
                <p>Gestión de base de proveedores</p>
            </div>
            <a href="crear.php" class="btn">
                <i class="bi bi-plus-circle"></i> Nuevo Proveedor
            </a>
        </div>

        <?php if ($mensaje_exito): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
        <?php endif; ?>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($mensaje_error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (empty($proveedores)): ?>
                <div class="empty">
                    <i class="bi bi-inbox"></i>
                    <p>No hay proveedores registrados</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Contacto</th>
                            <th>Ciudad</th>
                            <th>País</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $prov): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($prov['cNombre']) ?></strong></td>
                                <td><?= htmlspecialchars($prov['cCorreo']) ?></td>
                                <td><?= htmlspecialchars($prov['cTelefono'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($prov['cContacto'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($prov['cCiudad'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($prov['cPais'] ?? '-') ?></td>
                                <td class="actions">
                                    <a href="editar.php?id=<?= $prov['id'] ?>" class="btn btn-sm btn-edit">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-sm btn-delete" onclick="abrirModalEliminar(<?= $prov['id'] ?>, '<?= htmlspecialchars($prov['cNombre']) ?>')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <button onclick="history.back()" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </button>
                <a href="../../index.php" class="btn" style="background: #2563eb; margin-left: 10px;">
                    <i class="bi bi-house"></i> Inicio
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de eliminación -->
    <div class="modal-overlay" id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
            <h2 style="font-size: 20px; color: #1d1d1f; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-exclamation-circle" style="color: #ef4444;"></i> Confirmar Eliminación
            </h2>
            <p style="color: #6b7280; margin-bottom: 20px;">¿Deseas eliminar el proveedor <strong id="nombreProveedor"></strong>? Esta acción no se puede deshacer.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalEliminar()" style="background: #d1d5db; color: #1f2937;">Cancelar</button>
                <button type="button" class="btn btn-delete" onclick="confirmarEliminacion()" style="background: #ef4444;">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        let idProveedorAEliminar = null;

        function abrirModalEliminar(id, nombre) {
            idProveedorAEliminar = id;
            document.getElementById('nombreProveedor').textContent = nombre;
            document.getElementById('modalEliminar').style.display = 'flex';
        }

        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').style.display = 'none';
            idProveedorAEliminar = null;
        }

        function confirmarEliminacion() {
            if (!idProveedorAEliminar) return;

            const formData = new FormData();
            formData.append('id', idProveedorAEliminar);

            const btnEliminar = event.target;
            btnEliminar.disabled = true;
            btnEliminar.innerHTML = '<i class="bi bi-hourglass-split"></i> Eliminando...';

            fetch('/ProyectoPHP/frontend/services/eliminar_proveedor_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                cerrarModalEliminar();
                console.log('Respuesta recibida:', text);
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        setTimeout(() => { location.reload(); }, 1000);
                    } else {
                        alert('Error: ' + data.mensaje);
                        btnEliminar.disabled = false;
                        btnEliminar.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
                    }
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    alert('Error: ' + text.substring(0, 100));
                    btnEliminar.disabled = false;
                    btnEliminar.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
                }
            })
            .catch(error => {
                cerrarModalEliminar();
                console.error('Error en fetch:', error);
                alert('Error en la solicitud: ' + error);
                btnEliminar.disabled = false;
                btnEliminar.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
            });
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalEliminar').addEventListener('click', function(e) {
            if (e.target === this) cerrarModalEliminar();
        });
    </script>
</body>
</html>
