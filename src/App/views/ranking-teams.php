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
        
        <ul class="teams-container">
            <?php if (empty($equipos)): ?>
                <li>No se encontraron equipos.</li>
            <?php else: foreach ($equipos as $equipo): ?>
                <li>
                    <?php require __DIR__ . '/parts/tarjeta.php'; ?>
                </li>
                <br>
            <?php endforeach; endif; ?>
            <?php require "parts/pagination.php"; ?>
        </ul>
            
    </main>
  <?php require "parts/footer.php"; ?>
</body>
</html>
