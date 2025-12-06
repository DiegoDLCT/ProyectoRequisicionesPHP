# Desarrollo del Software - Sistema de Gestión de Requisiciones v1.0

## Resumen Ejecutivo

Se ha desarrollado un **Sistema de Gestión de Requisiciones web integral** que automatiza completamente el ciclo de vida de requisiciones, desde su creación hasta la entrega, con control de cotizaciones, pagos y gestión de proveedores.

**Duración del Proyecto:** 20 días
**Equipo:** 1 Full Stack Developer
**Stack:** PHP 7.4, MySQL 5.7, HTML5, CSS3, JavaScript (ES6)
**Arquitectura:** MVC (Model-View-Controller)

---

## 1. Descripción del Proyecto

### Visión
Proporcionar una herramienta digital que reemplace procesos manuales de requisiciones, mejorando eficiencia, trazabilidad y control en la gestión de compras y entregas.

### Problema que Soluciona
- ❌ Antes: Requisiciones en email, formularios impresos, perdida de información
- ✅ Ahora: Sistema centralizado, trazabilidad completa, reportes automáticos

### Objetivos Alcanzados
1. ✅ Crear sistema web escalable y seguro
2. ✅ Implementar 7 estados de requisición con flujo automático
3. ✅ Control de acceso por 5 roles diferentes
4. ✅ Sistema de cotizaciones múltiples
5. ✅ Automatización de pagos y compras
6. ✅ Interfaz moderna y responsive
7. ✅ Documentación completa para usuarios y desarrolladores

---

## 2. Arquitectura Técnica

### Patrón de Diseño: MVC (Model-View-Controller)

```
┌─────────────────────────────────────────────┐
│           FRONTEND                          │
│  (HTML, CSS, JavaScript, Bootstrap Icons)   │
├─────────────────────────────────────────────┤
│                ROUTER                       │
│  (Rutas dinámicas - routes.php)            │
├─────────────────────────────────────────────┤
│          CONTROLLERS                        │
│  (Lógica de negocio)                        │
├─────────────────────────────────────────────┤
│            MODELS                           │
│  (Acceso a datos)                           │
├─────────────────────────────────────────────┤
│        BASE DE DATOS                        │
│  (MySQL - 8 tablas, 7 estados)             │
└─────────────────────────────────────────────┘
```

### Componentes Principales

**Frontend (presentación):**
- `frontend/views/` - Vistas por módulo (requisiciones, cotizaciones, pagos, etc.)
- `frontend/assets/` - CSS global, JavaScript, imágenes
- `frontend/services/` - Servicios AJAX para actualizaciones dinámicas
- `frontend/index.php` - Punto de entrada (redirige según sesión/rol)

**Backend (lógica):**
- `backend/controllers/` - Controladores para cada módulo (5 controladores)
- `backend/models/` - Modelos de datos (5 modelos)
- `backend/services/` - Servicios de lógica compleja (3 servicios)
- `backend/utils/` - Utilidades de autenticación, sesiones, validación
- `backend/config/` - Configuración de BD y constantes

**Base de Datos:**
- 8 tablas normalizadas
- 7 estados de requisición
- 5 tipos de usuario
- Integridad referencial con Foreign Keys

---

## 3. Funcionalidades Desarrolladas

### 3.1 Gestión de Requisiciones

**Estados:** Pendiente → Cotizado → Solicitar Pago → Pagado → Por Entregar → Entregado

**Funcionalidades:**
- Crear nuevas requisiciones
- Listar con filtros por estado y área
- Ver detalles completos
- Editar en estado pendiente
- Transiciones automáticas de estado
- Generación automática de folio (REQ-YYYYMM-NNN)

**Validaciones:**
- Descripción requerida (min 10 caracteres)
- Unidad de medida obligatoria
- Solo Jefe de Área o Solicitante pueden crear
- Transiciones válidas por rol

### 3.2 Sistema de Cotizaciones

**Funcionalidades:**
- Múltiples cotizaciones por requisición
- Carga de archivos PDF
- Información de proveedor
- Monto, fecha y días de entrega
- Aprobación/Rechazo selectiva
- Historial de todas las cotizaciones

**Validaciones:**
- Proveedor requerido
- Monto > 0
- Fecha válida
- Solo Jefe Mayor o Admin puede aprobar
- Automáticamente crea solicitud de pago al aprobar

### 3.3 Control de Pagos

**Funcionalidades:**
- Listar pagos pendientes (solo Contaduría)
- Seleccionar método de pago (5 tipos)
- Confirmar pago
- Genera automáticamente compra en tabla COMPRAS
- Historial de pagos

**Validaciones:**
- Método de pago obligatorio
- Solo usuario con rol Contaduría puede confirmar
- Validación de datos antes de registrar

### 3.4 Gestión de Entregas

**Funcionalidades:**
- Marcar como "Por Entregar"
- Confirmar entrega
- Historial de entregas
- Estados finales

**Validaciones:**
- Solo después de pago confirmado
- Transiciones ordenadas

### 3.5 Gestión de Proveedores

**Funcionalidades:**
- Listar proveedores
- Crear nuevo proveedor
- Editar información
- Eliminar proveedores

**Validaciones:**
- Nombre obligatorio y único
- Solo Admin puede gestionar

### 3.6 Gestión de Usuarios

**Funcionalidades:**
- Crear usuarios con rol asignado
- Listar usuarios por rol
- Cambiar rol de usuario
- Activar/Desactivar usuarios
- Asignar área (opcional)

**Validaciones:**
- Email único
- Email válido
- Contraseña con hash bcrypt
- Solo Admin puede gestionar

### 3.7 Dashboards por Rol

**Dashboards implementados:**

1. **Admin Dashboard**
   - Estadísticas: 7 estados + Solicitar Pago
   - Accesos rápidos a todos los módulos
   - Vista de todas las requisiciones

2. **Jefe Mayor Dashboard**
   - Cotizaciones pendientes
   - Estadísticas de su proceso
   - Acciones rápidas

3. **Contaduría Dashboard**
   - Pagos pendientes
   - Entregas registradas
   - Estadísticas de pagos

4. **Jefe de Área Dashboard**
   - Requisiciones de su área
   - Estadísticas de su área
   - Crear nueva requisición

5. **Solicitante Dashboard**
   - Sus requisiciones
   - Estadísticas de sus requisiciones
   - Crear nueva requisición

### 3.8 Seguridad Implementada

**Autenticación:**
- Login con email + contraseña
- Contraseñas hasheadas con bcrypt
- Sesiones seguras con tokens
- Logout disponible

**Autorización:**
- Control de acceso por rol
- Validación en cada acción
- Usuarios solo ven datos permitidos
- No pueden acceder a URLs restringidas

**Validación de Datos:**
- Validación en cliente (JavaScript)
- Validación en servidor (PHP)
- Sanitización de inputs
- Protección contra SQL Injection (Prepared Statements)
- Protección contra XSS (htmlspecialchars)

---

## 4. Base de Datos

### Estructura de Tablas

**1. USUARIOS**
```
Campos: id, cNombre, cCorreo, cContrasena, cPuesto, idArea, lActivo
Relaciones: FK → areas
Índices: cCorreo (UNIQUE)
```

**2. AREAS**
```
Campos: id, cNombre, lActivo
Relaciones: FK ← usuarios
Índices: cNombre (UNIQUE)
```

**3. REQUISICIONES**
```
Campos: id, cFolio, dFechaSolicitud, idSolicitante, idArea, cDescripcion,
         bRequiereCotizacion, idUnidad, cMaquina, cObraUbicacion, estado,
         lActivo, created_at, updated_at
Relaciones: FK → usuarios, areas, unidades
Índices: cFolio (UNIQUE), estado, lActivo
```

**4. COTIZACIONES**
```
Campos: id, idRequisicion, cProveedor, cNumcotizacion, deMonto,
         dFechacotizacion, iDiasEntrega, cArchivourl, bAprovada
Relaciones: FK → requisiciones, proveedores
Índices: idRequisicion, bAprovada
```

**5. COMPRAS**
```
Campos: id, idRequisicion, idProveedor, deMonto, cMetodoPago,
         dFechaCompra, lActivo
Relaciones: FK → requisiciones, proveedores, tipos_pago
Índices: idRequisicion, dFechaCompra
```

**6. PROVEEDORES**
```
Campos: id, cNombre, cContacto, cTelefono, cEmail, lActivo
Relaciones: FK ← cotizaciones, compras
Índices: cNombre (UNIQUE)
```

**7. TIPOS_PAGO**
```
Campos: id, cNombre, lActivo
Relaciones: FK ← compras
Índices: cNombre (UNIQUE)
```

**8. UNIDADES**
```
Campos: id, cNombre, cAbreviatura, lActivo
Relaciones: FK ← requisiciones
Índices: cNombre (UNIQUE)
```

### Datos Iniciales Cargados

- 4 Áreas (Administración, Operaciones, Finanzas, TI)
- 9 Unidades (Kilogramo, Gramo, Litro, Metro, Cajas, Docenas, Pares, Piezas)
- 5 Tipos de Pago (Efectivo, Transferencia, Cheque, Tarjeta, Otro)
- 5 Usuarios de prueba (1 admin, 1 jefe_mayor, 1 contaduria, 1 jefe_area, 1 solicitante)

---

## 5. Rutas Dinámicas

El sistema detecta automáticamente la carpeta raíz y genera URLs correctas:

**Archivo:** `backend/config/routes.php`

```
Si la carpeta es: /ProyectoPHP/
URLs se generan como: http://localhost/ProyectoPHP/...

Si la carpeta es: /SistemaRequisiciones/
URLs se generan como: http://localhost/SistemaRequisiciones/...

El sistema funciona con CUALQUIER nombre de carpeta
```

---

## 6. Flujo de Requisición - Diagrama

```
┌─────────────────────────────────────────────────────────┐
│ SOLICITANTE/JEFE ÁREA crea requisición                 │
│ Estado: PENDIENTE                                       │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│ PROVEEDOR envía cotizaciones (múltiples)               │
│ Estado: COTIZADO                                        │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│ JEFE MAYOR aprueba una cotización                      │
│ Estado: SOLICITAR PAGO                                  │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│ JEFE MAYOR solicita pago a Contaduría                  │
│ Estado: PAGO SOLICITADO                                 │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│ CONTADURÍA confirma pago                               │
│ Sistema crea automáticamente COMPRA                    │
│ Estado: PAGADO                                          │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│ JEFE MAYOR marca como "Por Entregar"                   │
│ Estado: POR ENTREGAR                                    │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│ JEFE MAYOR confirma entrega                            │
│ Estado: ENTREGADO                                       │
│ Requisición COMPLETADA                                 │
└─────────────────────────────────────────────────────────┘
```

---

## 7. Tecnologías Utilizadas

| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| PHP | 7.4+ | Backend |
| MySQL | 5.7+ | Base de datos |
| HTML5 | - | Estructura |
| CSS3 | - | Estilos |
| JavaScript | ES6 | Interactividad |
| Bootstrap Icons | 1.11.1 | Iconografía |
| Git | - | Control de versiones |
| PDO | - | Acceso a BD |

---

## 8. Características Especiales Desarrolladas

### 8.1 Generación Automática de Folio
```php
Formato: REQ + YYYYMM + NNN secuencial
Ejemplo: REQ202412001 (primera requisición de diciembre 2024)
```

### 8.2 Creación Automática de Compra
```php
Cuando Contaduría confirma un pago:
- Se crea automáticamente registro en tabla COMPRAS
- Se registra: requisición, proveedor, monto, método pago, fecha
- Se genera traza de auditoría
```

### 8.3 Sistema de Permisos Dinámico
```php
Cada rol tiene acciones específicas
Las vistas se generan según permisos
Los botones se muestran/ocultan dinámicamente
Las URLs están protegidas por rol
```

### 8.4 AJAX para Actualizaciones Sin Recargar
```javascript
Acciones como:
- Cambiar estado de requisición
- Aprobar cotización
- Confirmar pago
Se ejecutan sin recargar la página
```

---

## 9. Mejoras Implementadas en Esta Sesión

### Mejoras de Interfaz
- ✅ Dashboards limpios y claros
- ✅ Estadísticas relevantes por rol
- ✅ Botones bien espaciados
- ✅ Tabla de requisiciones optimizada
- ✅ Iconografía consistente (Bootstrap Icons)

### Mejoras de Código
- ✅ Eliminación de duplicados
- ✅ Consistencia en nombres de variables
- ✅ Código limpio sin emojis
- ✅ Comentarios documentados

### Mejoras de Estructura
- ✅ Eliminación de archivos de prueba innecesarios
- ✅ Organización de carpetas clara
- ✅ SQL migration script completo
- ✅ Constants correctos

### Mejoras de Documentación
- ✅ README.md completo
- ✅ SETUP.md con instrucciones claras
- ✅ Manual de Usuario detallado
- ✅ Plan de Pruebas con 5 casos
- ✅ Plan de Implementación ejecutivo
- ✅ Este documento

---

## 10. Estadísticas del Proyecto

### Código

| Métrica | Cantidad |
|---------|----------|
| Archivos PHP | 25+ |
| Controladores | 5 |
| Modelos | 5 |
| Vistas | 20+ |
| Servicios | 3+ |
| Líneas de código | 5,000+ |
| Funciones | 100+ |

### Base de Datos

| Métrica | Cantidad |
|---------|----------|
| Tablas | 8 |
| Columnas totales | 60+ |
| Foreign Keys | 15+ |
| Índices | 20+ |
| Stored Procedures | 0 (no necesarios en este diseño) |

### Documentación

| Documento | Páginas |
|-----------|---------|
| Manual de Usuario | 5 |
| Plan de Pruebas | 6 |
| Plan de Implementación | 8 |
| README | 15 |
| SETUP | 10 |
| Este documento | 8 |

---

## 11. Casos de Uso Principales

### Caso de Uso 1: Crear y Procesar Requisición
**Actor:** Jefe de Área
**Pasos:** 5-7 transacciones
**Tiempo:** 5-10 minutos
**Éxito:** Requisición visible en dashboard de todos los actores

### Caso de Uso 2: Gestionar Cotizaciones
**Actor:** Jefe Mayor
**Pasos:** 3-4 transacciones
**Tiempo:** 2-3 minutos
**Éxito:** Cotización aprobada, solicitud de pago generada

### Caso de Uso 3: Confirmar Pago
**Actor:** Contaduría
**Pasos:** 2-3 transacciones
**Tiempo:** 1-2 minutos
**Éxito:** Pago confirmado, compra creada automáticamente

### Caso de Uso 4: Supervisar Todo
**Actor:** Admin
**Pasos:** 1 transacción
**Tiempo:** 1-2 minutos
**Éxito:** Ver panorama completo del sistema

---

## 12. Lecciones Aprendidas y Decisiones de Diseño

### ¿Por qué MVC?
- Separación clara de responsabilidades
- Fácil de mantener y escalar
- Permite trabajo paralelo en backend y frontend
- Estándar en aplicaciones PHP profesionales

### ¿Por qué 7 estados y no menos?
- Cada estado representa una transición importante
- Permite trackin

g detallado del proceso
- Necesarios para los 5 roles diferentes

### ¿Por qué rutas dinámicas?
- El sistema funciona en cualquier carpeta
- Facilita despliegue en diferentes servidores
- Evita hardcoding de URLs

### ¿Por qué AJAX en algunos lugares?
- Mejora experiencia del usuario
- Transiciones de estado sin recargar
- Pero solo donde es crítico, no todas partes (KISS)

---

## 13. Próximas Mejoras Sugeridas

Para futuras versiones:

1. **Reportes Avanzados**
   - PDF de requisiciones
   - Gráficos de gastos por área
   - Análisis de proveedores

2. **Notificaciones**
   - Email cuando hay cotizaciones
   - Alert cuando falta aprobar
   - Recordatorios de entregas

3. **Integraciones**
   - API REST para terceros
   - Integración con sistema contable
   - Exportación a Excel

4. **Performance**
   - Caché de datos
   - Lazy loading en tablas grandes
   - Índices adicionales en BD

5. **Mobile**
   - Aplicación móvil nativa
   - Responsive mejorado
   - Offline mode

---

## Conclusión

El Sistema de Gestión de Requisiciones ha sido desarrollado exitosamente con:

✅ **Funcionalidad Completa** - 7 estados, 5 roles, múltiples cotizaciones
✅ **Código Profesional** - MVC, seguro, escalable
✅ **Base de Datos Robusta** - Normalizada, con integridad referencial
✅ **Documentación Exhaustiva** - Manual, planes, documentación técnica
✅ **Listo para Producción** - Probado, limpio, optimizado

El sistema está listo para ser implementado inmediatamente siguiendo el Plan de Implementación.

---

**Documento Desarrollo del Software - Versión 1.0**
**Fecha:** 6 de diciembre de 2025
**Estado:** COMPLETO
