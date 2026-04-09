<?php

use Illuminate\Database\Seeder;

/**
 * MunicipiosFederacionesSeeder (v2 - compatible con Translatable)
 *
 * La tabla `cities` NO tiene columna `name` directa.
 * El nombre está en `city_translations` (locale='es').
 *
 * Ejecutar:  php artisan db:seed --class=MunicipiosFederacionesSeeder
 */
class MunicipiosFederacionesSeeder extends Seeder
{
    public function run()
    {
        // ─────────────────────────────────────────────────────────────
        // 1. MUNICIPIOS POR DEPARTAMENTO (code => [nombres])
        // ─────────────────────────────────────────────────────────────
        $municipiosPorDepto = [
            'SC' => [
                'Santa Cruz de la Sierra','Cotoca','Montero','Warnes','La Guardia',
                'El Torno','Porongo','San Julián','Yapacaní','Vallegrande','Mairana',
                'Postervalle','Camiri','Puerto Suárez','San Ignacio de Velasco',
                'Concepción','San José de Chiquitos','Buena Vista','Colpa Bélgica',
                'Okinawa Uno','Pailón','Roboré',
            ],
            'CB' => [
                'Cochabamba','Quillacollo','Sacaba','Colcapirhua','Tiquipaya',
                'Punata','Cliza','Aiquile','Chimoré','Shinahota','Villa Tunari','Tiraque',
            ],
            'LP' => [
                'La Paz','El Alto','Viacha','Achacachi','Copacabana','Caranavi',
                'Coroico','Desaguadero','Patacamaya','Pucarani','Guanay',
            ],
            'OR' => ['Oruro','Huanuni','Machacamarca','Caracollo','Challapata','Eucaliptus','Poopó'],
            'PT' => ['Potosí','Uyuni','Llallagua','Villazon','Tupiza','Colquechaca','Uncía','Betanzos'],
            'CH' => ['Sucre','Monteagudo','Camargo','Tarabuco','Padilla','Villa Serrano','Culpina','San Lucas','Poroma'],
            'TJ' => ['Tarija','Yacuiba','Bermejo','Villamontes','Padcaya','Caraparí'],
            'BE' => ['Trinidad','Riberalta','Guayaramerín','San Borja','Santa Ana del Yacuma','Rurrenabaque','Reyes'],
            'PD' => ['Cobija','Porvenir','Filadelfia','Bella Flor','Puerto Rico','San Pedro'],
        ];

        $cityIds       = []; // nombre => city_id
        $cityInserted  = 0;
        $cityExisting  = 0;

        foreach ($municipiosPorDepto as $code => $municipios) {

            // Buscar departamento por código
            $region = \DB::table('regions')->where('code', $code)->first();
            if (!$region) {
                $this->command->warn("⚠  Departamento '{$code}' no encontrado.");
                continue;
            }

            foreach ($municipios as $nombre) {

                // Buscar en city_translations (así es como funciona Translatable)
                $existing = \DB::table('cities')
                    ->join('city_translations', 'cities.id', '=', 'city_translations.city_id')
                    ->where('cities.region_id', $region->id)
                    ->where('city_translations.locale', 'es')
                    ->where('city_translations.name', $nombre)
                    ->select('cities.id')
                    ->first();

                if ($existing) {
                    $cityIds[$nombre] = $existing->id;
                    $cityExisting++;
                } else {
                    // Insertar en cities
                    $cityId = \DB::table('cities')->insertGetId([
                        'region_id'  => $region->id,
                        'active'     => 1,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ]);

                    // Insertar traducción
                    \DB::table('city_translations')->insert([
                        'city_id' => $cityId,
                        'locale'  => 'es',
                        'name'    => $nombre,
                    ]);

                    $cityIds[$nombre] = $cityId;
                    $cityInserted++;
                    $this->command->line("  + Municipio: {$nombre} ({$code}) id={$cityId}");
                }
            }
        }

        $this->command->info("✅ Municipios: {$cityInserted} insertados, {$cityExisting} ya existían.");

        // ─────────────────────────────────────────────────────────────
        // 2. FEDERACIONES → VINCULAR city_id
        // ─────────────────────────────────────────────────────────────
        $federaciones = [
            'FEDERACION DE MOTO TAXI "COTOCA"'       => 'Cotoca',
            'FEDERACION DE MOTO TAXI "MINERO"'       => 'Warnes',
            'FEDERACION DE MOTO TAXI "SAN JULIAN"'   => 'San Julián',
            'FEDERACION DE MOTO TAXI "YAPACANI"'     => 'Yapacaní',
            'FEDERACION MOTO TAXI WARNES'            => 'Warnes',
            'FEDERACION MOTO TAXI PORONGO'           => 'Porongo',
            'FEDERACION DE MOTO TAXIS "LA GUARDIA"'  => 'La Guardia',
            'FEDERACION DE MOTO TAXIS "MONTERO"'     => 'Montero',
            'FEDERACION DE MOTO TAXI MAIRANA'        => 'Mairana',
            'FEDERACION MOTO TAXI POSTERVALLE'       => 'Postervalle',
            'FEDERACION DE MOTO TAXI VALLEGRANDE'    => 'Vallegrande',
            'FEDERACION DE MOTO TAXI GUAYARAMERIN'   => 'Guayaramerín',
            'FEDERACION DE AUTO TAXI YAPACANI'       => 'Yapacaní',
            'FEDERACION MOTO TAXI EL TORNO'          => 'El Torno',
            'FEDERACION DE AUTO TAXI EL TORNO'       => 'El Torno',
            'FEDERACION DE AUTO TAXI "LA GUARDIA"'   => 'La Guardia',
            'FEDERACION SATELITE NORTE'              => 'Santa Cruz de la Sierra',
        ];

        $fedIns = 0; $fedUpd = 0; $fedErr = 0;

        foreach ($federaciones as $nombre => $municipio) {
            $cityId = $cityIds[$municipio] ?? null;

            if (!$cityId) {
                $this->command->warn("⚠  Municipio '{$municipio}' no encontrado para: {$nombre}");
                $fedErr++;
                continue;
            }

            $existe = \App\Organization::where('name', $nombre)->first();
            if ($existe) {
                \DB::table('organizations')->where('id', $existe->id)->update([
                    'city_id'    => $cityId,
                    'type'       => 'company',
                    'active'     => 1,
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
                $fedUpd++;
            } else {
                \DB::table('organizations')->insert([
                    'name'       => $nombre,
                    'type'       => 'company',
                    'city_id'    => $cityId,
                    'active'     => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
                $fedIns++;
                $this->command->line("  + Federación: {$nombre} → {$municipio}");
            }
        }

        $this->command->info("✅ Federaciones: {$fedIns} insertadas, {$fedUpd} actualizadas, {$fedErr} con error.");
        $this->command->info('🎉 MunicipiosFederacionesSeeder completado.');
    }
}
