<?php
$queryParams = $_GET;
unset($queryParams['page']);

$nombre = trim($_GET['nombre'] ?? '');
$rangoSelected = $_GET['rango'] ?? '';
$rangoSelectedId = isset($_GET['id_nivel_elo']) ? (int) $_GET['id_nivel_elo'] : '';
$rangoSelected = $mapaRangos[$rangoSelectedId] ?? '';

$mapaRangos = [
    1 => 'Principiante',
    2 => 'Amateur',
    3 => 'SemiPro',
    4 => 'Profesional',
];

$rangoSelectedId = $_GET['id_nivel_elo'] ?? null;


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Equipo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="./css/parts/map.css">
    <link rel="stylesheet" href="./css/search-team.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script src="./js/maps.js" defer></script>
    <script src="./js/sidebar.js"></script>
    <script src="./js/filtros.js" defer></script>

    <?php if (!empty($equipos)): ?>
        <script type="application/ld+json">
            <?= json_encode([
                "@context" => "https://schema.org",
                "@type" => "ItemList",
                "name" => "Resultados de búsqueda de equipos",
                "numberOfItems" => count($equipos),
                "itemListElement" => array_map(function ($equipo, $index) {
                    return [
                        "@type" => "ListItem",
                        "position" => $index + 1,
                        "item" => [
                            "@type" => "SportsTeam",
                            "name" => htmlspecialchars($equipo->getNombreEquipo()),
                            "alternateName" => htmlspecialchars($equipo->getAcronimo() ?? ''),
                            "identifier" => [
                                "@type" => "PropertyValue",
                                "name" => "Elo Ranking",
                                "value" => $equipo->getEloActual()
                            ],
                            "description" => htmlspecialchars($equipo->getLema() ?? ''),
                            "url" => "/team-profile.php?id=" . $equipo->getIdEquipo(), // ajustá esta URL
                            "location" => [
                                "@type" => "Place",
                                "geo" => [
                                    "@type" => "GeoCoordinates",
                                    "latitude" => $equipo->getLatitud(),
                                    "longitude" => $equipo->getLongitud()
                                ]
                            ]
                        ]
                    ];
                }, $equipos, array_keys($equipos))
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
        </script>
    <?php endif; ?>

</head>

<body>
    <?php require "parts/header.php"; ?>
    <?php require "parts/side-navbar.php"; ?>

    <main>
        <header>
            <h1>Buscar desafío</h1>
            <p>Busca rivales, por rango o zona</p>
        </header>

        <section class="search-container">
            <!-- === COLUMNA IZQUIERDA: FILTROS y MAPA === -->
            <aside class="left-size">
                <h2>Filtros:</h2>

                <h2 id="buscar-nombre">Buscar por nombre</h2>
                <form method="get" action="/search-team">
                    <input type="text" id="nombre" name="nombre" placeholder="Ejemplo FC"
                        value="<?= htmlspecialchars($nombre) ?>" />

                    <input type="hidden" name="id_nivel_elo" value="<?= htmlspecialchars($rangoSelectedId ?? '') ?>">
                    <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                    <button class="filter-button" type="submit">Buscar</button>
                </form>


                <div class="group-filters">
                    <!-- FILTRAR POR RANGO -->
                    <?php if (!empty($listLevelsElo)): ?>
                        <?php require "parts/filtro-por-rango.php"; ?>
                    <?php endif; ?>

                    <!-- ORDENAR -->
                    <section class="order-container" aria-labelledby="ordenar">
                        <h2 id="ordenar">Ordenar por</h2>
                        <form id="ordenForm" class="radio-btns" method="get">
                            <input type="hidden" name="id_nivel_elo"
                                value="<?= htmlspecialchars($rangoSelectedId ?? '') ?>">
                            <input type="hidden" name="lat" value="<?= htmlspecialchars($_GET['lat'] ?? '') ?>">
                            <input type="hidden" name="lng" value="<?= htmlspecialchars($_GET['lng'] ?? '') ?>">
                            <input type="hidden" name="radius_km"
                                value="<?= htmlspecialchars($_GET['radius_km'] ?? '') ?>">

                            <label>
                                <input type="radio" name="orden" value="desc" <?= $orden === 'desc' ? 'checked' : '' ?>>
                                Menor a mayor ELO
                            </label><br>

                            <label>
                                <input type="radio" name="orden" value="asc" <?= $orden === 'asc' ? 'checked' : '' ?>>
                                Mayor a menor ELO
                            </label><br>

                            <label>
                                <input type="radio" name="orden" value="alpha" <?= $orden === 'alpha' ? 'checked' : '' ?>>
                                Alfabéticamente
                            </label><br>
                        </form>
                    </section>
                </div>
                <!-- BUSQUEDA POR MAPA -->
                <section class="right-size">
                    <h2 id="zona-busqueda">Zona de búsqueda</h2>
                    <form id="mapForm" method="GET">
                        <input type="hidden" id="lat" name="lat" readonly
                            value="<?= htmlspecialchars($_GET['lat'] ?? '') ?>" />
                        <input type="hidden" id="lng" name="lng" readonly
                            value="<?= htmlspecialchars($_GET['lng'] ?? '') ?>" />

                        <label for="radiusSlider">Radio del área (km)</label>
                        <div class="input-group">
                            <input type="range" id="radiusSlider" name="radius_km" min="0.1" max="10" step="0.1"
                                value="<?= htmlspecialchars($_GET['radius_km'] ?? 1) ?>">
                            <span id="radiusValue">
                                <?= htmlspecialchars($_GET['radius_km'] ?? 1.0) ?>
                            </span>
                        </div>
                        <button class="filter-button" type="submit">Enviar</button>
                    </form>
                </section>

                <figure>
                    <div id="map" data-team-zone="<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '') ?>">
                    </div>
                </figure>

                <section class="limpiar-filtros">
                    <button id="clearFilters" class="filter-button" type="button">Limpiar filtros</button>
                </section>
            </aside>

            <!-- === COLUMNA DERECHA: BÚSQUEDA, LISTA y PAGINACIÓN === -->
            <section class="rigth-size">
                <ul class="lista-equipos">
                    <?php if (empty($equipos)): ?>
                        <li>No se encontraron equipos.</li>
                    <?php else:
                        foreach ($equipos as $equipo): ?>
                            <li>
                                <?php require __DIR__ . '/parts/tarjeta-envio-desafio.php'; ?>
                            </li>
                            <br>
                        <?php endforeach; ?>
                        <?php require "parts/pagination.php"; ?>
                    <?php endif; ?>
                </ul>
            </section>
        </section>
    </main>

    <?php require "parts/footer.php"; ?>
</body>

</html>