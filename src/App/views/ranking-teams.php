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
    <link rel="stylesheet" href="./css/ranking-team.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="./js/maps.js" defer></script>
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
