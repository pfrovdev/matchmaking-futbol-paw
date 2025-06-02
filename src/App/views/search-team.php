<?php
$queryParams = $_GET;
unset($queryParams['page']);

$nombre        = trim($_GET['nombre'] ?? '');
$rangoSelected = $_GET['rango']  ?? '';
$rangoSelectedId = isset($_GET['id_nivel_elo']) ? (int) $_GET['id_nivel_elo'] : '';
$rangoSelected   = $mapaRangos[$rangoSelectedId] ?? '';

$mapaRangos = [
    1 => 'Principiante',
    2 => 'Amateur',
    3 => 'SemiPro',
    4 => 'Profesional',
];


if ($nombre !== '') {
    $equipos = array_filter($equipos, function($e) use($nombre) {
        return mb_stripos($e['nombre'], $nombre) !== false;
    });
}

if ($rangoSelected !== '') {
    $equipos = array_filter($equipos, function($e) use($rangoSelected, $mapaRangos) {
        return isset($mapaRangos[$e['id_nivel_elo']])
            && $mapaRangos[$e['id_nivel_elo']] === $rangoSelected;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Equipo</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="./css/search-team.css">
    <script src="./js/maps.js" defer></script>
</head>
<body>
    <?php require "parts/header.php"; ?>
    <?php require "parts/side-navbar.php"; ?>

    <main>
        <header>
            <h1>Buscar desafío</h1>
            <p>Busca rivales, por rango o zona</p>
        </header>

        <section class="grid-layout">
            <!-- === COLUMNA IZQUIERDA: BÚSQUEDA, LISTA y PAGINACIÓN === -->
            <section class="left-size" aria-labelledby="buscar-nombre">
                <h2 id="buscar-nombre">Buscar por nombre</h2>
                <form method="get" action="/search-team">
                    <input type="text" id="nombre" name="nombre" placeholder="Ejemplo FC"
                           value="<?= htmlspecialchars($nombre) ?>" />

                    <input type="hidden" name="id_nivel_elo" value="<?= htmlspecialchars($rangoSelectedId) ?>">
                    <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                    <button type="submit">Buscar</button>
                </form>

                <ul class="lista-equipos">
                    <?php if (empty($equipos)): ?>
                        <li>No se encontraron equipos.</li>
                    <?php else: foreach ($equipos as $equipo): ?>
                        <li>
                            <?php require __DIR__ . '/parts/tarjeta-envio-desafio.php'; ?>
                        </li>
                        <br>
                    <?php endforeach; endif; ?>
                </ul>
                <?php require "parts/pagination.php"; ?>
            </section>

            <!-- === COLUMNA DERECHA: FILTROS y MAPA === -->
            <aside>
                <!-- FILTRAR POR RANGO -->
                <section aria-labelledby="filtro-rango">
                    <h2 id="filtro-rango">Filtrar por rango</h2>
                    <form method="get">

                        <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                        <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                        <ul class="filtros-rango">
                            <?php foreach ($mapaRangos as $id => $label): 
                                $clase = strtolower($label);
                                $activo = ($rangoSelectedId == $id) ? ' activo' : '';
                            ?>
                                <li>
                                    <button type="submit"
                                        name="id_nivel_elo"
                                        value="<?= $id ?>"
                                        class="<?= strtolower($label) . $activo ?>">
                                        <?= $label ?>>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </form>
                </section>

                <!-- ORDENAR -->
                <section aria-labelledby="ordenar">
                    <h2 id="ordenar">Ordenar por</h2>
                    <form class="radio-btns" method="get">

                        <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                        <input type="hidden" name="id_nivel_elo"  value="<?= htmlspecialchars($rangoSelectedId) ?>">

                        <label>
                            <input type="radio" name="orden" value="desc"
                                <?= $orden === 'desc' ? 'checked' : '' ?>>
                            Menor a mayor ELO
                        </label><br>

                        <label>
                            <input type="radio" name="orden" value="asc"
                                <?= $orden === 'asc' ? 'checked' : '' ?>>
                            Mayor a menor ELO
                        </label><br>

                        <label>
                            <input type="radio" name="orden" value="alpha"
                                <?= $orden === 'alpha' ? 'checked' : '' ?>>
                            Alfabéticamente
                        </label><br>

                        <button type="submit">Ordenar</button>
                    </form>
                </section>

                <!-- BUSQUEDA POR MAPA -->
                <section aria-labelledby="zona-busqueda">
                    <h2 id="zona-busqueda">Zona de búsqueda</h2>
                    <form id="mapForm" method="GET">
                        <div class="input-group">
                            <label for="lat">Latitud:</label>
                            <input type="text" id="lat" name="lat" readonly value="<?= htmlspecialchars($_GET['lat'] ?? '') ?>" />
                        </div>
                        <div class="input-group">
                            <label for="lng">Longitud:</label>
                            <input type="text" id="lng" name="lng" readonly value="<?= htmlspecialchars($_GET['lng'] ?? '') ?>" />
                        </div>
                        <div class="input-group">
                            <label for="radiusSlider">Radio del área (km)</label>
                        </div>
                        <div class="input-group">
                        <input type="range" id="radiusSlider" name="radius_km"
                            min="0.1" max="10" step="0.1"
                            value="<?= htmlspecialchars($_GET['radius_km'] ?? 1) ?>">
                        <span id="radiusValue"><?= htmlspecialchars($_GET['radius_km'] ?? 1.0) ?></span>
                        </div>
                        <button type="submit">Enviar</button>
                    </form>
                </section>

                <figure>
                    <div id="map"></div>
                </figure>
            </aside>
        </section>
    </main>

    <?php require "parts/footer.php"; ?>
</body>
</html>