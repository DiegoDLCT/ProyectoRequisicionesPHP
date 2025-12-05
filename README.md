# 📦 Sistema de Gestión de Requisiciones v1.0

Sistema web integral para la gestión digital de requisiciones, cotizaciones, pagos y entregas empresariales. Desarrollado con arquitectura MVC en PHP puro y MySQL.

---

## ✨ Características Principales

✅ **Gestión Completa de Requisiciones** - Ciclo completo de 7 estados  
✅ **Sistema de Cotizaciones** - Múltiples cotizaciones por requisición  
✅ **Control de Pagos** - Solicitud, confirmación y seguimiento  
✅ **Gestión de Entregas** - Preparación y confirmación de entregas  
✅ **Control de Usuarios por Rol** - 5 roles con permisos específicos  
✅ **Gestión de Proveedores** - Base de datos de proveedores  
✅ **Interfaz Minimalista** - Diseño moderno y responsivo  
✅ **Seguridad Avanzada** - Autenticación, autorización y validación  

---

## 🏗️ Arquitectura del Sistema

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

## 🚀 Inicio Rápido

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
   - Base de datos `requisiciones_db`
   - Todas las tablas
   - Usuario admin (admin@admin.com / password)

3. **Iniciar el proyecto**
   - Coloca la carpeta en `C:\xampp\htdocs\`
   - Accede a: `http://localhost/ProyectoPHP/`

### Credenciales de Prueba
- **Email:** admin@admin.com
- **Contraseña:** password

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
const DB_NAME = 'requisiciones_db';
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
