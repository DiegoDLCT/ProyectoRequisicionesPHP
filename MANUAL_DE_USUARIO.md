# Manual de Usuario - Sistema de Gestión de Requisiciones v1.0

## Tabla de Contenidos
1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Roles y Funciones](#roles-y-funciones)
4. [Flujo de Requisiciones](#flujo-de-requisiciones)
5. [Módulos Principales](#módulos-principales)
6. [Preguntas Frecuentes](#preguntas-frecuentes)

---

## Introducción

El Sistema de Gestión de Requisiciones es una herramienta web integral diseñada para automatizar el proceso completo de requisiciones, cotizaciones, pagos y entregas en la organización.

**Características principales:**
- Gestión de requisiciones con 7 estados
- Sistema de cotizaciones múltiples
- Control de pagos y entregas
- Gestión de proveedores
- 5 roles con permisos específicos
- Interfaz moderna y responsiva

---

## Acceso al Sistema

### Credenciales de Acceso

```
Email: admin@admin.com
Contraseña: password
```

### Pasos para Acceder

1. Abre tu navegador web
2. Ve a: `http://localhost/ProyectoPHP/`
3. Se te redirigirá automáticamente a la pantalla de login
4. Ingresa tus credenciales
5. Haz clic en "Iniciar Sesión"

### Cambiar Contraseña

1. Desde tu dashboard, ve a Configuración
2. Selecciona "Cambiar Contraseña"
3. Ingresa la contraseña actual
4. Ingresa la nueva contraseña (dos veces para confirmar)
5. Haz clic en "Guardar"

---

## Roles y Funciones

### 1. Administrador (Admin)
**Acceso:** Completo a todo el sistema

**Funciones:**
- Ver todas las requisiciones en cualquier estado
- Crear usuarios y gestionar permisos
- Acceder a reportes y estadísticas
- Gestionar proveedores
- Supervisar todo el proceso de requisiciones

**Dashboard del Admin:**
- Estadísticas de todas las requisiciones
- Acceso rápido a todos los módulos
- Botones de acción principales

### 2. Jefe Mayor
**Acceso:** Aprobación de cotizaciones y solicitud de pagos

**Funciones:**
- Ver todas las requisiciones cotizadas
- Aprobar o rechazar cotizaciones
- Solicitar pagos a Contaduría
- Ver estado de pagos

**Dashboard del Jefe Mayor:**
- Requisiciones en estado "Cotizado"
- Estadísticas de cotizaciones aprobadas
- Acciones rápidas para aprobar cotizaciones

### 3. Contaduría
**Acceso:** Confirmación de pagos y gestión de entregas

**Funciones:**
- Ver requisiciones con pago solicitado
- Confirmar pagos
- Registrar método de pago
- Marcar como por entregar
- Ver historial de pagos

**Dashboard de Contaduría:**
- Pagos pendientes de confirmar
- Entregas registradas
- Estadísticas de pagos

### 4. Jefe de Área
**Acceso:** Crear requisiciones de su área

**Funciones:**
- Crear nuevas requisiciones
- Ver requisiciones de su área
- Consultar cotizaciones
- Ver estado de entregas

**Dashboard del Jefe de Área:**
- Sus requisiciones activas
- Estadísticas del área
- Opciones para crear requisiciones

### 5. Solicitante
**Acceso:** Crear requisiciones personales

**Funciones:**
- Crear nuevas requisiciones
- Ver sus requisiciones
- Consultar cotizaciones de sus requisiciones
- Ver estado de entregas

**Dashboard del Solicitante:**
- Sus requisiciones personales
- Estadísticas de sus requisiciones
- Botón para crear nueva requisición

---

## Flujo de Requisiciones

### Estados y Transiciones

```
1. PENDIENTE
   ↓
   Esperando que se reciban cotizaciones

2. COTIZADO
   ↓
   Jefe Mayor revisa y aprueba una cotización

3. SOLICITAR PAGO
   ↓
   Se solicita al departamento de Contaduría

4. PAGO SOLICITADO
   ↓
   Contaduría confirma el pago y selecciona método

5. PAGADO
   ↓
   Sistema registra automáticamente en COMPRAS

6. POR ENTREGAR
   ↓
   Se preparan los materiales para entrega

7. ENTREGADO
   ↓
   Se confirma la entrega y se cierra la requisición
```

### Ejemplo Práctico

**Paso 1: Crear Requisición (Jefe de Área o Solicitante)**
1. Ve a "Crear Requisición"
2. Completa los campos:
   - Descripción del producto/servicio
   - Unidad de medida (kilogramos, cajas, piezas, etc.)
   - Máquina/Equipo (si aplica)
   - Ubicación de la obra
3. Haz clic en "Crear Requisición"
4. Estado: PENDIENTE

**Paso 2: Enviar Cotizaciones**
- Los proveedores envían cotizaciones
- Se cargan en el sistema con monto y fecha de entrega
- Estado: COTIZADO

**Paso 3: Aprobar Cotización (Jefe Mayor)**
1. Ve a "Cotizaciones"
2. Selecciona la requisición
3. Revisa las cotizaciones recibidas
4. Haz clic en "Aprobar" en la cotización elegida
5. Estado: SOLICITAR PAGO

**Paso 4: Confirmar Pago (Contaduría)**
1. Ve a "Pagos Pendientes"
2. Selecciona la requisición
3. Elige el método de pago
4. Confirma el pago
5. Estado: PAGADO (se crea automáticamente registro en COMPRAS)

**Paso 5: Marcar Entrega**
1. Se preparan los materiales
2. El Jefe Mayor marca como "Por Entregar"
3. Se confirma la entrega
4. Estado: ENTREGADO

---

## Módulos Principales

### Módulo de Requisiciones

**Crear Requisición**
- Acceso: Solicitante, Jefe de Área
- Pasos:
  1. Haz clic en "Nueva Requisición"
  2. Completa la descripción
  3. Selecciona unidad de medida
  4. Ingresa ubicación
  5. Haz clic en "Crear"

**Listar Requisiciones**
- Acceso: Todos los roles
- Muestra:
  - Folio de requisición
  - Estado actual
  - Fecha de solicitud
  - Área responsable
  - Botones de acción según permisos

**Ver Detalles**
- Acceso: Todos
- Muestra información completa y cotizaciones

### Módulo de Cotizaciones

**Subir Cotización**
- Acceso: Jefe Mayor, Admin
- Campos:
  - Proveedor
  - Número de cotización
  - Monto
  - Fecha de cotización
  - Días de entrega
  - Archivo PDF (opcional)

**Aprobar Cotización**
- Acceso: Jefe Mayor, Admin
- Acción: Selecciona la cotización a aprobar
- Resultado: Requisición pasa a estado "Solicitar Pago"

### Módulo de Pagos

**Solicitar Pago**
- Acceso: Jefe Mayor
- Se ejecuta al aprobar una cotización

**Confirmar Pago**
- Acceso: Contaduría
- Selecciona método de pago
- Confirma el pago

**Ver Pagos**
- Acceso: Todos
- Historial de todos los pagos realizados

### Módulo de Proveedores

**Listar Proveedores**
- Acceso: Admin, Jefe Mayor
- Muestra: Todos los proveedores registrados

**Agregar Proveedor**
- Acceso: Admin
- Campos: Nombre, contacto, información adicional

**Editar/Eliminar**
- Acceso: Admin

### Módulo de Usuarios

**Listar Usuarios**
- Acceso: Admin
- Muestra: Todos los usuarios del sistema

**Crear Usuario**
- Acceso: Admin
- Campos: Nombre, email, rol, área

**Cambiar Rol**
- Acceso: Admin
- Permite cambiar el rol de un usuario

---

## Preguntas Frecuentes

### ¿Cómo creo una nueva requisición?
Ve a "Requisiciones" → "Crear Requisición", completa los datos y haz clic en "Crear".

### ¿Quién aprueba las cotizaciones?
El Jefe Mayor revisa y aprueba las cotizaciones. El Admin también tiene esta función.

### ¿Qué sucede cuando se confirma un pago?
El sistema registra automáticamente la compra en el módulo de compras y la requisición pasa a estado "Pagado".

### ¿Puedo editar una requisición después de crearla?
Depende del estado. Las requisiciones en estado "Pendiente" pueden editarse. Una vez cotizadas, no se pueden editar.

### ¿Cómo veo el historial de pagos?
Ve a "Pagos" → "Ver Historial" para ver todos los pagos registrados.

### ¿Qué significa cada estado?
- **Pendiente**: Esperando cotizaciones
- **Cotizado**: Hay cotizaciones disponibles
- **Solicitar Pago**: Cotización aprobada, listo para pagar
- **Pago Solicitado**: En proceso de pago
- **Pagado**: Pago confirmado
- **Por Entregar**: En tránsito
- **Entregado**: Completado

### ¿Quién tiene acceso a las requisiciones?
Cada usuario ve solo lo que su rol permite:
- Admin: Todo
- Jefe Mayor: Todas las requisiciones
- Contaduría: Requisiciones en estado de pago
- Jefe de Área: Requisiciones de su área
- Solicitante: Sus propias requisiciones

---

## Soporte

Para preguntas o problemas técnicos, contacta al equipo de desarrollo.

**Última actualización:** 6 de diciembre de 2025
**Versión:** 1.0
