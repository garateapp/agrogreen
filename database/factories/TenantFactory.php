<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'razon_social' => fake()->company(),
            'rut' => fake()->unique()->numerify('##.###.###-#'),
            'moneda_base' => fake()->randomElement(['CLP', 'USD']),
            'status' => 'activo',
        ];
    }
}
