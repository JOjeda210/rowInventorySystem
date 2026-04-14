# Documentación SIMP - Sistema de Inventario de Materia Prima

## 1. Introducción

SIMP es una aplicación web desarrollada en Laravel 11 para la gestión de inventario de materia prima. Permite un control detallado de entradas, salidas, lotes, proveedores, y genera alertas y reportes para una gestión eficiente.

## 2. Características Principales

- **Dashboard Interactivo:** KPIs y métricas clave.
- **Gestión de Catálogos:** Productos, categorías, unidades, locaciones, proveedores.
- **Control de Operaciones:** Recepciones, despachos, ajustes de inventario.
- **Trazabilidad por Lote:** Seguimiento FEFO (First-Expires, First-Out).
- **Alertas de Stock:** Notificaciones para niveles bajos o vencimiento.
- **Órdenes de Compra:** Gestión de compras a proveedores.
- **Roles y Permisos:** 6 roles predefinidos (Admin, Gerente, Empleado, etc.).
- **Reportes:** Inventario, movimientos, alertas.

## 3. Instalación

**Requisitos:**
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL o PostgreSQL

**Pasos:**
1.  Clonar el repositorio.
2.  `composer install`
3.  `npm install && npm run build`
4.  Crear archivo `.env` (copiar de `.env.example`).
5.  `php artisan key:generate`
6.  Configurar la base de datos en `.env`.
7.  `php artisan migrate --seed`
8.  `php artisan serve` (o usar Laravel Herd).

## 4. Configuración

El archivo `.env` controla la configuración principal:

- `DB_CONNECTION`: `mysql` o `pgsql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER`: Configuración para envío de correos.

## 5. Estructura de la Base de Datos

| Tabla | Descripción |
|---|---|
| `users` | Usuarios y roles |
| `categories` | Categorías de productos |
| `units_of_measure` | Unidades de medida (Kg, L, etc.) |
| `locations` | Ubicaciones de almacenamiento |
| `suppliers` | Proveedores de materia prima |
| `products` | Catálogo de productos |
| `lots` | Lotes de productos con fecha de vencimiento |
| `movements` | Historial de todos los movimientos de inventario |
| `receipts` / `receipt_lines` | Entradas de mercancía |
| `dispatches` / `dispatch_lines` | Salidas de mercancía |
| `stock_adjustments` / `adjustment_lines` | Ajustes manuales de stock |
| `alerts` | Alertas de stock bajo o vencimiento |
| `purchase_orders` / `purchase_order_lines` | Órdenes de compra |

## 6. Roles y Permisos

| Rol | Descripción |
|---|---|
| `admin` | Acceso total. |
| `warehouse_manager` | Gestiona catálogos, operaciones y aprueba ajustes. |
| `warehouse_clerk` | Ejecuta recepciones y despachos. |
| `purchasing` | Gestiona órdenes de compra y proveedores. |
| `production` | Solicita despachos de materia prima. |
| `quality` | Visualiza reportes y lotes. |

## 7. Componentes Clave

### Modelos (en `app/Models/`)
Cada tabla tiene un modelo Eloquent con sus relaciones definidas (ej: `Product`, `Lot`, `User`).

### Controllers (en `app/Http/Controllers/`)
- **Admin:** `UserController`
- **Catalog:** `CategoryController`, `ProductController`, etc.
- **Operations:** `ReceiptController`, `DispatchController`, `StockAdjustmentController`
- **Purchasing:** `PurchaseOrderController`
- **DashboardController:** Lógica del dashboard.
- **ReportController:** Generación de reportes.

### Servicios (en `app/Services/`)
- **FolioService:** Genera folios únicos para documentos.
- **FefoService:** Lógica para sugerir lotes a despachar (FEFO).
- **AlertService:** Verifica y crea alertas de stock.

### Comandos (en `app/Console/Commands/`)
- **CheckAlertsCommand:** Tarea programada para generar alertas. Se ejecuta diariamente.

## 8. Rutas Principales (`routes/web.php`)

- `/dashboard`: Dashboard principal.
- `/admin/users`: Gestión de usuarios.
- `/catalog/*`: Rutas para catálogos.
- `/inventory/products`: Gestión de productos.
- `/operations/*`: Rutas para operaciones de inventario.
- `/purchasing/orders`: Órdenes de compra.
- `/reports/*`: Reportes.
- `/alerts`: Visualización de alertas.
