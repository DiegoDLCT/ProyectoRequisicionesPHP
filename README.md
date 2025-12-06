# Sistema de Gestión de Requisiciones v1.0

Sistema web integral para la gestión digital de requisiciones, cotizaciones, pagos y entregas empresariales. Desarrollado con arquitectura MVC en PHP puro y MySQL.

---

## Características Principales

- **Gestión Completa de Requisiciones** - Ciclo completo de 7 estados  
- **Sistema de Cotizaciones** - Múltiples cotizaciones por requisición  
- **Control de Pagos** - Solicitud, confirmación y seguimiento  
- **Gestión de Entregas** - Preparación y confirmación de entregas  
- **Control de Usuarios por Rol** - 5 roles con permisos específicos  
- **Gestión de Proveedores** - Base de datos de proveedores  
- **Interfaz Minimalista** - Diseño moderno y responsivo  
- **Seguridad Avanzada** - Autenticación, autorización y validación  

---

## Arquitectura del Sistema

### 7 Estados de Requisición

```
Pendiente → Cotizado → Pago Solicitado → Pagado → Por Entregar → Entregado
```

### 5 Roles de Usuario

| Rol | Funciones |
|-----|-----------|
| **Admin** | Acceso completo a todos los módulos |
| **Jefe Mayor** | Aprueba cotizaciones, solicita pagos |
| **Contaduría** | Confirma pagos, gestiona entregas |
| **Jefe de Área** | Crea requisiciones de su área |
| **Solicitante** | Crea requisiciones personales |

### 8 Tablas de Base de Datos

- `usuarios` - Gestión de usuarios y roles
- `areas` - Áreas de la organización
- `requisiciones` - Requisiciones (7 estados)
- `cotizaciones` - Cotizaciones múltiples
- `compras` - Registro de compras (automático)
- `proveedores` - Base de proveedores
- `tipos_pago` - Métodos de pago
- `unidades` - Unidades de medida

---

## Inicio Rápido

### Requisitos
- PHP 7.4+
- MySQL 5.7+
- XAMPP (recomendado)

### Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/DiegoDLCT/ProyectoRequisicionesPHP.git
   cd ProyectoRequisicionesPHP
   ```

2. **Configurar base de datos automáticamente**
   ```bash
   php setup_database.php
   ```
   Este script crea automáticamente:
   - Base de datos `sistema_requisiciones`
   - Todas las tablas
   - Usuario admin (admin@admin.com / password)

3. **Iniciar el proyecto**
   - Coloca la carpeta en `C:\xampp\htdocs\`
   - Accede a: `http://localhost/ProyectoPHP/`

### Credenciales de Prueba
- **Email:** admin@admin.com
- **Contraseña:** password

---

## Guía para Compañeros - Cómo Correr el Proyecto

### Pre-requisitos
1. **XAMPP instalado** (descargar de https://www.apachefriends.org/)
2. **Apache y MySQL activados** en XAMPP
3. **Git instalado** (para clonar el repositorio)

### Pasos de Instalación

**Paso 1: Clonar el repositorio**
```bash
cd C:\xampp\htdocs
git clone https://github.com/DiegoDLCT/ProyectoRequisicionesPHP.git
cd ProyectoRequisicionesPHP
```

**Paso 2: Ejecutar setup automático**
```bash
php setup_database.php
```
Este script configura automáticamente:
- Crea la base de datos `sistema_requisiciones`
- Crea todas las tablas necesarias
- Carga datos iniciales (áreas, unidades, etc.)
- Crea usuario admin de prueba

**Paso 3: Acceder al sistema**
- Abre tu navegador
- Ve a: `http://localhost/ProyectoPHP/frontend/views/auth/login.php`
- O simplemente: `http://localhost/ProyectoPHP/` (redirige automáticamente)

**Paso 4: Iniciar sesión**
```
Email: admin@admin.com
Contraseña: password
```

### Estructura de Carpetas Importante

```
ProyectoPHP/
├── frontend/              ← PUNTO DE ENTRADA PRINCIPAL
│   └── index.php         (redirige según el rol del usuario)
├── backend/              ← Lógica del sistema
├── scripts/
│   ├── dev/             ← Scripts de desarrollo (no necesarios para correr)
│   └── ...otros archivos de utilidad
├── migrations/          ← Esquemas de BD
└── uploads/            ← Archivos cargados (cotizaciones, etc.)
```

### Solución de Problemas Comunes

**Error: "Base de datos no existe"**
- Ejecuta nuevamente: `php setup_database.php`
- Verifica que MySQL esté corriendo en XAMPP

**Error: "No puedo acceder a login.php"**
- Ve directamente a: `http://localhost/ProyectoPHP/`
- El sistema redirige automáticamente

**Error: "Sesión expirada"**
- Limpia las cookies de tu navegador
- Intenta nuevamente el login

**Error de permisos en archivos**
- Verifica que la carpeta `/uploads/` tenga permisos de escritura
- En Windows, normalmente no hay problemas

### Cuentas de Prueba Disponibles

Después de correr `setup_database.php`, hay varias cuentas predefinidas:

| Email | Rol | Contraseña |
|-------|-----|-----------|
| admin@admin.com | Admin | password |
| jefe@admin.com | Jefe Mayor | password |
| conta@admin.com | Contaduría | password |

Para crear más usuarios, usa el módulo de **Gestión de Usuarios** como admin.

### Notas Importantes

**Archivos de Desarrollo**
- La carpeta `scripts/dev/` contiene scripts de prueba y no es necesaria para correr el sistema
- Estos archivos son solo para desarrollo y testing
- Puedes ignorarla tranquilamente

**Estructura Limpia**
- El proyecto está organizado en capas (MVC)
- `backend/` → Lógica de negocios
- `frontend/` → Interfaz de usuario
- Todos los archivos innecesarios están en `scripts/dev/`

**Seguridad**
- Nunca subirás datos sensibles a git (en `.gitignore`)
- Las credenciales por defecto son solo para desarrollo
- En producción, cambia las contraseñas

---

## Configuración de Rutas y Base de Datos

### Estructura de Rutas del Proyecto

El proyecto está configurado para funcionar en `C:\xampp\htdocs\ProyectoPHP\`

**Punto de entrada principal:**
```
http://localhost/ProyectoPHP/
```

Este punto de entrada redirige automáticamente a:
```
frontend/views/auth/login.php  (si no hay sesión)
frontend/views/dashboard/      (si ya hay sesión, según el rol)
```

### Rutas Principales por Rol

| Rol | Dashboard | URL |
|-----|-----------|-----|
| Admin | admin.php | `/ProyectoPHP/frontend/views/dashboard/admin.php` |
| Jefe Mayor | jefe_mayor.php | `/ProyectoPHP/frontend/views/dashboard/jefe_mayor.php` |
| Contaduría | contaduria.php | `/ProyectoPHP/frontend/views/dashboard/contaduria.php` |
| Jefe de Área | jefe_area.php | `/ProyectoPHP/frontend/views/dashboard/jefe_area.php` |
| Solicitante | listar.php | `/ProyectoPHP/frontend/views/requisiciones/listar.php` |

### Base de Datos - Información Técnica

**Base de datos:** `sistema_requisiciones`

**Tablas principales (8 tablas):**

1. **usuarios** - Gestión de usuarios y roles
2. **areas** - Áreas de la organización
3. **requisiciones** - Requisiciones (ciclo completo)
4. **cotizaciones** - Cotizaciones múltiples
5. **proveedores** - Base de datos de proveedores
6. **compras** - Registro de compras automáticas
7. **tipos_pago** - Métodos de pago
8. **unidades** - Unidades de medida

**Estados válidos de requisición:**
- `pendiente` - Esperando cotización
- `cotizado` - Cotización recibida
- `solicitar_pago` - Listo para solicitar pago
- `pago_solicitado` - Pago en solicitud
- `pagado` - Pago confirmado
- `por_entregar` - En tránsito
- `entregado` - Completado

### Script SQL Completo

Se proporciona un script SQL completo en `migrations/setup_complete.sql` que:
- Crea la base de datos desde cero
- Crea todas las tablas con relaciones correctas
- Carga datos iniciales (áreas, unidades, tipos de pago)
- Configura índices para optimización
- Habilita claves foráneas

**Para ejecutar manualmente (opcional):**
```bash
mysql -u root -p < migrations/setup_complete.sql
```

---

## 📁 Estructura del Proyecto

```
ProyectoPHP/
├── backend/
│   ├── config/
│   │   ├── constants.php       # Constantes de la aplicación
│   │   └── database.php        # Configuración de BD
│   ├── controllers/            # Controladores principales
│   │   ├── AprobacionController.php
│   │   ├── AuthController.php
│   │   ├── CotizacionController.php
│   │   ├── PagoController.php
│   │   ├── RequisicionController.php
│   │   └── UsuarioController.php
│   ├── models/                 # Modelos de datos
│   │   ├── Area.php
│   │   ├── Cotizacion.php
│   │   ├── Proveedor.php
│   │   ├── Requisicion.php
│   │   └── Usuario.php
│   ├── services/               # Servicios de negocio
│   │   ├── AuthService.php
│   │   ├── EmailService.php
│   │   ├── aprobar_service.php
│   │   ├── cambiar_estado.php
│   │   ├── confirmar_pago_service.php
│   │   └── [otros servicios]
│   └── utils/
│       ├── auth.php            # Funciones de autenticación
│       ├── Session.php         # Gestión de sesiones
│       └── Validator.php       # Validaciones
│
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── estilo.css      # Estilos generales
│   │   │   └── global.css      # Framework CSS moderno
│   │   └── js/                 # Scripts JavaScript
│   ├── views/
│   │   ├── auth/
│   │   │   ├── login.php       # Login moderno
│   │   │   └── logout.php
│   │   ├── dashboard/
│   │   │   ├── admin.php
│   │   │   ├── contaduria.php
│   │   │   └── jefe_mayor.php
│   │   ├── requisiciones/
│   │   ├── cotizaciones/
│   │   ├── pagos/
│   │   ├── proveedores/
│   │   ├── usuarios/
│   │   └── shared/             # Componentes compartidos
│   └── index.php               # Punto de entrada
│
├── migrations/
│   ├── 001_create_schema.sql
│   └── db_structure_dump.sql   # Esquema completo
│
├── scripts/
│   ├── dev/                    # Scripts de desarrollo
│   │   └── [scripts de prueba y migración]
│   └── limpiar_basura.php
│
├── uploads/                    # Archivos cargados
├── setup_database.php          # Script de instalación
├── SETUP.md                    # Guía de instalación
├── generar_hash.php            # Utilidad para hashes
└── README.md                   # Este archivo
```

---

## 🔐 Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ Sesiones seguras con token de validación
- ✅ Validación de inputs en servidor
- ✅ Control de acceso por rol
- ✅ Protección CSRF en formularios
- ✅ SQL prepared statements

---

## 🎨 Interfaz de Usuario

- **Tema minimalista** - Diseño limpio y profesional
- **Responsive** - Funciona en desktop, tablet y mobile
- **Accesible** - Cumple estándares WCAG
- **Bootstrap Icons** - Iconografía moderna
- **Transiciones suaves** - Animaciones fluidas

---

## 📊 Flujo de Trabajo Típico

```
1. Usuario crea Requisición
   ↓
2. Jefe Mayor aprueba Cotización
   ↓
3. Contaduría confirma Pago
   ↓
4. Admin prepara Entrega
   ↓
5. Admin finaliza Entrega
   ↓
6. Requisición COMPLETADA
```

---

## 🛠️ Tecnologías Utilizadas

| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| PHP | 7.4+ | Backend |
| MySQL | 5.7+ | Base de datos |
| HTML5 | - | Estructura |
| CSS3 | - | Estilos |
| JavaScript | ES6 | Interactividad |
| Bootstrap Icons | 1.11.1 | Iconografía |
| Git | - | Control de versiones |

---

## 📝 Configuración Inicial

### Cambiar credenciales de BD

Edita `backend/config/database.php`:

```php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_NAME = 'sistema_requisiciones';
```

### Crear usuarios adicionales

Usa el panel de Administración → Usuarios → Crear Nuevo Usuario

---

## 🐛 Solución de Problemas

| Problema | Solución |
|----------|----------|
| "Base de datos no existe" | Ejecuta: `php setup_database.php` |
| "No se puede conectar a MySQL" | Verifica que MySQL está corriendo en XAMPP |
| "No tienes permiso" | Comprueba que tienes el rol correcto |
| "Error 404" | Verifica que la ruta es `/ProyectoPHP/` |

---

## 📚 Documentación Adicional

- **SETUP.md** - Guía detallada de instalación
- **scripts/dev/** - Scripts de prueba y migración
- **backend/** - Código bien documentado

---

## 👥 Equipo de Desarrollo

Desarrollado por Diego López Citalán

---

## 📄 Licencia

Este proyecto es de uso privado para propósitos educativos y empresariales.

---

## 🚀 Versiones

**v1.0** - 5 de diciembre de 2025
- Sistema completo con 7 estados
- 5 roles funcionales
- 8 tablas optimizadas
- Interfaz moderna

---

## 📞 Soporte

Para preguntas, sugerencias o reportar bugs, contacta al equipo de desarrollo.

---

**Última actualización:** 5 de diciembre de 2025  
**Estado:** ✅ Producción lista
