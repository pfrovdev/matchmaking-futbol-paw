<?php
    $queryParams = $_GET;
    unset($queryParams['page']);

    $rangoSelectedId = $_GET['id_nivel_elo'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ranking de Equipos</title>
    <meta name="description" content="Ranking de los mejores equipos según su nivel ELO. Consulta su deportividad, lema y rendimiento." />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="./css/ranking-team.css" />
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script src="./js/maps.js" defer></script>
    <script src="/js/sidebar.js"></script>

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
            <h1>Ranking equipos</h1>
            <p>Estos son los mejores</p>
        </header>

        <?php if (!empty($listLevelsElo)): ?>
            <?php require "parts/filtro-por-rango.php"; ?>
        <?php endif ;?>

        <?php require "parts/filtro-por-ubicacion.php"; ?>
        
        <ul class="teams-container">
            <?php if (empty($equipos)): ?>
                <li>No se encontraron equipos.</li>
            <?php else: foreach ($equipos as $equipo): ?>
                <li>
                    <?php require __DIR__ . '/parts/tarjeta.php'; ?>
                </li>
                <br>
            <?php endforeach; ?> 
            <?php require "parts/pagination.php"; ?>
        <?php endif; ?>
        </ul>
            
    </main>
  <?php require "parts/footer.php"; ?>
</body>
</html>
