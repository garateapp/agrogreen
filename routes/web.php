<?php

use App\Http\Controllers\Agroquimicos\ApplicationRecordController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BodegajeController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\CosechaController;
use App\Http\Controllers\EstimacionController;
use App\Http\Controllers\FaenasController;
use App\Http\Controllers\LaboresController;
use App\Http\Controllers\Mantenedores\MantenedorController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\MaquinariaController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('logs', [AuditLogController::class, 'index'])->name('logs');
    });
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('invoices', [ComprasController::class, 'invoices'])->name('invoices');
        Route::post('invoices', [ComprasController::class, 'storeEgreso'])->name('invoices.store');
        Route::put('invoices/{id}', [ComprasController::class, 'updateEgreso'])->name('invoices.update');
        Route::delete('invoices/{id}', [ComprasController::class, 'destroyEgreso'])->name('invoices.destroy');
        Route::get('incomes', [ComprasController::class, 'incomes'])->name('incomes');
        Route::post('incomes', [ComprasController::class, 'storeIngreso'])->name('incomes.store');
        Route::put('incomes/{id}', [ComprasController::class, 'updateIngreso'])->name('incomes.update');
        Route::delete('incomes/{id}', [ComprasController::class, 'destroyIngreso'])->name('incomes.destroy');
        Route::get('purchase-orders', [ComprasController::class, 'purchaseOrders'])->name('purchase-orders');
        Route::post('purchase-orders', [ComprasController::class, 'storeOrdenCompra'])->name('purchase-orders.store');
        Route::put('purchase-orders/{id}', [ComprasController::class, 'updateOrdenCompra'])->name('purchase-orders.update');
        Route::delete('purchase-orders/{id}', [ComprasController::class, 'destroyOrdenCompra'])->name('purchase-orders.destroy');
        Route::get('payments', [ComprasController::class, 'payments'])->name('payments');
        Route::post('payments', [ComprasController::class, 'storePago'])->name('payments.store');
        Route::put('payments/{id}', [ComprasController::class, 'updatePago'])->name('payments.update');
        Route::delete('payments/{id}', [ComprasController::class, 'destroyPago'])->name('payments.destroy');
        Route::get('purchase-orders-report', [ComprasController::class, 'purchaseOrdersReport'])->name('purchase-orders-report');
        Route::get('cash-flow-report', [ComprasController::class, 'cashFlowReport'])->name('cash-flow-report');
        Route::get('quotations', [ComprasController::class, 'quotations'])->name('quotations');
        Route::post('quotations', [ComprasController::class, 'storeCotizacion'])->name('quotations.store');
        Route::put('quotations/{id}', [ComprasController::class, 'updateCotizacion'])->name('quotations.update');
        Route::delete('quotations/{id}', [ComprasController::class, 'destroyCotizacion'])->name('quotations.destroy');
    });

    Route::prefix('maquinaria')->name('maquinaria.')->group(function () {
        Route::get('machine-tasks', [MaquinariaController::class, 'machineTasks'])->name('machine-tasks');
        Route::post('machine-tasks', [MaquinariaController::class, 'storeMachineTask'])->name('machine-tasks.store');
        Route::put('machine-tasks/{id}', [MaquinariaController::class, 'updateMachineTask'])->name('machine-tasks.update');
        Route::delete('machine-tasks/{id}', [MaquinariaController::class, 'destroyMachineTask'])->name('machine-tasks.destroy');
        Route::get('oil-receipts', [MaquinariaController::class, 'oilReceipts'])->name('oil-receipts');
        Route::post('oil-receipts', [MaquinariaController::class, 'storeOilReceipt'])->name('oil-receipts.store');
        Route::put('oil-receipts/{id}', [MaquinariaController::class, 'updateOilReceipt'])->name('oil-receipts.update');
        Route::delete('oil-receipts/{id}', [MaquinariaController::class, 'destroyOilReceipt'])->name('oil-receipts.destroy');
        Route::get('machine-report', [MaquinariaController::class, 'machineReport'])->name('machine-report');
    });

    Route::prefix('labores')->name('labores.')->group(function () {
        Route::get('planificador', [LaboresController::class, 'planificador'])->name('planificador');
        Route::post('planificador', [LaboresController::class, 'store'])->name('store');
        Route::put('planificador/{id}', [LaboresController::class, 'update'])->name('update');
        Route::delete('planificador/{id}', [LaboresController::class, 'destroy'])->name('destroy');
        Route::patch('planificador/{id}/estado', [LaboresController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::post('planificador/{id}/ejecutar', [LaboresController::class, 'ejecutarInstancia'])->name('ejecutar-instancia');
        Route::get('tarja-diaria', [LaboresController::class, 'tarjaDiaria'])->name('tarja-diaria');
        Route::post('tarja-diaria/{id}/empleados', [LaboresController::class, 'guardarTarja'])->name('guardar-tarja');
    });

    Route::prefix('faenas')->name('faenas.')->group(function () {
        Route::get('tasks', [FaenasController::class, 'tasks'])->name('tasks');
        Route::get('tasks-creation', [FaenasController::class, 'tasksCreation'])->name('tasks-creation');
        Route::post('tasks-creation', [FaenasController::class, 'store'])->name('tasks-creation.store');
        Route::get('tasks-creation-mobile', [FaenasController::class, 'tasksCreationMobile'])->name('tasks-creation-mobile');
        Route::get('salary-report', [FaenasController::class, 'salaryReport'])->name('salary-report');
        Route::get('additional-salary-report', [FaenasController::class, 'additionalSalaryReport'])->name('additional-salary-report');
        Route::get('tasks-performance', [FaenasController::class, 'tasksPerformance'])->name('tasks-performance');
    });

    Route::prefix('bodegaje')->name('bodegaje.')->group(function () {
        Route::get('goods-receipts', [BodegajeController::class, 'goodsReceipts'])->name('goods-receipts');
        Route::post('goods-receipts', [BodegajeController::class, 'storeGoodsReceipt'])->name('goods-receipts.store');
        Route::put('goods-receipts/{id}', [BodegajeController::class, 'updateGoodsReceipt'])->name('goods-receipts.update');
        Route::delete('goods-receipts/{id}', [BodegajeController::class, 'destroyGoodsReceipt'])->name('goods-receipts.destroy');
        Route::get('goods-issues', [BodegajeController::class, 'goodsIssues'])->name('goods-issues');
        Route::post('goods-issues', [BodegajeController::class, 'storeGoodsIssue'])->name('goods-issues.store');
        Route::put('goods-issues/{id}', [BodegajeController::class, 'updateGoodsIssue'])->name('goods-issues.update');
        Route::delete('goods-issues/{id}', [BodegajeController::class, 'destroyGoodsIssue'])->name('goods-issues.destroy');
        Route::get('inventory-report', [BodegajeController::class, 'inventoryReport'])->name('inventory-report');
        Route::get('warehouse-transfers', [BodegajeController::class, 'warehouseTransfers'])->name('warehouse-transfers');
        Route::post('warehouse-transfers', [BodegajeController::class, 'storeWarehouseTransfer'])->name('warehouse-transfers.store');
        Route::get('product-consumption-report', [BodegajeController::class, 'productConsumptionReport'])->name('product-consumption-report');
    });

    Route::prefix('cosecha')->name('cosecha.')->group(function () {
        Route::get('registro', [CosechaController::class, 'registro'])->name('registro');
        Route::get('contenedores', [CosechaController::class, 'contenedores'])->name('contenedores');
        Route::post('cerrar-jornada', [CosechaController::class, 'cerrarJornada'])->name('cerrar-jornada');
    });

    Route::prefix('presupuesto')->name('presupuesto.')->group(function () {
        Route::get('/', [PresupuestoController::class, 'index'])->name('index');
        Route::get('create', [PresupuestoController::class, 'create'])->name('create');
        Route::post('/', [PresupuestoController::class, 'store'])->name('store');
        Route::get('{id}/edit', [PresupuestoController::class, 'edit'])->name('edit');
        Route::put('{id}', [PresupuestoController::class, 'update'])->name('update');
        Route::delete('{id}', [PresupuestoController::class, 'destroy'])->name('destroy');

        Route::put('{presupuestoId}/detalle/{detalleId}', [PresupuestoController::class, 'updateDetalle'])->name('detalle.update');
        Route::post('{presupuestoId}/detalle/{detalleId}/clone', [PresupuestoController::class, 'cloneDetalle'])->name('detalle.clone');
        Route::post('{presupuestoId}/detalle/{detalleId}/copy-to-agrupador', [PresupuestoController::class, 'copyDetalleToAgrupador'])->name('detalle.copy-agrupador');

        Route::post('{id}/clone', [PresupuestoController::class, 'clonePresupuesto'])->name('clone');

        Route::get('temporadas', [PresupuestoController::class, 'temporadas'])->name('temporadas');
        Route::post('temporadas', [PresupuestoController::class, 'storeTemporada'])->name('temporadas.store');
        Route::put('temporadas/{id}', [PresupuestoController::class, 'updateTemporada'])->name('temporadas.update');
        Route::delete('temporadas/{id}', [PresupuestoController::class, 'destroyTemporada'])->name('temporadas.destroy');

        Route::get('estimaciones', [EstimacionController::class, 'index'])->name('estimaciones');
        Route::post('estimaciones', [EstimacionController::class, 'store'])->name('estimaciones.store');
        Route::put('estimaciones/{id}', [EstimacionController::class, 'update'])->name('estimaciones.update');
        Route::delete('estimaciones/{id}', [EstimacionController::class, 'destroy'])->name('estimaciones.destroy');
    });

    Route::prefix('agroquimicos')->name('agroquimicos.')->group(function () {
        Route::get('/', [ApplicationRecordController::class, 'index'])->name('index');
        Route::get('create', [ApplicationRecordController::class, 'create'])->name('create');
        Route::post('/', [ApplicationRecordController::class, 'store'])->name('store');
        Route::get('{id}', [ApplicationRecordController::class, 'show'])->name('show');
        Route::patch('{id}/approve', [ApplicationRecordController::class, 'approve'])->name('approve');
        Route::post('{id}/cancel', [ApplicationRecordController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('mantenedores')->name('mantenedores.')->group(function () {
        Route::get('familias', [MantenedorController::class, 'familias'])->name('familias');
        Route::get('especies', [MantenedorController::class, 'especies'])->name('especies');
        Route::get('actividades', [MantenedorController::class, 'actividades'])->name('actividades');
        Route::get('variedades', [MantenedorController::class, 'variedades'])->name('variedades');
        Route::get('cuarteles', [MantenedorController::class, 'paddocks'])->name('paddocks');
        Route::get('empleados', [MantenedorController::class, 'employees'])->name('employees');
        Route::get('contenedores-cosecha', [MantenedorController::class, 'harvestContainers'])->name('harvest-containers');
        Route::get('productos', [MantenedorController::class, 'products'])->name('products');
        Route::get('centros-costo', [MantenedorController::class, 'costCenters'])->name('cost-centers');
        Route::get('tratos', [MantenedorController::class, 'extraPaymentTypes'])->name('extra-payment-types');
        Route::get('tractores', [MantenedorController::class, 'tractors'])->name('tractors');
        Route::get('productos-sag', [MantenedorController::class, 'productosSag'])->name('productos-sag');
        Route::get('aplicadores', [MantenedorController::class, 'aplicadoresMetodo'])->name('aplicadores');
        Route::get('equipos-aplicacion', [MantenedorController::class, 'equiposAplicacion'])->name('equipos-aplicacion');
        Route::get('tarjetas', [MantenedorController::class, 'cards'])->name('tarjetas');
        Route::patch('tarjetas/{id}/unassign', [MantenedorController::class, 'unassignTarjeta'])->name('tarjetas.unassign');

        Route::get('{entity}/template', [MantenedorController::class, 'exportTemplate'])->name('template');
        Route::post('{entity}/import', [MantenedorController::class, 'import'])->name('import');
        Route::delete('{entity}/batch', [MantenedorController::class, 'batchDestroy'])->name('batch-destroy');
        Route::get('{entity}', [MantenedorController::class, 'index'])->name('simple');
        Route::post('{entity}', [MantenedorController::class, 'store'])->name('store');
        Route::put('{entity}/{id}', [MantenedorController::class, 'update'])->name('update');
        Route::delete('{entity}/{id}', [MantenedorController::class, 'destroy'])->name('destroy');
        Route::patch('{entity}/{id}/toggle-status', [MantenedorController::class, 'toggleStatus'])->name('toggle-status');
    });
});

require __DIR__.'/settings.php';
