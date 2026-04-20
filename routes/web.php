<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AlertController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Catalog\CategoryController;
use App\Http\Controllers\Catalog\LocationController;
use App\Http\Controllers\Catalog\SupplierController;
use App\Http\Controllers\Catalog\UnitController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Operations\DispatchController;
use App\Http\Controllers\Operations\ReceiptController;
use App\Http\Controllers\Operations\StockAdjustmentController;
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Reports\ReportController;
use Illuminate\Support\Facades\Route;

// Ruta raiz
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Rutas publicas (login)
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Rutas autenticadas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Administracion de usuarios (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('/admin/users', UserController::class)
            ->names('users');
        Route::patch('/admin/users/{user}/toggle', [UserController::class, 'toggle'])
            ->name('users.toggle');
    });

    // Catalogos (admin, warehouse_manager)
    Route::middleware('role:admin,warehouse_manager')->group(function () {
        Route::resource('/catalog/categories', CategoryController::class)
            ->names('categories')
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('/catalog/categories/{category}/toggle', [CategoryController::class, 'toggle'])
            ->name('categories.toggle');

        Route::resource('/catalog/units', UnitController::class)
            ->names('units')
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('/catalog/locations', LocationController::class)
            ->names('locations')
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('/catalog/locations/{location}/toggle', [LocationController::class, 'toggle'])
            ->name('locations.toggle');
    });

    // Proveedores (admin, warehouse_manager, purchasing)
    Route::middleware('role:admin,warehouse_manager,purchasing')->group(function () {
        Route::resource('/catalog/suppliers', SupplierController::class)
            ->names('suppliers')
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('/catalog/suppliers/{supplier}/toggle', [SupplierController::class, 'toggle'])
            ->name('suppliers.toggle');
    });

    // Productos
    Route::get('/inventory/products', [ProductController::class, 'index'])->name('products.index');
    Route::middleware('role:admin,warehouse_manager')->group(function () {
        Route::get('/inventory/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/inventory/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/inventory/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/inventory/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
    });
    Route::get('/inventory/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/api/products/{product}/lots', [ProductController::class, 'availableLots']);

    // Operaciones - Recepciones
    Route::middleware('role:admin,warehouse_manager,warehouse_clerk')->group(function () {
        Route::get('/operations/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/operations/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
        Route::post('/operations/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
        Route::get('/operations/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::patch('/operations/receipts/{receipt}/confirm', [ReceiptController::class, 'confirm'])
            ->name('receipts.confirm');
    });

    // Operaciones - Despachos
    Route::get('/operations/dispatches', [DispatchController::class, 'index'])
        ->middleware('role:admin,warehouse_manager,warehouse_clerk,production,quality')
        ->name('dispatches.index');
    Route::middleware('role:admin,warehouse_manager,warehouse_clerk,production')->group(function () {
        Route::get('/operations/dispatches/create', [DispatchController::class, 'create'])->name('dispatches.create');
        Route::post('/operations/dispatches', [DispatchController::class, 'store'])->name('dispatches.store');
    });
    Route::get('/operations/dispatches/{dispatch}', [DispatchController::class, 'show'])
        ->middleware('role:admin,warehouse_manager,warehouse_clerk,production')
        ->name('dispatches.show');
    Route::middleware('role:admin,warehouse_manager,warehouse_clerk')->group(function () {
        Route::patch('/operations/dispatches/{dispatch}/fulfill', [DispatchController::class, 'fulfill'])
            ->name('dispatches.fulfill');
    });
    Route::patch('/operations/dispatches/{dispatch}/cancel', [DispatchController::class, 'cancel'])
        ->middleware('role:admin,warehouse_manager,warehouse_clerk,production')
        ->name('dispatches.cancel');

    // Operaciones - Ajustes de inventario
    Route::middleware('role:admin,warehouse_manager,warehouse_clerk')->group(function () {
        Route::get('/operations/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
        Route::get('/operations/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('/operations/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
        Route::get('/operations/adjustments/{adjustment}', [StockAdjustmentController::class, 'show'])->name('adjustments.show');
    });
    Route::middleware('role:admin,warehouse_manager')->group(function () {
        Route::patch('/operations/adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])
            ->name('adjustments.approve');
        Route::patch('/operations/adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])
            ->name('adjustments.reject');
    });

    // Alertas (todos)
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])->name('alerts.read');
    Route::patch('/alerts/read-all', [AlertController::class, 'markAllAsRead'])->name('alerts.read-all');

    // Reportes (admin, warehouse_manager, quality, purchasing)
    Route::middleware('role:admin,warehouse_manager,quality,purchasing')->group(function () {
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/movements', [ReportController::class, 'movements'])->name('reports.movements');
        Route::get('/reports/alerts', [ReportController::class, 'alertsReport'])->name('reports.alerts');
    });

    // Ordenes de compra (admin, warehouse_manager, purchasing)
    Route::middleware('role:admin,warehouse_manager,purchasing')->group(function () {
        Route::get('/purchasing/orders', [PurchaseOrderController::class, 'index'])->name('orders.index');
        Route::get('/purchasing/orders/create', [PurchaseOrderController::class, 'create'])->name('orders.create');
        Route::post('/purchasing/orders', [PurchaseOrderController::class, 'store'])->name('orders.store');
        Route::get('/purchasing/orders/{order}', [PurchaseOrderController::class, 'show'])->name('orders.show');
        Route::patch('/purchasing/orders/{order}/status', [PurchaseOrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');
    });
});
