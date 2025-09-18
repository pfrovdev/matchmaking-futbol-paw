<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$queryParams = $_GET;
unset($queryParams['page']);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

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
    <link rel="stylesheet" href="./css/spinner.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script src="./js/maps.js" defer></script>
    <script src="./js/components/modals.js"></script>
    <script src="./js/pages/search-team.js" defer></script>
    <script src="./js/components/spinner.js" defer></script>
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
                                "name" => htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8'),
                                "alternateName" => htmlspecialchars($equipo->getAcronimo() ?? '', ENT_QUOTES, 'UTF-8'),
                                "identifier" => [
                                    "@type" => "PropertyValue",
                                    "name" => "Elo Ranking",
                                    "value" => $equipo->getEloActual()
                                ],
                                "description" => htmlspecialchars($equipo->getLema() ?? '', ENT_QUOTES, 'UTF-8'),
                                "url" => "/team-profile.php?id=" . $equipo->getIdEquipo(),
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
    <?php
    $estaLogueado = !!$miEquipo->getIdEquipo();
    require "parts/header.php";
    ?>
    <?php require "parts/side-navbar.php"; ?>

    <main>
        <header>
            <h1>Buscar desafío</h1>
            <p>Busca rivales, por rango o zona</p>
        </header>
        <?php
        if (!empty($errors)) {
            $type = "error";
            $messages = $errors;
            include __DIR__ . "/parts/alert.php";
        }
        ?>

        <section class="search-container">
            <aside class="left-size">
                <!-- Botones Mobile -->
                <div class="mobile-controls">
                    <button id="openFiltersBtn" class="mobile-btn">Filtros</button>
                    <button id="openOrderBtn" class="mobile-btn">Ordenar</button>
                </div>

                <!-- Modal filtros (mobile) -->
                <div id="filtersModal" class="mobile-modal hidden">
                    <div class="mobile-modal-content">
                        <button class="close-modal">&times;</button>
                        <h2>Filtros</h2>

                        <form method="get" action="/search-team" data-form="search">
                            <input type="text" name="nombre" placeholder="Ejemplo FC"
                                value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>" />
                            <input type="hidden" name="id_nivel_elo"
                                value="<?= htmlspecialchars($rangoSelectedId ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="orden"
                                value="<?= htmlspecialchars($orden, ENT_QUOTES, 'UTF-8') ?>">

                            <button class="button" type="submit">Buscar</button>
                        </form>

                        <?php require "parts/filtro-por-rango.php"; ?>

                        <section class="mobile-location-filter">
                            <h2 id="zona-busqueda">Zona de búsqueda</h2>
                            <form method="GET" data-form="map-mobile">
                                <input type="hidden" id="latMobile" name="lat"
                                    value="<?= htmlspecialchars($_GET['lat'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                                <input type="hidden" id="lngMobile" name="lng"
                                    value="<?= htmlspecialchars($_GET['lng'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />

                                <label for="radiusSliderMobile">Radio del área (km)</label>
                                <div class="input-group">
                                    <input type="range" id="radiusSliderMobile" name="radius_km" min="0.1" max="10"
                                        step="0.1"
                                        value="<?= htmlspecialchars($_GET['radius_km'] ?? 1, ENT_QUOTES, 'UTF-8') ?>">
                                    <span id="radiusValueMobile">
                                        <?= htmlspecialchars($_GET['radius_km'] ?? 1.0, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <button class="button" type="submit">Aplicar</button>
                            </form>

                            <figure>
                                <div id="map-mobile"
                                    data-team-zone="<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </figure>
                        </section>
                    </div>
                </div>

                <!-- Modal orden (mobile) -->
                <div id="orderModal" class="mobile-modal hidden">
                    <div class="mobile-modal-content">
                        <button class="close-modal">&times;</button>
                        <h2>Ordenar por</h2>
                        <form class="radio-btns" method="get" data-form="orden">
                            <input type="hidden" name="id_nivel_elo"
                                value="<?= htmlspecialchars($rangoSelectedId ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                    </div>
                </div>

                <!-- Filtros desktop -->
                <div class="desktop-filters">
                    <h2>Filtros</h2>
                    <form method="get" action="/search-team" data-form="search">
                        <input type="text" name="nombre" placeholder="Ejemplo FC"
                            value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>" />

                        <input type="hidden" name="id_nivel_elo"
                            value="<?= htmlspecialchars($rangoSelectedId ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="orden" value="<?= htmlspecialchars($orden, ENT_QUOTES, 'UTF-8') ?>">

                        <button class="button" type="submit">Buscar</button>
                    </form>

                    <?php if (!empty($listLevelsElo)): ?>
                        <?php require "parts/filtro-por-rango.php"; ?>
                    <?php endif; ?>
                </div>

                <!-- Orden desktop -->
                <div class="desktop-order">
                    <h2>Ordenar por</h2>
                    <form class="radio-btns" method="get" data-form="orden">
                        <input type="hidden" name="id_nivel_elo"
                            value="<?= htmlspecialchars($rangoSelectedId ?? '', ENT_QUOTES, 'UTF-8') ?>">

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
                </div>

                <!-- Zona búsqueda desktop -->
                <section class="right-size">
                    <h2 id="zona-busqueda">Zona de búsqueda</h2>
                    <form method="GET" data-form="map">
                        <input type="hidden" id="latDesktop" name="lat"
                            value="<?= htmlspecialchars($_GET['lat'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                        <input type="hidden" id="lngDesktop" name="lng"
                            value="<?= htmlspecialchars($_GET['lng'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />

                        <label for="radiusSliderDesktop">Radio del área (km)</label>
                        <div class="input-group">
                            <input type="range" id="radiusSliderDesktop" name="radius_km" min="0.1" max="10" step="0.1"
                                value="<?= htmlspecialchars($_GET['radius_km'] ?? 1, ENT_QUOTES, 'UTF-8') ?>">
                            <span id="radiusValueDesktop">
                                <?= htmlspecialchars($_GET['radius_km'] ?? 1.0, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        <button class="button" type="submit">Enviar</button>
                    </form>
                </section>

                <figure class="filter-map">
                    <div id="map"
                        data-team-zone="<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </figure>

                <section class="limpiar-filtros">
                    <button id="clearFilters" class="button" type="button">Limpiar filtros</button>
                </section>
                <div id="modalOverlayInfo" class="modal-overlay-info"></div>
            </aside>

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
    <?php
    if ($success)
        require __DIR__ . '/parts/modal-success.php';
    ?>
    <?php require "parts/footer.php"; ?>
</body>

</html>