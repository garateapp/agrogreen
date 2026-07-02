<?php

use App\Models\CentroCosto;
use App\Models\Cuartel;
use App\Models\Especie;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variedad;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

test('paddock creation persists cost center and agronomic fields', function () {
    createPaddocksSchema();

    try {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user);

        $centroCosto = CentroCosto::create([
            'tenant_id' => $tenant->id,
            'codigo' => 'CC-001',
            'nombre' => 'Campo Norte',
        ]);

        $especie = Especie::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Cereza',
        ]);

        $variedad = Variedad::create([
            'tenant_id' => $tenant->id,
            'especie_id' => $especie->id,
            'nombre' => 'Santina',
        ]);

        $this->post(route('mantenedores.store', ['entity' => 'cuarteles']), [
            'nombre' => 'Olivar - Cuartel 1 y 2 - Cereza - Santina',
            'centro_costo_id' => $centroCosto->id,
            'especie_id' => $especie->id,
            'superficie_hectareas' => 5.18,
            'ano_plantacion' => 2020,
            'distancia_sobre_hilera' => 3.5,
            'distancia_intra_hilera' => 1.5,
            '_variedades' => json_encode([
                [
                    'variedad_id' => $variedad->id,
                    'cantidad_plantas' => 1200,
                ],
            ]),
        ])->assertRedirect();

        $cuartel = Cuartel::first();

        expect($cuartel)->not->toBeNull()
            ->and($cuartel->centro_costo_id)->toBe($centroCosto->id)
            ->and($cuartel->especie_id)->toBe($especie->id)
            ->and((float) $cuartel->superficie_hectareas)->toBe(5.18)
            ->and($cuartel->ano_plantacion)->toBe(2020)
            ->and((float) $cuartel->distancia_sobre_hilera)->toBe(3.5)
            ->and((float) $cuartel->distancia_intra_hilera)->toBe(1.5);

        $this->assertDatabaseHas('cuartel_variedad', [
            'cuartel_id' => $cuartel->id,
            'variedad_id' => $variedad->id,
            'cantidad_plantas' => 1200,
        ]);
    } finally {
        dropPaddocksSchema();
    }
});

function createPaddocksSchema(): void
{
    dropPaddocksSchema();

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

    Schema::create('centros_costo', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->string('codigo');
        $table->string('nombre');
        $table->uuid('parent_id')->nullable();
        $table->boolean('activo')->default(true);
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('especies', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->uuid('familia_id')->nullable();
        $table->string('nombre');
        $table->string('descripcion')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('variedades', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id');
        $table->uuid('especie_id')->nullable();
        $table->string('nombre');
        $table->string('descripcion')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('cuartels', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tenant_id')->nullable();
        $table->uuid('centro_costo_id');
        $table->uuid('especie_id')->nullable();
        $table->string('nombre');
        $table->decimal('superficie_hectareas', 8, 2);
        $table->integer('ano_plantacion');
        $table->decimal('distancia_sobre_hilera', 4, 2);
        $table->decimal('distancia_intra_hilera', 4, 2);
        $table->json('geometria_geojson')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('cuartel_variedad', function (Blueprint $table) {
        $table->uuid('cuartel_id');
        $table->uuid('variedad_id');
        $table->integer('cantidad_plantas');
        $table->timestamps();
        $table->primary(['cuartel_id', 'variedad_id']);
    });
}

function dropPaddocksSchema(): void
{
    collect([
        'cuartel_variedad',
        'cuartels',
        'variedades',
        'especies',
        'centros_costo',
        'users',
        'tenants',
    ])->each(fn (string $table) => Schema::dropIfExists($table));
}
