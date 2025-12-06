# Setup del Proyecto - Sistema de Requisiciones v1.0

## Requisitos Previos

- PHP 7.4+
- MySQL 5.7+
- XAMPP o servidor local equivalente
- Git

## Instalación Paso a Paso

### 1. Clonar el Repositorio

```bash
git clone https://github.com/DiegoDLCT/ProyectoRequisicionesPHP.git
cd ProyectoRequisicionesPHP
```

### 2. Crear la Base de Datos

Ejecuta el siguiente comando en la terminal desde la carpeta del proyecto:

```bash
php setup_database.php
```

Este script:
- Crea la base de datos `requisiciones_db`
- Importa el esquema desde `migrations/db_structure_dump.sql`
- Crea usuario admin por defecto

### 3. Configurar la Conexión a la Base de Datos

Si usas configuración personalizada, edita `backend/config/database.php`:

```php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_NAME = 'requisiciones_db';
```

### 4. Acceder al Sistema

1. Coloca la carpeta del proyecto en `C:\xampp\htdocs\`
2. Inicia Apache y MySQL en XAMPP
3. Abre en el navegador: `http://localhost/ProyectoPHP/`

## Flexibilidad en Nombres de Carpeta

**IMPORTANTE:** El proyecto es completamente flexible con el nombre de la carpeta.

Si nombras la carpeta de otra manera:
- `SistemaRequisiciones`
- `requisiciones-app`
- `MiProyecto`
- Cualquier otro nombre

El proyecto funcionará perfectamente. Las rutas son **100% dinámicas** y se adaptan automáticamente.

### ¿Cómo funciona?

El sistema detecta automáticamente el nombre de la carpeta mediante `backend/config/routes.php`:

```php
// Se define automáticamente según el nombre de la carpeta
BASE_URL       // Raíz del proyecto
SERVICES_URL   // Servicios backend
ASSETS_URL     // Archivos CSS, JS
// ... etc
```

**Ejemplos que funcionan igual:**
```
http://localhost/ProyectoPHP/
http://localhost/SistemaRequisiciones/
http://localhost/requisiciones/
```

Para más detalles, consulta `RUTAS_DINAMICAS.md`

## Credenciales de Prueba

**Usuario:** admin@admin.com  
**Contraseña:** password

## Roles Disponibles

- **Admin**: Acceso completo a todos los módulos
- **Jefe Mayor**: Aprueba cotizaciones y solicita pagos
- **Contaduría**: Confirma y gestiona pagos
- **Jefe de Área**: Crea requisiciones de su área
- **Solicitante**: Crea requisiciones

## Estructura del Proyecto

```
ProyectoPHP/
├── backend/
│   ├── config/          # Configuración de BD y constantes
│   ├── controllers/     # Controladores de la aplicación
│   ├── models/          # Modelos de datos
│   ├── services/        # Servicios de lógica de negocio
│   └── utils/           # Utilidades (auth, validación)
├── frontend/
│   ├── assets/          # CSS, JS, imágenes
│   ├── views/           # Vistas del sistema
│   │   ├── auth/        # Login
│   │   ├── dashboard/   # Dashboards por rol
│   │   ├── requisiciones/
│   │   ├── cotizaciones/
│   │   ├── pagos/
│   │   ├── proveedores/
│   │   ├── usuarios/
│   │   └── shared/      # Componentes compartidos
│   └── index.php        # Punto de entrada
├── migrations/          # Esquema de base de datos
├── scripts/
│   ├── dev/            # Scripts de desarrollo y prueba
│   └── limpiar_basura.php
├── uploads/            # Archivos cargados
├── generar_hash.php    # Utilidad para generar hashes
└── README.md           # Documentación

```

## Flujo del Sistema

### 7 Estados de Requisición

1. **Pendiente** → Sin cotizar
2. **Cotizado** → Esperando aprobación
3. **Pago Solicitado** → Aprobado, en espera de pago
4. **Pagado** → Pago confirmado
5. **Por Entregar** → Preparación de entrega
6. **Entregado** → Completado

## Características Principales

- Gestión completa de requisiciones  
- Sistema de cotizaciones múltiples  
- Control de pagos y entregas  
- Gestión de proveedores  
- Control de usuarios por rol  
- Interfaz minimalista y moderna  
- Responsive design  

## Solución de Problemas

### Error: "Base de datos no existe"
Ejecuta nuevamente: `php setup_database.php`

### Error: "SQLSTATE[HY000] [2002]"
Verifica que MySQL está corriendo en XAMPP

### Error: "No tienes permiso para acceder"
Asegúrate de tener el rol correcto en la base de datos

## Desarrollo

Para generar hashes de contraseñas:

```bash
php scripts/dev/generar_hash.php
```

## Contacto y Soporte

Para preguntas o problemas, contacta al equipo de desarrollo.

---

**Versión:** 1.0  
**Última actualización:** 5 de diciembre de 2025
