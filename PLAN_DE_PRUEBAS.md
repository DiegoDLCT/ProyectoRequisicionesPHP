# Plan de Pruebas - Sistema de Gestión de Requisiciones v1.0

## Objetivos del Plan de Pruebas

1. Validar que el sistema funciona correctamente en todos los módulos
2. Verificar que los flujos de requisiciones son correctos
3. Asegurar que los permisos por rol funcionan adecuadamente
4. Validar la integridad de datos en la base de datos
5. Confirmar la experiencia del usuario en los procesos principales

---

## 5 Pruebas Seleccionadas

### PRUEBA 1: Flujo Completo de Requisición

**Tipo:** Prueba de Integración - Crítica

**Descripción:**
Validar que una requisición pueda completar todo su ciclo de vida desde la creación hasta la entrega.

**Justificación:**
Esta es la prueba más importante porque valida el funcionamiento integral del sistema. Si falla, todo el sistema está comprometido. Prueba todos los módulos, roles y estados.

**Pasos:**

1. **Crear Requisición** (Jefe de Área)
   - Iniciar sesión como jefe_area@example.com
   - Ve a "Crear Requisición"
   - Ingresa: descripción="Material de oficina", unidad=2 (kilogramos)
   - Haz clic en "Crear"
   - Verifica que la requisición está en estado "PENDIENTE"

2. **Subir Cotización** (Jefe Mayor)
   - Inicia sesión como jefe_mayor@example.com
   - Ve a "Cotizaciones"
   - Busca la requisición recién creada
   - Carga cotización: proveedor="Office Depot", monto=5000, días=5
   - Verifica que la requisición está en estado "COTIZADO"

3. **Aprobar Cotización** (Jefe Mayor)
   - Desde "Cotizaciones", haz clic en "Aprobar"
   - La requisición debe pasar a "SOLICITAR PAGO"
   - Se genera automáticamente solicitud de pago

4. **Confirmar Pago** (Contaduría)
   - Inicia sesión como contaduria@example.com
   - Ve a "Pagos Pendientes"
   - Selecciona método de pago: "Transferencia Bancaria"
   - Haz clic en "Confirmar Pago"
   - Verifica que la requisición está en "PAGADO"
   - Verifica que se creó registro en tabla COMPRAS

5. **Marcar Entrega** (Jefe Mayor)
   - Inicia sesión como jefe_mayor@example.com
   - Ve a "Requisiciones"
   - Haz clic en "Por Entregar"
   - La requisición debe estar en estado "POR_ENTREGAR"

6. **Confirmar Entrega** (Jefe Mayor o Admin)
   - Haz clic en "Confirmar Entrega"
   - Verifica que la requisición está en estado "ENTREGADO"

**Resultado Esperado:** La requisición completa todo su ciclo correctamente

**Criterio de Éxito:** 
- Todos los estados se alcanzan correctamente
- Se crea registro en tabla COMPRAS
- No hay errores en transiciones de estado
- Los datos persisten en la base de datos

---

### PRUEBA 2: Validación de Permisos por Rol

**Tipo:** Prueba de Seguridad - Crítica

**Descripción:**
Verificar que cada rol solo puede acceder a las funciones y datos que le corresponden.

**Justificación:**
La seguridad y control de acceso es fundamental. Una brecha en permisos podría permitir que usuarios sin autorización modifiquen datos críticos o vean información confidencial.

**Pasos:**

1. **Solicitante intentando acceder a Usuarios**
   - Inicia sesión como solicitante
   - Intenta acceder a: `/ProyectoPHP/frontend/views/usuarios/`
   - Resultado: Debe ser redirigido o mostrar error de acceso

2. **Jefe de Área intentando aprobar cotizaciones**
   - Inicia sesión como jefe_area
   - Intenta acceder a módulo de "Aprobar Cotizaciones"
   - Resultado: Debe ser redirigido, no tiene permiso

3. **Contaduría viendo requisiciones de otra área**
   - Inicia sesión como contaduria
   - Ve "Pagos Pendientes"
   - Verifica que solo ve requisiciones en estado de pago, no todas

4. **Admin accediendo a todo**
   - Inicia sesión como admin
   - Verifica que puede acceder a todos los módulos
   - Verifica que ve todas las requisiciones independientemente del estado

5. **Solicitante solo viendo sus requisiciones**
   - Inicia sesión como solicitante
   - Ve "Mis Requisiciones"
   - Verifica que solo ve sus propias requisiciones, no todas

**Resultado Esperado:** Cada rol tiene exactamente los permisos asignados

**Criterio de Éxito:**
- Solicitante: solo crea requisiciones y ve las suyas
- Jefe de Área: crea requisiciones de su área
- Jefe Mayor: ve todas, aprueba cotizaciones
- Contaduría: ve y confirma pagos
- Admin: acceso total

---

### PRUEBA 3: Validación de Datos - Estados de Requisición

**Tipo:** Prueba de Datos - Alta Prioridad

**Descripción:**
Verificar que los 7 estados de requisición se almacenan correctamente en la base de datos y que las transiciones son válidas.

**Justificación:**
Los estados son el corazón del sistema. Si un estado se guarda incorrectamente o se permite una transición inválida, el flujo se rompe. Esta prueba valida la integridad de datos.

**Pasos:**

1. **Verificar tabla REQUISICIONES**
   - Abre phpMyAdmin o cliente MySQL
   - Ve a base de datos `sistema_requisiciones`
   - Tabla: `requisiciones`
   - Crea 7 requisiciones, una por estado:
     - REQ-001: pendiente
     - REQ-002: cotizado
     - REQ-003: solicitar_pago
     - REQ-004: pago_solicitado
     - REQ-005: pagado
     - REQ-006: por_entregar
     - REQ-007: entregado

2. **Verificar que los estados son exactos**
   - Busca en la tabla: `SELECT DISTINCT estado FROM requisiciones`
   - Resultado esperado: 7 filas con los estados listados arriba
   - NO debe existir estado "aprobado" (este fue un error que corregimos)

3. **Verificar transiciones en aplicación**
   - Una requisición en "pendiente" no puede pasar a "pagado"
   - Una requisición en "entregado" no puede volver a "pendiente"
   - Las transiciones siguen el flujo: pendiente → cotizado → solicitar_pago → pagado → por_entregar → entregado

4. **Verificar COTIZACIONES**
   - Ve a tabla `cotizaciones`
   - Verifica que campo `bAprovada` solo contiene 0 (no aprobada) o 1 (aprobada)
   - No debe haber valores inválidos

5. **Verificar integridad de COMPRAS**
   - Cuando se confirma un pago, se crea registro en `compras`
   - Verifica que el registro tiene todos los campos correctos
   - Verifica que la fecha de compra es correcta

**Resultado Esperado:** Todos los datos se almacenan correctamente

**Criterio de Éxito:**
- 7 estados válidos en requisiciones
- No hay estados inválidos
- Las transiciones respetan el flujo
- No hay registros huérfanos
- Integridad referencial correcta

---

### PRUEBA 4: Funcionalidad de Cotizaciones Múltiples

**Tipo:** Prueba Funcional - Alta Prioridad

**Descripción:**
Verificar que una requisición puede recibir múltiples cotizaciones de diferentes proveedores y que el sistema las gestiona correctamente.

**Justificación:**
La capacidad de recibir múltiples cotizaciones es un requisito clave del sistema. Si solo acepta una, pierde funcionalidad vital. Esta prueba valida que los usuarios pueden comparar opciones.

**Pasos:**

1. **Crear Requisición Base**
   - Crea requisición: "Computadoras de escritorio x5"
   - Estado: PENDIENTE

2. **Cargar 3 Cotizaciones Diferentes**
   - Cotización 1: Proveedor "Tech Solutions", $50,000, 7 días
   - Cotización 2: Proveedor "Computer Store", $48,000, 10 días
   - Cotización 3: Proveedor "Office Tech", $52,000, 5 días

3. **Verificar que aparecen todas**
   - Ve a "Ver Cotizaciones"
   - Verifica que las 3 cotizaciones aparecen listadas
   - Verifica montos correctos

4. **Aprobar una cotización**
   - Aprueba la cotización 2 ($48,000 - mejor precio)
   - Verifica que la requisición pasa a "SOLICITAR PAGO"

5. **Verificar estado de las otras**
   - Las cotizaciones 1 y 3 deben estar marcadas como "No Aprobada"
   - Deben seguir siendo visibles en el historial
   - No afectan el flujo de la requisición

**Resultado Esperado:** El sistema maneja correctamente múltiples cotizaciones

**Criterio de Éxito:**
- Se pueden cargar 3+ cotizaciones
- Se aprueban/rechazan correctamente
- El flujo continúa con la aprobada
- Las otras permanecen en historial
- Los montos se comparan fácilmente

---

### PRUEBA 5: Validación de Integración entre Módulos

**Tipo:** Prueba de Integración - Media Prioridad

**Descripción:**
Verificar que cuando ocurre un evento en un módulo, se actualiza correctamente en los otros (cascada de cambios).

**Justificación:**
El sistema debe ser coherente. Si confirmo un pago en Contaduría, debe reflejarse en el dashboard del Jefe Mayor, en reportes, etc. Esta prueba valida que los módulos están integrados.

**Pasos:**

1. **Requisición en estado PAGADO**
   - Crea y completa el flujo hasta "PAGADO"
   - Confirma el pago en Contaduría

2. **Verificar en Dashboard Admin**
   - Inicia sesión como admin
   - Ve dashboard
   - Estadística "Pagado" debe incrementar en 1
   - Requisición aparece en sección "Pagadas"

3. **Verificar en Dashboard Jefe Mayor**
   - Inicia sesión como jefe_mayor
   - Ve dashboard
   - Requisición aparece disponible para marcar entrega
   - Estadísticas actualizadas

4. **Verificar en Dashboard Contaduría**
   - Inicia sesión como contaduria
   - Requisición desaparece de "Pendientes"
   - Aparece en "Pagadas"

5. **Verificar en tabla COMPRAS**
   - MySQL: SELECT * FROM compras ORDER BY id DESC LIMIT 1
   - Verifica que existe el registro
   - Campos correctos: id_requisicion, monto, fecha

6. **Marcar entrega y verificar cascada**
   - Jefe Mayor marca "Por Entregar"
   - Dashboard Jefe Mayor se actualiza
   - Dashboard Admin se actualiza
   - Estadísticas se actualizan

**Resultado Esperado:** Todos los módulos se actualizan en sincronía

**Criterio de Éxito:**
- Dashboard se actualiza sin recargar (si está usando AJAX)
- Estadísticas son consistentes
- Tabla COMPRAS se genera correctamente
- Todas las vistas muestran datos consistentes
- No hay desincronización de datos

---

## Ambiente de Pruebas

**Base de Datos:** `sistema_requisiciones`
**PHP:** 7.4+
**MySQL:** 5.7+
**Navegador:** Chrome/Firefox/Edge (versiones recientes)

## Casos de Prueba Exitosos

Un caso de prueba es exitoso cuando:
1. Completa todos sus pasos sin errores
2. Cumple todos los criterios de éxito
3. No produce datos inconsistentes
4. No genera excepciones o warnings

## Reportes de Defectos

Si encuentra un defecto:
1. Documente pasos exactos para reproducirlo
2. Incluya mensajes de error
3. Anexe capturas de pantalla
4. Reporte en el repositorio

---

**Documento creado:** 6 de diciembre de 2025
**Versión:** 1.0
