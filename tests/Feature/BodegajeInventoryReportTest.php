<?php

use App\Models\Bodega;
use App\Models\InventarioMovimiento;
use App\Models\InventarioMovimientoDetalle;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Tenant;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

test('inventory report exposes initial stock entries and exits', function () {
    createInventoryReportSchema();

    try {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user);

        $unidad = Unidad::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Litro',
            'abreviacion' => 'L',
        ]);

        $bodega = Bodega::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Bodega Central',
            'codigo' => 'BC',
        ]);

        $producto = Producto::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Fertilizante Cereza',
            'categoria' => 'fertilizante',
            'unidad_medida_id' => $unidad->id,
            'dias_carencia' => 0,
        ]);

        $lote = Lote::create([
            'tenant_id' => $tenant->id,
            'bodega_id' => $bodega->id,
            'producto_id' => $producto->id,
            'codigo_lote' => 'L-001',
            'cantidad_inicial' => 10,
            'cantidad_disponible' => 7,
            'costo_unitario' => 2500,
        ]);

        $entrada = InventarioMovimiento::create([
            'tenant_id' => $tenant->id,
            'codigo' => 'GR-001',
            'bodega_destino_id' => $bodega->id,
            'tipo_movimiento' => 'entrada_compra',
            'fecha_movimiento' => now(),
        ]);

        InventarioMovimientoDetalle::create([
            'movimiento_id' => $entrada->id,
            'producto_id' => $producto->id,
            'lote_id' => $lote->id,
            'cantidad' => 10,
            'costo_unitario_moneda_base' => 2500,
            'saldo_stock_anterior' => 0,
            'saldo_stock_posterior' => 10,
        ]);

        $salida = InventarioMovimiento::create([
            'tenant_id' => $tenant->id,
            'codigo' => 'GC-001',
            'bodega_origen_id' => $bodega->id,
            'tipo_movimiento' => 'consumo_faena',
            'fecha_movimiento' => now(),
        ]);

        InventarioMovimientoDetalle::create([
            'movimiento_id' => $salida->id,
            'producto_id' => $producto->id,
            'lote_id' => $lote->id,
            'cantidad' => -3,
            'costo_unitario_moneda_base' => 2500,
            'saldo_stock_anterior' => 10,
            'saldo_stock_posterior' => 7,
        ]);

        $this->get(route('bodegaje.inventory-report'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('bodegaje/inventory-report')
                ->has('items', 1)
                ->where('items.0.inicial', fn ($value) => (float) $value === 10.0)
                ->where('items.0.entradas', fn ($value) => (float) $value === 10.0)
                ->where('items.0.salidas', fn ($value) => (float) $value === 3.0)
                ->where('items.0.stock', fn ($value) => (float) $value === 7.0)
                ->where('items.0.valorUnitario', fn ($value) => (float) $value === 2500.0)
                ->where('items.0.subtotal', fn ($value) => (float) $value === 17500.0)
                ->etc()
            );
    } finally {
        dropInventoryReportSchema();
    }
});

test('warehouse transfers page exposes database warehouses', function () {
    createInventoryReportSchema();

    try {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user);

        Bodega::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Bodega Sur',
            'codigo' => 'BS',
        ]);

        Bodega::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Bodega Central',
            'codigo' => 'BC',
        ]);

        $this->get(route('bodegaje.warehouse-transfers'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('bodegaje/warehouse-transfers')
                ->has('bodegas', 2)
                ->where('bodegas.0.nombre', 'Bodega Central')
                ->where('bodegas.1.nombre', 'Bodega Sur')
                ->etc()
            );
    } finally {
        dropInventoryReportSchema();
    }
});

test('warehouse transfer store generates code and moves stock', function () {
    createInventoryReportSchema();

    try {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user);

        $unidad = Unidad::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Litro',
            'abreviacion' => 'L',
        ]);

        $origen = Bodega::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Bodega Origen',
            'codigo' => 'BO',
        ]);

        $destino = Bodega::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Bodega Destino',
            'codigo' => 'BD',
        ]);

        $producto = Producto::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Fertilizante Cereza',
            'categoria' => 'fertilizante',
            'unidad_medida_id' => $unidad->id,
            'dias_carencia' => 0,
        ]);

        $lote = Lote::create([
            'tenant_id' => $tenant->id,
            'bodega_id' => $origen->id,
            'producto_id' => $producto->id,
            'codigo_lote' => 'L-ORIG',
            'cantidad_inicial' => 10,
            'cantidad_disponible' => 10,
            'costo_unitario' => 2500,
        ]);

        InventarioMovimiento::create([
            'tenant_id' => $tenant->id,
            'codigo' => 'TR-'.now()->year.'-0007',
            'bodega_origen_id' => $origen->id,
            'bodega_destino_id' => $destino->id,
            'tipo_movimiento' => 'traspaso',
            'fecha_movimiento' => now(),
        ]);

        $this->post(route('bodegaje.warehouse-transfers.store'), [
            'bodega_origen_id' => $origen->id,
            'bodega_destino_id' => $destino->id,
            'fecha_emision' => now()->toDateString(),
            'descripcion' => 'Traspaso interno',
            'lineas' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 4,
                ],
            ],
        ])->assertRedirect()
            ->assertSessionHasNoErrors();

        $codigo = 'TR-'.now()->year.'-0008';
        $movimiento = InventarioMovimiento::where('codigo', $codigo)->first();

        expect($movimiento)->not->toBeNull()
            ->and($movimiento->tipo_movimiento)->toBe('traspaso')
            ->and($movimiento->bodega_origen_id)->toBe($origen->id)
            ->and($movimiento->bodega_destino_id)->toBe($destino->id)
            ->and((float) $lote->refresh()->cantidad_disponible)->toBe(6.0);

        $loteDestino = Lote::where('codigo_lote', 'L-ORIG-'.$codigo)->first();

        expect($loteDestino)->not->toBeNull()
            ->and($loteDestino->bodega_id)->toBe($destino->id)
            ->and((float) $loteDestino->cantidad_disponible)->toBe(4.0)
            ->and(InventarioMovimientoDetalle::where('movimiento_id', $movimiento->id)->count())->toBe(2);
    } finally {
        dropInventoryReportSchema();
    }
});

function createInventoryReportSchema(): void
{
    dropInventoryReportSchema();

    Schema::create('tenants', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('razon_social');
        $table->string('rut');
        $table->string('moneda_base');
        $table->string('status');
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('users', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamp('email_verified_at')->nullable();
        $table->string('role');
        $table->boolean('is_first_login')->default(true);
        $table->boolean('status')->default(true);
        $table->rememberToken();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('unidades', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->string('nombre');
        $table->string('abreviacion');
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('bodegas', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->string('nombre');
        $table->string('codigo');
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('productos', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->string('nombre');
        $table->string('codigo_barras')->nullable();
        $table->string('categoria');
        $table->uuid('unidad_medida_id');
        $table->string('ingrediente_activo')->nullable();
        $table->decimal('dosis_recomendada_por_ha', 8, 2)->nullable();
        $table->integer('dias_carencia')->default(0);
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('lotes', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->uuid('bodega_id')->nullable();
        $table->uuid('producto_id');
        $table->string('codigo_lote');
        $table->date('fecha_vencimiento')->nullable();
        $table->decimal('cantidad_inicial', 12, 4)->default(0);
        $table->decimal('cantidad_disponible', 12, 4)->default(0);
        $table->decimal('costo_unitario', 14, 4)->default(0);
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('inventario_movimientos', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->string('codigo')->nullable();
        $table->uuid('bodega_origen_id')->nullable();
        $table->uuid('bodega_destino_id')->nullable();
        $table->string('tipo_movimiento');
        $table->uuid('documento_referencia_id')->nullable();
        $table->timestamp('fecha_movimiento');
        $table->text('descripcion')->nullable();
        $table->timestamps();
    });

    Schema::create('inventario_movimiento_detalles', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('movimiento_id');
        $table->uuid('producto_id');
        $table->uuid('lote_id')->nullable();
        $table->decimal('cantidad', 12, 4);
        $table->decimal('costo_unitario_moneda_base', 14, 4);
        $table->decimal('saldo_stock_anterior', 12, 4);
        $table->decimal('saldo_stock_posterior', 12, 4);
        $table->timestamps();
    });
}

function dropInventoryReportSchema(): void
{
    collect([
        'inventario_movimiento_detalles',
        'inventario_movimientos',
        'lotes',
        'productos',
        'bodegas',
        'unidades',
        'users',
        'tenants',
    ])->each(fn (string $table) => Schema::dropIfExists($table));
}
