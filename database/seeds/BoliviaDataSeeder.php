<?php

use Illuminate\Database\Seeder;

class BoliviaDataSeeder extends Seeder
{
    /**
     * Seeder con:
     * - 9 Departamentos de Bolivia (tabla regions + region_translation)
     * - Marcas de Vehículos completas (tabla vehicle_brands)
     *
     * Ejecutar con: php artisan db:seed --class=BoliviaDataSeeder
     */
    public function run()
    {
        // ─────────────────────────────────────────
        // 1. DEPARTAMENTOS DE BOLIVIA
        // ─────────────────────────────────────────
        $departamentos = [
            ['code' => 'LP',  'order' => 1, 'name' => 'La Paz'],
            ['code' => 'CB',  'order' => 2, 'name' => 'Cochabamba'],
            ['code' => 'SC',  'order' => 3, 'name' => 'Santa Cruz'],
            ['code' => 'OR',  'order' => 4, 'name' => 'Oruro'],
            ['code' => 'PT',  'order' => 5, 'name' => 'Potosí'],
            ['code' => 'CH',  'order' => 6, 'name' => 'Chuquisaca'],
            ['code' => 'TJ',  'order' => 7, 'name' => 'Tarija'],
            ['code' => 'BE',  'order' => 8, 'name' => 'Beni'],
            ['code' => 'PD',  'order' => 9, 'name' => 'Pando'],
        ];

        foreach ($departamentos as $depto) {
            // Evita duplicados si se corre más de una vez
            $exists = \DB::table('regions')->where('code', $depto['code'])->first();
            if (!$exists) {
                $regionId = \DB::table('regions')->insertGetId([
                    'order'      => $depto['order'],
                    'country_id' => 1,
                    'code'       => $depto['code'],
                    'active'     => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);

                \DB::table('region_translation')->insert([
                    'region_id' => $regionId,
                    'locale'    => 'es',
                    'name'      => $depto['name'],
                ]);
            }
        }

        $this->command->info('✅ 9 Departamentos de Bolivia insertados.');

        // ─────────────────────────────────────────
        // 2. MARCAS DE VEHÍCULOS
        // ─────────────────────────────────────────
        $marcas = [
            // Internacionales
            'Acura', 'Alfa Romeo', 'Aston Martin', 'Audi', 'Bentley',
            'BMW', 'Bugatti', 'Buick', 'Cadillac', 'Chevrolet',
            'Chrysler', 'Citroën', 'Corvette', 'Daewoo', 'Daihatsu',
            'Dodge', 'Ferrari', 'FIAT', 'Ford', 'GMC',
            'Honda', 'Hummer', 'Hyundai', 'Infiniti', 'Isuzu',
            'Jaguar', 'Jeep', 'Kia', 'Lamborghini', 'Land Rover',
            'Lexus', 'Lincoln', 'Lotus', 'Maserati', 'Maybach',
            'Mazda', 'McLaren', 'Mercedes-Benz', 'MG', 'MINI',
            'Mitsubishi', 'Nissan', 'Oldsmobile', 'Peugeot', 'Plymouth',
            'Pontiac', 'Porsche', 'Ram', 'Renault', 'Rolls-Royce',
            'Saab', 'Saturn', 'Scion', 'Shelby', 'Smart',
            'Subaru', 'Suzuki', 'Tesla', 'Toyota', 'Volkswagen',
            'Volvo',
            // Marcas chinas
            'BYD', 'Chery', 'DFSK', 'DongFeng', 'FAW',
            'Foton', 'GAC', 'Geely', 'Great Wall', 'Haval',
            'JAC', 'JMC', 'Lifan', 'MG (China)', 'NIO',
            'Roewe', 'SAIC', 'Shineray', 'Wuling', 'Zotye',
            // Marcas de motos
            'Bajaj', 'Boxer', 'CF Moto', 'Dayang', 'Hero',
            'Honda Moto', 'Kawasaki', 'KTM', 'Loncin', 'Maranello',
            'Pegasus', 'Royal Enfield', 'Shineray Moto', 'Suzuki Moto',
            'TVS', 'Yamaha', 'Zongshen',
            // Marcas de triciclos / tuk-tuk (Torito)
            'Ape Piaggio', 'Bajaj RE', 'Dayun', 'Haojue',
            'Jialing', 'Loncin Torito', 'MOTO3', 'Qlink',
            'Saige', 'Shineray Torito', 'Tolsen', 'TVS King',
            // Locales / otras
            'Otro',
        ];

        $insertadas = 0;
        foreach ($marcas as $orden => $nombre) {
            $exists = \App\VehicleBrand::where('name', $nombre)->first();
            if (!$exists) {
                \App\VehicleBrand::create([
                    'name'   => $nombre,
                    'active' => true,
                ]);
                $insertadas++;
            }
        }

        $this->command->info("✅ {$insertadas} Marcas de vehículos insertadas.");
        $this->command->info('Seeder BoliviaDataSeeder completado exitosamente.');
    }
}
