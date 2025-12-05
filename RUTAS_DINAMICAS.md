# ✅ Rutas Dinámicas - Compatibilidad de Nombres de Carpeta

## Problema Resuelto

Anteriormente, las rutas estaban **hardcodeadas** con `/ProyectoPHP/`, lo que significaba que si alguien renombraba la carpeta, el proyecto no funcionaría.

**Ahora está SOLUCIONADO** ✨

## ¿Cómo Funciona?

### 1. Sistema de Rutas Dinámicas

Se detecta automáticamente el nombre de la carpeta del proyecto:

```php
// backend/config/routes.php
$uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$projectFolder = $uriParts[0] ?? '';

define('PROJECT_FOLDER', $projectFolder);
define('BASE_URL', '/' . $projectFolder);
define('SERVICES_URL', BASE_URL . '/backend/services');
// ... etc
```

### 2. Constantes Disponibles

En cualquier archivo PHP, después de incluir `routes.php`, puedes usar:

```php
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';

// Rutas Web (para HTML, fetch, etc)
BASE_URL              // / . nombre_carpeta
BACKEND_URL           // / . nombre_carpeta . /backend
FRONTEND_URL          // / . nombre_carpeta . /frontend
ASSETS_URL            // / . nombre_carpeta . /frontend/assets
SERVICES_URL          // / . nombre_carpeta . /backend/services

// Rutas del Servidor (para file_get_contents, etc)
PROJECT_ROOT          // Carpeta raíz del proyecto
BACKEND_PATH          // Carpeta backend
FRONTEND_PATH         // Carpeta frontend
UPLOADS_PATH          // Carpeta uploads
```

### 3. Uso en HTML

**Antes (Hardcodeado - ❌ NO FUNCIONA si cambias nombre):**
```php
<form action="/ProyectoPHP/backend/services/confirmar_pago_service.php">
```

**Ahora (Dinámico - ✅ FUNCIONA siempre):**
```php
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';
// ...
<form action="<?= SERVICES_URL ?>/confirmar_pago_service.php">
```

### 4. Uso en JavaScript (fetch)

**Antes (❌ NO FUNCIONA):**
```javascript
fetch('/ProyectoPHP/backend/services/aprobar_service.php', { ... })
```

**Ahora (✅ FUNCIONA):**
```php
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';
// ...
?>
<script>
fetch('<?= SERVICES_URL ?>/aprobar_service.php', { ... })
</script>
```

---

## 📝 Para Tus Compañeros

**El proyecto ahora funciona con CUALQUIER nombre de carpeta:**

```
✅ http://localhost/ProyectoPHP/
✅ http://localhost/SistemaRequisiciones/
✅ http://localhost/requisiciones-app/
✅ http://localhost/MiProyecto/
```

**Todo funcionará perfectamente sin necesidad de cambiar código.**

---

## 🔍 Archivos Modificados

Se agregó `routes.php` a:

✅ `backend/config/routes.php` - Nueva definición de constantes  
✅ `frontend/views/pagos/metodo_pago.php` - Usa SERVICES_URL  
✅ `frontend/views/pagos/solicitar.php` - Usa SERVICES_URL  
✅ `frontend/views/pagos/gestionar_entregas.php` - Usa SERVICES_URL  
✅ `frontend/views/pagos/finalizar_entregas.php` - Usa SERVICES_URL  
✅ `frontend/views/proveedores/listar.php` - Usa SERVICES_URL  
✅ `frontend/views/cotizaciones/aprobar.php` - Usa SERVICES_URL en fetch  
✅ `frontend/views/requisiciones/ver.php` - Usa SERVICES_URL en fetch  

---

## 🚀 Para Futuros Desarrolladores

Si encuentras rutas hardcodeadas en el código:

1. Incluye `routes.php` al inicio del archivo
2. Reemplaza las rutas hardcodeadas con las constantes

**Ejemplo:**

```php
<?php
// Al inicio del archivo
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';

// Luego en el HTML/JS
<a href="<?= BASE_URL ?>/frontend/views/dashboard/admin.php">Admin</a>
<form action="<?= SERVICES_URL ?>/algún_service.php">
```

---

**Versión:** 1.0  
**Actualizado:** 5 de diciembre de 2025  
**Estado:** ✅ 100% Compatible con cambios de nombres de carpeta
