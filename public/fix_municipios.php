<?php
/**
 * Script temporal para corregir municipios y federaciones.
 * ⚠️  ELIMINAR ESTE ARCHIVO después de ejecutarlo.
 * Acceder via: https://18.225.57.224/fix_municipios.php?key=AnDre2024Fix
 */

$SECRET_KEY = 'AnDre2024Fix';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    http_response_code(403);
    die('Acceso denegado.');
}

// ── Conexión a la BD ──────────────────────────────────────────────────────────
$host    = 'taxisapp-database.ct8q0g6ei5q7.us-east-2.rds.amazonaws.com';
$db      = 'taxisapp';
$user    = 'admin';
$pass    = 'TaxisApp2024!';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

$log = [];

// ── 1. DEPARTAMENTOS: verificar que existen ───────────────────────────────────
$codigos = ['LP','CB','SC','OR','PT','CH','TJ','BE','PD'];
$regiones = [];
foreach ($codigos as $c) {
    $st = $pdo->prepare("SELECT id FROM regions WHERE code = ?");
    $st->execute([$c]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $regiones[$c] = $row['id'];
        $log[] = "✅ Región {$c} → id={$row['id']}";
    } else {
        $log[] = "⚠️  Región {$c} NO encontrada en la BD";
    }
}

// ── 2. MUNICIPIOS ─────────────────────────────────────────────────────────────
$municipiosPorDepto = [
    'SC' => ['Santa Cruz de la Sierra','Cotoca','Montero','Warnes','La Guardia','El Torno','Porongo',
             'San Julián','Yapacaní','Vallegrande','Mairana','Postervalle','Camiri','Puerto Suárez',
             'San Ignacio de Velasco','Concepción','San José de Chiquitos','Buena Vista',
             'Colpa Bélgica','Okinawa Uno','Pailón','Roboré'],
    'CB' => ['Cochabamba','Quillacollo','Sacaba','Colcapirhua','Tiquipaya','Punata','Cliza',
             'Aiquile','Chimoré','Shinahota','Villa Tunari','Tiraque'],
    'LP' => ['La Paz','El Alto','Viacha','Achacachi','Copacabana','Caranavi','Coroico',
             'Desaguadero','Patacamaya','Pucarani','Guanay'],
    'OR' => ['Oruro','Huanuni','Machacamarca','Caracollo','Challapata','Eucaliptus','Poopó'],
    'PT' => ['Potosí','Uyuni','Llallagua','Villazon','Tupiza','Colquechaca','Uncía','Betanzos'],
    'CH' => ['Sucre','Monteagudo','Camargo','Tarabuco','Padilla','Villa Serrano','Culpina','San Lucas','Poroma'],
    'TJ' => ['Tarija','Yacuiba','Bermejo','Villamontes','Padcaya','Caraparí'],
    'BE' => ['Trinidad','Riberalta','Guayaramerín','San Borja','Santa Ana del Yacuma','Rurrenabaque','Reyes'],
    'PD' => ['Cobija','Porvenir','Filadelfia','Bella Flor','Puerto Rico','San Pedro'],
];

$cityIds = []; // nombre => id
$ins = 0; $exist = 0;

foreach ($municipiosPorDepto as $code => $municipios) {
    if (!isset($regiones[$code])) continue;
    $regionId = $regiones[$code];

    foreach ($municipios as $nombre) {
        // ¿ya existe?
        $st = $pdo->prepare("SELECT id FROM cities WHERE region_id = ? AND name = ?");
        $st->execute([$regionId, $nombre]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $cityIds[$nombre] = $row['id'];
            $exist++;
        } else {
            $st2 = $pdo->prepare("INSERT INTO cities (name, region_id, active, created_at, updated_at) VALUES (?,?,1,NOW(),NOW())");
            $st2->execute([$nombre, $regionId]);
            $id = $pdo->lastInsertId();
            $cityIds[$nombre] = $id;
            $ins++;
            $log[] = "✅ Municipio creado: {$nombre} (depto {$code}, id={$id})";
        }
    }
}
$log[] = "── Municipios: {$ins} creados, {$exist} ya existían ──";

// ── 3. FEDERACIONES → VINCULAR city_id ───────────────────────────────────────
$federaciones = [
    'FEDERACION DE MOTO TAXI "COTOCA"'        => 'Cotoca',
    'FEDERACION DE MOTO TAXI "MINERO"'        => 'Warnes',
    'FEDERACION DE MOTO TAXI "SAN JULIAN"'    => 'San Julián',
    'FEDERACION DE MOTO TAXI "YAPACANI"'      => 'Yapacaní',
    'FEDERACION MOTO TAXI WARNES'             => 'Warnes',
    'FEDERACION MOTO TAXI PORONGO'            => 'Porongo',
    'FEDERACION DE MOTO TAXIS "LA GUARDIA"'   => 'La Guardia',
    'FEDERACION DE MOTO TAXIS "MONTERO"'      => 'Montero',
    'FEDERACION DE MOTO TAXI MAIRANA'         => 'Mairana',
    'FEDERACION MOTO TAXI POSTERVALLE'        => 'Postervalle',
    'FEDERACION DE MOTO TAXI VALLEGRANDE'     => 'Vallegrande',
    'FEDERACION DE MOTO TAXI GUAYARAMERIN'    => 'Guayaramerín',
    'FEDERACION DE AUTO TAXI YAPACANI'        => 'Yapacaní',
    'FEDERACION MOTO TAXI EL TORNO'           => 'El Torno',
    'FEDERACION DE AUTO TAXI EL TORNO'        => 'El Torno',
    'FEDERACION DE AUTO TAXI "LA GUARDIA"'    => 'La Guardia',
    'FEDERACION SATELITE NORTE'               => 'Santa Cruz de la Sierra',
];

$fedIns = 0; $fedUpd = 0; $fedErr = 0;

foreach ($federaciones as $nombre => $municipio) {
    $cityId = $cityIds[$municipio] ?? null;
    if (!$cityId) {
        $log[] = "⚠️  Municipio '{$municipio}' no encontrado para: {$nombre}";
        $fedErr++;
        continue;
    }

    // ¿existe la organización?
    $st = $pdo->prepare("SELECT id, city_id FROM organizations WHERE name = ?");
    $st->execute([$nombre]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $pdo->prepare("UPDATE organizations SET city_id = ?, type = 'company', active = 1 WHERE id = ?")
            ->execute([$cityId, $row['id']]);
        $fedUpd++;
    } else {
        $pdo->prepare("INSERT INTO organizations (name, type, city_id, active, created_at, updated_at) VALUES (?,?,?,1,NOW(),NOW())")
            ->execute([$nombre, 'company', $cityId]);
        $fedIns++;
        $log[] = "✅ Federación insertada: {$nombre} → {$municipio}";
    }
}
$log[] = "── Federaciones: {$fedIns} insertadas, {$fedUpd} actualizadas, {$fedErr} con error ──";

// ── OUTPUT ────────────────────────────────────────────────────────────────────
echo "<pre style='font-family:monospace; background:#111; color:#0f0; padding:20px; font-size:13px;'>";
echo "=== FIX MUNICIPIOS Y FEDERACIONES ===\n\n";
foreach ($log as $line) {
    echo $line . "\n";
}
echo "\n✅ LISTO. Por seguridad, ELIMINA este archivo del servidor ahora.\n";
echo "</pre>";

// ── Auto-eliminar el script ───────────────────────────────────────────────────
// Descomenta si quieres que se borre solo:
// @unlink(__FILE__);
