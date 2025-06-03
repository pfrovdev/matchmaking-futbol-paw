<?php
    $equipoDescripcionElo = $equipo->getDescripcionElo();
    $gradient = '';

    foreach ($listLevelsElo as $row) {
        if ($row['descripcion'] === $equipoDescripcionElo) {
            $colorInicio = $row['color_inicio'];
            $colorFin = $row['color_fin'];
            $gradient = "linear-gradient(90deg, $colorInicio, $colorFin)";
            break;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detalle del Equipo <?= htmlspecialchars($equipo->getNombreEquipo()) ?></title>
  <meta name="description" content="Detalle del equipo. Consulta su deportividad, lema y rendimiento." />
  <link rel="stylesheet" href="./css/details-team.css" />
</head>
<body>
    <?php require "parts/header.php"; ?>
    <?php require "parts/side-navbar.php"; ?>
    <main>
        <?php if (empty($equipo)): ?>
            <li>No se encontraro al equipo.</li>
        <?php else: ?>
            <section class="details-team-container">
                <?php require __DIR__ . '/parts/tarjeta.php'; ?>
            </section>
            
        <?php endif; ?>
    </main>
  <?php require "parts/footer.php"; ?>
</body>
</html>
