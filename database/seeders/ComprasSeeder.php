<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use App\Models\CentroCosto;
use App\Models\Cliente;
use App\Models\Egreso;
use App\Models\Ingreso;
use App\Models\ItemGasto;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SolicitudCotizacion;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\CuartelVariedad;

class ComprasSeeder extends Seeder
{
    public function run(?string $tenantId = null): void
    {
             // Crear registros de CuartelVariedad
$cuartelVariedades = [
    // El Carmen - Cuartel 1 -Nectarin - White Angel => White Angel
    [
        'cuartel_id' => '019ef4df-c0a1-7229-9d16-9396ada7ad71',
        'variedad_id' => '019ef4df-bce5-7211-bcab-c895ff07eb95',
        'cantidad_plantas' => 0,
    ],
    // El Carmen - Cuartel 2 - Nectarin - Nectariane => Nectariane
    [
        'cuartel_id' => '019ef4df-c0a4-73ad-b904-b3313b46752c',
        'variedad_id' => '019ef4df-bd2b-723a-b48e-8b35fabcd24f',
        'cantidad_plantas' => 0,
    ],
    // El Carmen - Cuartel 3 - Nectarin - Nectarjewel => Nectajewel
    [
        'cuartel_id' => '019ef4df-c0a7-72fb-99a4-7a4ceff3fad0',
        'variedad_id' => '019ef4df-bd12-72c3-ba8b-707944102dc0',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 2 (1) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c0aa-7106-bb0a-0eab20a2f5f7',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 3.1 (1) - Cereza - Royal Dawn-Lapins => Royal Dawn*
    [
        'cuartel_id' => '019ef4df-c0ad-73e3-95ce-9bc97a647286',
        'variedad_id' => '019ef4df-bca4-70ce-abd8-10aedd13ffaf',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 3.1 (1) - Cereza - Royal Dawn-Lapins => Royal Dawn
    [
        'cuartel_id' => '019ef4df-c0ad-73e3-95ce-9bc97a647286',
        'variedad_id' => '019ef4df-bbef-70ce-8b13-e6a56c801f51',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 3.1 (1) - Cereza - Royal Dawn-Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0ad-73e3-95ce-9bc97a647286',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4.1 (1) - Cereza - Rainier-Santina-Lapins => Santina
    [
        'cuartel_id' => '019ef4df-c0af-7252-9139-ccd173fa9a07',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4.1 (1) - Cereza - Rainier-Santina-Lapins => Rainier
    [
        'cuartel_id' => '019ef4df-c0af-7252-9139-ccd173fa9a07',
        'variedad_id' => '019ef4df-bbe3-70c8-bc2a-6a61d4bfff99',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4.1 (1) - Cereza - Rainier-Santina-Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0af-7252-9139-ccd173fa9a07',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 5.1 (1) - Cereza - Royal Dawn-Lapins => Royal Dawn*
    [
        'cuartel_id' => '019ef4df-c0b2-7092-8a33-a175b17c6e06',
        'variedad_id' => '019ef4df-bca4-70ce-abd8-10aedd13ffaf',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 5.1 (1) - Cereza - Royal Dawn-Lapins => Royal Dawn
    [
        'cuartel_id' => '019ef4df-c0b2-7092-8a33-a175b17c6e06',
        'variedad_id' => '019ef4df-bbef-70ce-8b13-e6a56c801f51',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 5.1 (1) - Cereza - Royal Dawn-Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0b2-7092-8a33-a175b17c6e06',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 1 (2) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c0b6-71d9-8b16-38944152deec',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 2 (2) - Cereza - Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0b9-70a5-aed5-bb61f044da59',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 6 (2) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c0bc-7079-9855-a37772a93b6d',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 1 (3) - Cereza - Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0be-73e3-b0a2-e3fd935046f1',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 3 (3) - Cereza - Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0c1-710a-9c7e-f15407a3bcb2',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 2 (3) - Cereza - Rtyoga => Royal Tioga
    [
        'cuartel_id' => '019ef4df-c0c5-72ce-a4d4-7003e70c4302',
        'variedad_id' => '019ef4df-bc77-7320-92c7-3f82450697e1',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 2 y 3: Rainier, Lapins => Rainier
    [
        'cuartel_id' => '019ef4df-c0c8-7268-a75e-4fdf03ac2421',
        'variedad_id' => '019ef4df-bbe3-70c8-bc2a-6a61d4bfff99',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 2 y 3: Rainier, Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0c8-7268-a75e-4fdf03ac2421',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4 (3) - Ciruelo Turtle egg-RedPhoenix-Candy Red => Red Phoenix
    [
        'cuartel_id' => '019ef4df-c0ca-714e-9e71-5ffec04b52f2',
        'variedad_id' => '019ef4df-beda-7218-8d6a-08c96a32efac',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4 (3) - Ciruelo Turtle egg-RedPhoenix-Candy Red => Turtle Egg
    [
        'cuartel_id' => '019ef4df-c0ca-714e-9e71-5ffec04b52f2',
        'variedad_id' => '019ef4df-bf36-73f6-a398-2d70b099a063',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4 (3) - Ciruelo Turtle egg-RedPhoenix-Candy Red => Candy Red
    [
        'cuartel_id' => '019ef4df-c0ca-714e-9e71-5ffec04b52f2',
        'variedad_id' => '019ef4df-be90-7266-bba6-db94e2c9c2fa',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 6 (3) - Ciruelo Turtle egg-Red Phoenix-Candy Red => Red Phoenix
    [
        'cuartel_id' => '019ef4df-c0cd-73a7-a3ec-91335d7b644f',
        'variedad_id' => '019ef4df-beda-7218-8d6a-08c96a32efac',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 6 (3) - Ciruelo Turtle egg-Red Phoenix-Candy Red => Turtle Egg
    [
        'cuartel_id' => '019ef4df-c0cd-73a7-a3ec-91335d7b644f',
        'variedad_id' => '019ef4df-bf36-73f6-a398-2d70b099a063',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 6 (3) - Ciruelo Turtle egg-Red Phoenix-Candy Red => Candy Red
    [
        'cuartel_id' => '019ef4df-c0cd-73a7-a3ec-91335d7b644f',
        'variedad_id' => '019ef4df-be90-7266-bba6-db94e2c9c2fa',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4 (2) - Ciruela- Silver red-Red Phoenix => Red Phoenix
    [
        'cuartel_id' => '019ef4df-c0d3-7038-8a42-950ea150feae',
        'variedad_id' => '019ef4df-beda-7218-8d6a-08c96a32efac',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 4 (2) - Ciruela- Silver red-Red Phoenix => Silver Red
    [
        'cuartel_id' => '019ef4df-c0d3-7038-8a42-950ea150feae',
        'variedad_id' => '019ef4df-bef9-728e-8cf8-390afb50fb3e',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 1 y 4, Ciruela Sweet mery, Candy red, Red Phoenix => Red Phoenix
    [
        'cuartel_id' => '019ef4df-c0d5-70e8-944f-ac44de1c57b7',
        'variedad_id' => '019ef4df-beda-7218-8d6a-08c96a32efac',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 1 y 4, Ciruela Sweet mery, Candy red, Red Phoenix => Sweet Mary
    [
        'cuartel_id' => '019ef4df-c0d5-70e8-944f-ac44de1c57b7',
        'variedad_id' => '019ef4df-be62-718b-8801-dfb3c7a66c55',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 1 y 4, Ciruela Sweet mery, Candy red, Red Phoenix => Candy Red
    [
        'cuartel_id' => '019ef4df-c0d5-70e8-944f-ac44de1c57b7',
        'variedad_id' => '019ef4df-be90-7266-bba6-db94e2c9c2fa',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Ciruelo Candy red => Candy Red
    [
        'cuartel_id' => '019ef4df-c0d8-722f-9d98-81f05cf15de3',
        'variedad_id' => '019ef4df-be90-7266-bba6-db94e2c9c2fa',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 5, Ciruelo Sweet Mery, Blue Gusto => Sweet Mary
    [
        'cuartel_id' => '019ef4df-c0dc-70e3-9fd6-c56e3b1ed2c9',
        'variedad_id' => '019ef4df-be62-718b-8801-dfb3c7a66c55',
        'cantidad_plantas' => 0,
    ],
    // La Esperanza - Cuartel 5, Ciruelo Sweet Mery, Blue Gusto => Blue Gusto
    [
        'cuartel_id' => '019ef4df-c0dc-70e3-9fd6-c56e3b1ed2c9',
        'variedad_id' => '019ef4df-be5c-714b-b0b8-f4b4806e94a8',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 4.1 - Cereza - Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0e2-73eb-8b59-4661b2082318',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 6 - Cereza - Reinier-Lapins => Rainier
    [
        'cuartel_id' => '019ef4df-c0e5-7328-b873-ba1ebf2a7b6b',
        'variedad_id' => '019ef4df-bbe3-70c8-bc2a-6a61d4bfff99',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 6 - Cereza - Reinier-Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0e5-7328-b873-ba1ebf2a7b6b',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 4.4 - Cereza - Sweet Aryana => Sweet Aryana
    [
        'cuartel_id' => '019ef4df-c0e8-70f7-8e1d-1f3bc63537f8',
        'variedad_id' => '019ef4df-bc5f-7281-ab8e-2df0caa172f1',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 4.5 - Cereza - Red Pacific => Red Pacific
    [
        'cuartel_id' => '019ef4df-c0eb-71e8-82f0-d0132dd864ee',
        'variedad_id' => '019ef4df-bc61-7382-a969-5f91339aba50',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 2.3 - Cereza - Nimba => Nimba
    [
        'cuartel_id' => '019ef4df-c0ed-70bf-af02-be32918c3a17',
        'variedad_id' => '019ef4df-bc64-717b-900a-4c3d7400d237',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 2.4 - Cereza - Red Pacific => Red Pacific
    [
        'cuartel_id' => '019ef4df-c0f0-7343-878d-3c53d3296682',
        'variedad_id' => '019ef4df-bc61-7382-a969-5f91339aba50',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 5.1 - Cereza - Cherry Treat-Lapins => Cherry Treat
    [
        'cuartel_id' => '019ef4df-c0f3-724f-89fa-352bccd6993d',
        'variedad_id' => '019ef4df-bc7b-724c-b874-2271eb0836ef',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 5.1 - Cereza - Cherry Treat-Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c0f3-724f-89fa-352bccd6993d',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.3 - Injerto Ciruela, Sweet mery, Blue gusto, red phoenix => Red Phoenix
    [
        'cuartel_id' => '019ef4df-c0f6-7362-9b14-18c56b1896fb',
        'variedad_id' => '019ef4df-beda-7218-8d6a-08c96a32efac',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.3 - Injerto Ciruela, Sweet mery, Blue gusto, red phoenix => Sweet Mary
    [
        'cuartel_id' => '019ef4df-c0f6-7362-9b14-18c56b1896fb',
        'variedad_id' => '019ef4df-be62-718b-8801-dfb3c7a66c55',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.3 - Injerto Ciruela, Sweet mery, Blue gusto, red phoenix => Blue Gusto
    [
        'cuartel_id' => '019ef4df-c0f6-7362-9b14-18c56b1896fb',
        'variedad_id' => '019ef4df-be5c-714b-b0b8-f4b4806e94a8',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.3 - Injerto Ciruela, Sweet mery, Blue gusto, red phoenix => Injerto
    [
        'cuartel_id' => '019ef4df-c0f6-7362-9b14-18c56b1896fb',
        'variedad_id' => '019ef4df-befc-71ab-a1e8-16d3867b7c31',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 1.2 - Ciruela - Injerto => Injerto
    [
        'cuartel_id' => '019ef4df-c0f8-72a2-a665-fff9b26bc949',
        'variedad_id' => '019ef4df-befc-71ab-a1e8-16d3867b7c31',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.1 - Nectarin - Majestic Pearl => Majestic Pearl
    [
        'cuartel_id' => '019ef4df-c0fe-7183-901c-ab8626971d76',
        'variedad_id' => '019ef4df-bcf2-7141-98d7-9e898adc5e3f',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.2 - Nectarin - Andesneccuatro => Andesneccuatro
    [
        'cuartel_id' => '019ef4df-c101-71ac-9570-4c5bd92ad100',
        'variedad_id' => '019ef4df-bd17-71f3-bef4-a30d34945f79',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 3.3 - Nectarin - Rock Pearl => Rock Pearl
    [
        'cuartel_id' => '019ef4df-c104-7367-a6b2-894c4d1d0511',
        'variedad_id' => '019ef4df-bd88-72c4-be0d-ee605051b67a',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 1 - Nectarin - Snow Sweet => Snow Sweet
    [
        'cuartel_id' => '019ef4df-c107-7105-a044-3355cc11ffe2',
        'variedad_id' => '019ef4df-bd7b-7263-a942-a03bf2bb4528',
        'cantidad_plantas' => 0,
    ],
    // Las Cabras - Cuartel 1 - Nectarin - Majestic Pearl => Majestic Pearl
    [
        'cuartel_id' => '019ef4df-c10b-71b1-9f96-70922d56ae53',
        'variedad_id' => '019ef4df-bcf2-7141-98d7-9e898adc5e3f',
        'cantidad_plantas' => 0,
    ],
    // Los Niches - Cuartel 2 y 4 - Cereza - Regina-Kordia-Skeena => Skeena
    [
        'cuartel_id' => '019ef4df-c10e-7119-a542-df0d96d63918',
        'variedad_id' => '019ef4df-bc02-737a-bdbb-403d7e35e64b',
        'cantidad_plantas' => 0,
    ],
    // Los Niches - Cuartel 2 y 4 - Cereza - Regina-Kordia-Skeena => Regina
    [
        'cuartel_id' => '019ef4df-c10e-7119-a542-df0d96d63918',
        'variedad_id' => '019ef4df-bbe8-7399-ab00-446fb1d01dd3',
        'cantidad_plantas' => 0,
    ],
    // Los Niches - Cuartel 2 y 4 - Cereza - Regina-Kordia-Skeena => Kordia
    [
        'cuartel_id' => '019ef4df-c10e-7119-a542-df0d96d63918',
        'variedad_id' => '019ef4df-bbe1-70c6-930f-89478fd30144',
        'cantidad_plantas' => 0,
    ],
    // Los Niches - Cuartel 1, 3 y 5 - Cereza - Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c111-7032-b93d-a7c30d4b12a2',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // Los Niches - Cuartel 1, 3 y 5 - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c114-713f-89ec-c42674837c2b',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 1.(1) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c117-7023-8510-fe28f72de72f',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 2 y 3 (1) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c11a-7016-8cea-47919404561c',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 4 (1) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c11d-72e7-afd2-8b0dc7674a13',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 5 (1) - Cererza - Lapins-Santina => Santina
    [
        'cuartel_id' => '019ef4df-c120-72d2-ae21-549415b50b85',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 5 (1) - Cererza - Lapins-Santina => Lapins
    [
        'cuartel_id' => '019ef4df-c120-72d2-ae21-549415b50b85',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 1 y2 (2) - Nectarin - White Angel => White Angel
    [
        'cuartel_id' => '019ef4df-c124-73c4-8cc8-d34a2fd17351',
        'variedad_id' => '019ef4df-bce5-7211-bcab-c895ff07eb95',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 3 y 4 (2) - Nectarin - Andesneccuatro => Andesneccuatro
    [
        'cuartel_id' => '019ef4df-c127-700b-ae3d-16d3006190a9',
        'variedad_id' => '019ef4df-bd17-71f3-bef4-a30d34945f79',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 5 (2) - Nectarin - Sweet Giant => Sweet Giant
    [
        'cuartel_id' => '019ef4df-c12a-7393-beed-cd65fc430226',
        'variedad_id' => '019ef4df-bd08-728d-b682-2820ff55b18d',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 6 (2) - Nectarin - Sweet Giant => Sweet Giant
    [
        'cuartel_id' => '019ef4df-c12c-702c-9078-85634236b50f',
        'variedad_id' => '019ef4df-bd08-728d-b682-2820ff55b18d',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 2) - Cuartel 5 (3) - Cereza - Lapins => Lapins
    [
        'cuartel_id' => '019ef4df-c12f-7291-bcb3-0383e12010b1',
        'variedad_id' => '019ef4df-bbdf-71bf-b002-60df1d527ca8',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 1) - Cuartel 4 (1) - Cereza - Santina => Santina
    [
        'cuartel_id' => '019ef4df-c132-7231-9e99-cad1a576e6f7',
        'variedad_id' => '019ef4df-bbea-706c-b425-08c3b37a7eea',
        'cantidad_plantas' => 0,
    ],
    // Paine (Campo 2) - Cuartel 1, 2, 3 y 4 (3) - Ciruela - Varias => Varias
    [
        'cuartel_id' => '019ef4df-c135-7185-b88f-8387bc28d3cf',
        'variedad_id' => '019ef4df-bef2-7016-bc86-125f4c14f27b',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Durazno Zee Lady => Zee Lady
    [
        'cuartel_id' => '019ef4df-c138-71e5-8e25-dc5df46ec216',
        'variedad_id' => '019ef4df-bdf8-703f-82fe-7d2e4233fdab',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Durazno Ivory Princess => Ivory Princess
    [
        'cuartel_id' => '019ef4df-c13b-7083-bbe6-9cc924f3c094',
        'variedad_id' => '019ef4df-be15-7194-ae24-14e880a4ca54',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Durazno September Snow => September Snow
    [
        'cuartel_id' => '019ef4df-c13e-71e9-8f51-d37a9de1d46b',
        'variedad_id' => '019ef4df-bdf1-7253-a263-799a3cd8360f',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Membrillo Champion => Champion
    [
        'cuartel_id' => '019ef4df-c141-710f-bf02-fba9e7f52905',
        'variedad_id' => '019ef4df-bb68-73ec-aa3c-d5d592029b25',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Membrillo Champion => Champion
    [
        'cuartel_id' => '019ef4df-c144-72ec-aae2-43d698278a1b',
        'variedad_id' => '019ef4df-bb68-73ec-aa3c-d5d592029b25',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Nectarin Bright Pearl => Bright Pearl
    [
        'cuartel_id' => '019ef4df-c148-7390-ad47-3e7e82809976',
        'variedad_id' => '019ef4df-bcc9-721e-8be1-3ef03c2cb424',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Nectarin Agust Pearl => August Pearl
    [
        'cuartel_id' => '019ef4df-c14b-7176-8be8-7b49c98a02a7',
        'variedad_id' => '019ef4df-bcf0-7059-b3c8-24cb8f96d4eb',
        'cantidad_plantas' => 0,
    ],
    // San Andrés - Nectarin Giant Pearl => Giant Pearl
    [
        'cuartel_id' => '019ef4df-c14e-70ae-b223-37f6c5217b82',
        'variedad_id' => '019ef4df-bd02-730d-8c6a-0ec004a6580d',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Durazno White Lady => White Lady
    [
        'cuartel_id' => '019ef4df-c150-70bb-9746-7fb4f5e8f900',
        'variedad_id' => '019ef4df-bdcf-70c1-9b8c-db06956eff39',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Durazno Carson => Carson
    [
        'cuartel_id' => '019ef4df-c153-7165-a445-0131d6d1242a',
        'variedad_id' => '019ef4df-bdd6-71ef-999a-6063b463e8ee',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Durazno Spring Beauty => Spring Beauty
    [
        'cuartel_id' => '019ef4df-c156-720e-8b38-e35fb32a1542',
        'variedad_id' => '019ef4df-be2c-73f3-8606-1eaa4fff57a6',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Durazno Andross => Andross
    [
        'cuartel_id' => '019ef4df-c159-7141-8e80-bdcc1cd7e3b1',
        'variedad_id' => '019ef4df-bdd4-7221-b2e9-d46f48cd13f8',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Durazno Spring Beauty => Spring Beauty
    [
        'cuartel_id' => '019ef4df-c15b-726f-8dab-72272f7f4cb5',
        'variedad_id' => '019ef4df-be2c-73f3-8606-1eaa4fff57a6',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Durazno Spring Beauty => Spring Beauty
    [
        'cuartel_id' => '019ef4df-c15f-71b0-9fbc-50fcf282a14c',
        'variedad_id' => '019ef4df-be2c-73f3-8606-1eaa4fff57a6',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Artic Star => Arctic Star
    [
        'cuartel_id' => '019ef4df-c163-7269-8a8a-51c97ebd05f2',
        'variedad_id' => '019ef4df-bcbd-73b5-a314-06179b626501',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Super Manon => Super Manon
    [
        'cuartel_id' => '019ef4df-c165-729d-82f8-8837939d468e',
        'variedad_id' => '019ef4df-bd0f-725e-8846-4822b6f6dc3b',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Super Manon => Super Manon
    [
        'cuartel_id' => '019ef4df-c168-726f-8107-6f739c6b32c1',
        'variedad_id' => '019ef4df-bd0f-725e-8846-4822b6f6dc3b',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Artic Mist => Arctic Mist
    [
        'cuartel_id' => '019ef4df-c16b-71a0-b07a-02317328e0f4',
        'variedad_id' => '019ef4df-bcc0-73d1-8e44-6a014cd36a57',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Artic Mist => Arctic Mist
    [
        'cuartel_id' => '019ef4df-c16e-7146-be9a-6b234b59979e',
        'variedad_id' => '019ef4df-bcc0-73d1-8e44-6a014cd36a57',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Artic Mist => Arctic Mist
    [
        'cuartel_id' => '019ef4df-c171-707f-9539-8169ab07348a',
        'variedad_id' => '019ef4df-bcc0-73d1-8e44-6a014cd36a57',
        'cantidad_plantas' => 0,
    ],
    // Santa Isabel - Nectarin Artic Mist => Arctic Mist
    [
        'cuartel_id' => '019ef4df-c173-70b3-9d9f-b611e00de843',
        'variedad_id' => '019ef4df-bcc0-73d1-8e44-6a014cd36a57',
        'cantidad_plantas' => 0,
    ],
];

foreach ($cuartelVariedades as $item) {
    DB::table('cuartel_variedad')->updateOrInsert(
        [
            'cuartel_id' => $item['cuartel_id'],
            'variedad_id' => $item['variedad_id'],
        ],
        [
            'cantidad_plantas' => $item['cantidad_plantas'],
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    }
}
}
