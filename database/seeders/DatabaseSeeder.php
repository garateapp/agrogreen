<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CuartelVariedad;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tenant = Tenant::first() ?? '00000000-0000-0000-0000-000000000001';

        $this->call(ComprasSeeder::class, false, ['tenantId' => $tenant->id]);
        //$this->call(ComprasSeeder::class, false, ['tenantId' => $tenant->id]);
        //$this->call(LaboresSeeder::class, false, ['tenantId' => $tenant->id]);
    }
}
