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

$jugados = 0;
$goles = 0;
$asistencias = 0;
$amarillas = 0;
$rojas = 0;
$ganados = 0;
$empatados = 0;
$perdidos = 0;
$promedioGoles = 0;
$promedioAsistencias = 0;
$promedioAmarillas = 0;

$mostrar_estadisticas = true;
if ($estadisticas) {
    $jugados = $estadisticas->getJugados();
    $goles = $estadisticas->getGoles();
    $golesEnContra = $resultadosPartidosEstadisticas['goles_en_contra'] ?? 0;
    $asistencias = $estadisticas->getAsistencias();
    $amarillas = $estadisticas->getTarjetasAmarillas();
    $rojas = $estadisticas->getTarjetasRojas();
    $ganados = $estadisticas->getGanados();
    $empatados = $estadisticas->getEmpatados();
    $perdidos = $estadisticas->getPerdidos();
    $promedioGolesEnContra = $jugados > 0 ? round($golesEnContra / $jugados, 2) : 0;
    $diferenciaGol = $goles - $golesEnContra;
    $eloMasAlto = $resultadosPartidosEstadisticas['elo_mas_alto'] ?? 0;

    $promedioGoles = $jugados > 0 ? round($goles / $jugados, 2) : 0;
    $promedioAsistencias = $jugados > 0 ? round($asistencias / $jugados, 2) : 0;
    $promedioAmarillas = $jugados > 0 ? round($amarillas / $jugados, 2) : 0;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detalle del Equipo <?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="Detalle del equipo. Consulta su deportividad, lema y rendimiento." />
    <link rel="stylesheet" href="./css/details-team.css" />
    <script src="/js/sidebar.js"></script>
    <script src="/js/components/graficosEstadisticas.js"></script>
</head>

<body>
    <?php
    $estaLogueado = !!$miEquipo->getIdEquipo();
    require "parts/header.php";
    ?>
    <?php require "parts/side-navbar.php"; ?>
    <main>
        <?php if (empty($equipo)): ?>
            <li>No se encontraro al equipo.</li>
        <?php else: ?>
            <section class="details-team-container">
                <?php require __DIR__ . '/parts/tarjeta-equipo.php'; ?>
            </section>
        <?php endif; ?>
    </main>
    <?php require "parts/footer.php"; ?>
</body>
</html>