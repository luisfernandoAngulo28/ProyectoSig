<?php

use Illuminate\Database\Seeder;

class FederacionesSeeder extends Seeder
{
    /**
     * Inserta las Federaciones de Moto Taxi como Organizations (type=company)
     * para que aparezcan en el formulario de registro de conductor.
     *
     * Ejecutar con: php artisan db:seed --class=FederacionesSeeder
     */
    public function run()
    {
        $federaciones = [
            'FEDERACION DE MOTO TAXI "COTOCA"',
            'FEDERACION DE MOTO TAXI "MINERO"',
            'FEDERACION DE MOTO TAXI "SAN JULIAN"',
            'FEDERACION DE MOTO TAXI "YAPACANI"',
            'FEDERACION MOTO TAXI WARNES',
            'FEDERACION MOTO TAXI PORONGO',
            'FEDERACION DE MOTO TAXIS "LA GUARDIA"',
            'FEDERACION DE MOTO TAXIS "MONTERO"',
            'FEDERACION DE MOTO TAXI MAIRANA',
            'FEDERACION MOTO TAXI POSTERVALLE',
            'FEDERACION DE MOTO TAXI VALLEGRANDE',
            'FEDERACION DE MOTO TAXI GUAYARAMERIN',
            'FEDERACION DE AUTO TAXI YAPACANI',
            'FEDERACION MOTO TAXI EL TORNO',
            'FEDERACION DE AUTO TAXI EL TORNO',
            'FEDERACION DE AUTO TAXI "LA GUARDIA"',
            'FEDERACION SATELITE NORTE',
        ];

        $insertadas = 0;
        $saltadas   = 0;

        foreach ($federaciones as $nombre) {
            // Evitar duplicados si se corre más de una vez
            $existe = \App\Organization::where('name', $nombre)->first();
            if (!$existe) {
                \App\Organization::create([
                    'name'   => $nombre,
                    'type'   => 'company',
                    'active' => 1,
                ]);
                $insertadas++;
            } else {
                $saltadas++;
            }
        }

        $this->command->info("✅ {$insertadas} Federaciones insertadas.");
        if ($saltadas > 0) {
            $this->command->warn("⚠  {$saltadas} ya existían (omitidas).");
        }
        $this->command->info('Seeder FederacionesSeeder completado.');
    }
}
