# SIMP — Sistema de Inventario de Materia Prima
## Documentación de Negocio

---

## 1. Credenciales de acceso

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@simp.com | `Admin123!` |
| Gerente de Almacén | gerente@simp.com | `Pass1234!` |
| Empleado de Almacén | empleado@simp.com | `Pass1234!` |
| Producción | produccion@simp.com | `Pass1234!` |
| Control de Calidad | calidad@simp.com | `Pass1234!` |
| Compras | compras@simp.com | `Pass1234!` |

---

## 2. Roles y permisos

| Sección | admin | warehouse_manager | warehouse_clerk | production | quality | purchasing |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Usuarios | ✓ | — | — | — | — | — |
| Categorías / Unidades / Ubicaciones | ✓ | ✓ | — | — | — | — |
| Proveedores | ✓ | ✓ | — | — | — | ✓ |
| Productos (ver) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Productos (crear/editar) | ✓ | ✓ | — | — | — | — |
| Recepciones | ✓ | ✓ | ✓ | — | — | — |
| Despachos (crear) | ✓ | ✓ | ✓ | ✓ | — | — |
| Despachos (surtir) | ✓ | ✓ | ✓ | — | — | — |
| Despachos (ver) | ✓ | ✓ | ✓ | ✓ | ✓ | — |
| Ajustes (crear) | ✓ | ✓ | ✓ | — | — | — |
| Ajustes (aprobar/rechazar) | ✓ | ✓ | — | — | — | — |
| Alertas | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Reportes | ✓ | ✓ | — | — | ✓ | ✓ |
| Órdenes de compra | ✓ | ✓ | — | — | — | ✓ |

---

## 3. Dashboard

Al ingresar al sistema todos los usuarios ven el panel con:

- **Alertas críticas activas** — lotes vencidos o stock en cero
- **Productos con stock bajo** — por debajo del mínimo configurado
- **Productos activos** — total de productos en catálogo
- **Recepciones del día** — cuántas entradas se registraron hoy
- **Últimos 10 movimientos** — historial reciente de entradas y salidas
- **Últimas 5 alertas** — priorizadas por nivel (critical > warning > info)

---

## 4. Catálogos

Antes de operar el sistema se configuran los catálogos base.

### 4.1 Categorías
Agrupan los productos por tipo. Ejemplo de categorías del sistema:

| Nombre | Perecedero |
|---|---|
| Harina y Cereales | No |
| Lacteos | Sí |
| Aceites y Grasas | No |
| Azucares y Endulzantes | No |
| Condimentos y Especias | No |

### 4.2 Unidades de Medida
Define cómo se mide cada producto (kg, L, pz, saco, etc.)

### 4.3 Ubicaciones
Espacios físicos del almacén donde se guardan los productos:

| Código | Nombre | Tipo | Temp. |
|---|---|---|---|
| WARE-MAIN | Bodega General | Almacén | — |
| SHELF-A1 | Estante A1 | Estante | — |
| SHELF-A2 | Estante A2 | Estante | — |
| REFRIG-B1 | Refrigerador B1 | Refrigerador | 2°C a 8°C |
| FREEZE-C1 | Congelador C1 | Congelador | -20°C a -15°C |

### 4.4 Proveedores
Empresas que suministran la materia prima. Incluye: código, nombre, contacto, teléfono, email y RFC/NIT.

### 4.5 Productos
Catálogo de materias primas. Cada producto tiene:
- Código único y nombre
- Categoría y unidad de medida
- Código de barras (para escaneo)
- Stock mínimo y máximo
- Ubicación predeterminada
- Días de vida útil (shelf life)
- Proveedor preferido y costo unitario

---

## 5. Flujo de Recepciones (entrada de stock)

**Quién puede:** admin, warehouse_manager, warehouse_clerk

### Paso 1 — Crear la recepción

1. Ir a **Operaciones → Recepciones → Nueva Recepción**
2. Seleccionar el **proveedor**
3. Ingresar la **fecha y hora de llegada**
4. Agregar líneas de productos:
   - Con el botón **"Agregar Línea"** manualmente, o
   - Escaneando el **código de barras** del producto (el campo de escaneo está arriba de las líneas — el lector escribe el código y presiona Enter automáticamente)
5. Por cada línea llenar:
   - **Producto** — seleccionar del catálogo
   - **Cantidad recibida** — lo que físicamente llegó
   - **Cantidad esperada** — lo que decía la orden (opcional, detecta discrepancias)
   - **Número de lote** — identificador del lote del proveedor
   - **Fecha de vencimiento** — obligatoria para perecederos
   - **Costo unitario** — precio de compra (opcional)
   - **Ubicación** — dónde se va a guardar
6. Hacer clic en **"Crear Recepción"**

> El stock **no cambia** en este paso. La recepción queda en estado `Pendiente`.

> Si la cantidad recibida < esperada, el estado cambia a `Con discrepancias`. Igual se puede confirmar.

### Paso 2 — Confirmar la recepción

1. Abrir la recepción creada
2. Revisar las líneas y discrepancias si las hay
3. Hacer clic en **"Confirmar Recepción"** → aparece un modal de confirmación
4. Confirmar

**Lo que ocurre al confirmar:**
- Se crea un **Lote** por cada línea (con número de lote, vencimiento, ubicación)
- Se genera un **Movimiento** de tipo `receipt` (inmutable, no se puede borrar)
- El `current_stock` del producto **sube** automáticamente
- La recepción pasa a estado `Completado`

> Una recepción confirmada **no puede volver a confirmarse**.

### Estados de una recepción

| Estado | Descripción |
|---|---|
| `Pendiente` | Creada, sin confirmar |
| `Con discrepancias` | Cantidad recibida < esperada |
| `Completado` | Confirmada, stock actualizado |

---

## 6. Flujo de Despachos (salida de stock)

**Quién puede crear:** admin, warehouse_manager, warehouse_clerk, production
**Quién puede surtir:** admin, warehouse_manager, warehouse_clerk

### Paso 1 — Crear el despacho

1. Ir a **Operaciones → Despachos → Nuevo Despacho**
2. Ingresar **destino** (área o proceso que solicita)
3. Agregar líneas: producto y cantidad solicitada
4. Guardar → estado `Pendiente`

> El stock **no cambia** en este paso.

### Paso 2 — Surtir el despacho

1. Abrir el despacho
2. Hacer clic en **"Surtir Despacho"** → modal de confirmación
3. Confirmar

**Lo que ocurre al surtir:**
- El sistema aplica **FEFO** (First Expired, First Out): descuenta primero del lote con fecha de vencimiento más próxima
- Se genera un **Movimiento** de tipo `dispatch` por cada lote consumido
- El `current_stock` del producto **baja** automáticamente
- Si no hay stock suficiente → **el sistema bloquea la operación**

### Paso alternativo — Cancelar

- Solo disponible mientras el despacho está en `Pendiente`
- Modal de confirmación antes de cancelar

### Estados de un despacho

| Estado | Descripción |
|---|---|
| `Pendiente` | Solicitado, sin surtir |
| `Surtido` | Entregado completamente |
| `Parcial` | Entregado parcialmente |
| `Cancelado` | Anulado |

---

## 7. Flujo de Ajustes de Inventario

**Quién puede crear:** admin, warehouse_manager, warehouse_clerk
**Quién puede aprobar:** admin, warehouse_manager (y debe ser una persona diferente al creador)

Sirve para corregir diferencias entre lo que dice el sistema y lo que hay físicamente.

### Paso 1 — Crear el ajuste

1. Ir a **Operaciones → Ajustes → Nuevo Ajuste**
2. Seleccionar el **tipo**:
   - **Conteo físico** — diferencia detectada en inventario
   - **Merma** — producto dañado o perdido
   - **Devolución** — regresa stock al almacén
   - **Corrección** — error de captura
3. Ingresar el **motivo**
4. Agregar líneas: producto, lote, cantidad en sistema (automática), cantidad física real
5. La **varianza** se calcula sola: `física - sistema`
6. Guardar → estado `Borrador`

> El stock **no cambia** en este paso.

### Paso 2 — Aprobar el ajuste

1. Un usuario diferente al que creó el ajuste lo abre
2. Revisa las líneas y varianzas
3. Hace clic en **"Aprobar Ajuste"** → modal de confirmación

**Lo que ocurre al aprobar:**
- Si varianza > 0: movimiento `positive_adjustment` → stock sube
- Si varianza < 0: movimiento `negative_adjustment` → stock baja
- El ajuste pasa a estado `Aprobado`

> **Regla de doble control:** el creador del ajuste **no puede aprobarlo él mismo**.

### Paso alternativo — Rechazar

- El aprobador puede rechazar si encuentra errores
- El stock no se modifica
- Estado pasa a `Rechazado`

### Estados de un ajuste

| Estado | Descripción |
|---|---|
| `Borrador` | Creado, pendiente de aprobación |
| `Aprobado` | Aplicado al inventario |
| `Rechazado` | No aplicado |

---

## 8. Órdenes de Compra

**Quién puede:** admin, warehouse_manager, purchasing

Son documentos de solicitud al proveedor. **No mueven stock directamente.**

### Flujo

1. Crear orden con proveedor, productos y cantidades
2. Cambiar estado según avance: `draft → sent → confirmed → received`
3. Cuando llega la mercancía, crear una **Recepción** manualmente para que el stock suba

### Estados

| Estado | Descripción |
|---|---|
| `draft` | Borrador |
| `sent` | Enviada al proveedor |
| `confirmed` | Confirmada por el proveedor |
| `received` | Mercancía recibida |
| `cancelled` | Cancelada |

---

## 9. Sistema de Alertas

Las alertas se generan automáticamente. Aparecen en el badge rojo del menú lateral y en **Alertas**.

| Tipo | Nivel | Condición |
|---|---|---|
| Vencimiento en 7 días | Info | `expiry_date` entre hoy y +7 días |
| Vencimiento en 3 días | Warning | `expiry_date` entre hoy y +3 días |
| Vencimiento en 1 día | Critical | `expiry_date` = mañana o hoy |
| Lote vencido | Critical | `expiry_date` < hoy |
| Stock bajo | Warning | `current_stock` < `min_stock` |
| Sin stock | Critical | `current_stock` = 0 |

Los lotes vencidos también cambian su estado a `expired` automáticamente.

---

## 10. Reportes

**Quién puede:** admin, warehouse_manager, quality, purchasing

| Reporte | Contenido |
|---|---|
| Inventario | Stock actual por producto con filtros por categoría y estado |
| Movimientos | Historial de entradas y salidas con filtros por fecha, tipo y producto |
| Alertas | Historial de todas las alertas con filtros por nivel |

---

## 11. Reglas de negocio críticas

1. **Stock no puede ser negativo** — el sistema bloquea cualquier operación que intente dejarlo en negativo
2. **Movimientos son inmutables** — una vez creados no se pueden editar ni eliminar
3. **Confirmación única** — una recepción confirmada no se puede volver a confirmar
4. **Doble control en ajustes** — quien crea un ajuste no puede aprobarlo
5. **FEFO en despachos** — siempre se descuenta primero el lote que vence antes
6. **Folios automáticos** — el sistema genera folios secuenciales por año (ej. `REC-2026-0001`)
7. **Sin alertas duplicadas** — el sistema no crea dos alertas activas del mismo tipo para el mismo lote/producto

---

## 12. Datos de demostración (seeder)

Al correr `php artisan migrate:fresh --seed` el sistema carga:

**Categorías:** Harina y Cereales, Lacteos, Aceites y Grasas, Azucares y Endulzantes, Condimentos y Especias

**Ubicaciones:** Bodega General, Estante A1, Estante A2, Refrigerador B1, Congelador C1

**Proveedores:** Agricola Andina S.A., Productos del Valle Ltda., Alimentos Frescos Colombia

**Productos de muestra:**

| Código | Producto | Unidad | Stock mín. | Stock máx. |
|---|---|---|---|---|
| PROD-001 | Harina de Trigo | kg | 100 | 500 |
| PROD-002 | Leche Entera | L | 50 | 200 |
| PROD-003 | Aceite Vegetal | L | 30 | 150 |
| PROD-004 | Azucar Refinada | kg | 80 | 300 |
| PROD-005 | Sal de Mesa | kg | 20 | 100 |
| PROD-006 | Mantequilla | kg | 10 | 50 |
| PROD-007 | Harina de Maiz | kg | 50 | 250 |
| PROD-008 | Levadura Seca | kg | 5 | 30 |

> **Nota:** Los productos cargan con `current_stock = 0`. Para ver el sistema con stock real, crear y confirmar recepciones después del seed.
