<?php
// src/App/views/profile.php
// Variables: $miEquipo, $comentariosPag, $desafiosRecib, $nivelDesc, $deportividad, $ultimoPartidoJugado, $page, $per, $order, $dir
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Perfil público del equipo de futbol">
    <title>Perfil - <?= htmlspecialchars($miEquipo->fields['nombre'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="./css/spinner.css">

    <script type="module" src="js/pages/Dashboard.js" defer></script>
    <script src="./js/components/spinner.js" defer></script>
    <script src="/js/sidebar.js"></script>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SportsTeam",
            "name": "<?= htmlspecialchars($equipoBanner->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>",
            "alternateName": "<?= htmlspecialchars($miEquipo->fields['acronimo'], ENT_QUOTES, 'UTF-8') ?>",
            "description": "<?= htmlspecialchars($equipoBanner->getLema(), ENT_QUOTES, 'UTF-8') ?>",
            "sport": "Soccer",
            "identifier": {
                "@type": "PropertyValue",
                "name": "Elo Ranking",
                "value": "<?= htmlspecialchars($equipoBanner->getEloActual(), ENT_QUOTES, 'UTF-8') ?>"
            },
            "gender": "<?= htmlspecialchars($equipoBanner->getTipoEquipo(), ENT_QUOTES, 'UTF-8') ?>",
            <?php if ($equipoBanner->getUrlFotoPerfil()): ?>
                                            "image": "<?= htmlspecialchars($equipoBanner->getUrlFotoPerfil(), ENT_QUOTES, 'UTF-8') ?>",
            <?php endif; ?>
            "location": {
            "@type": "Place",
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": <?= $equipoBanner->getLatitud() ?>,
                    "longitude": <?= $equipoBanner->getLongitud() ?>
                }
            }
        }
    </script>
</head>

<body data-profile-id="<?= $equipoVistoId ?>" data-is-owner="false">

    <?php
        $estaLogueado = !!$miEquipo->getIdEquipo();
        require "parts/header.php";
    ?>
    <?php require "parts/side-navbar.php"; ?>
    <main>

        <div class="dashboard-container">
            <?php
                if (!empty($errors)) {
                    $type = "error";
                    $messages = $errors;
                    include __DIR__ . "/parts/alert.php";
            }
            ?>
            <!-- GRID PRINCIPAL -->
            <div class="dashboard-grid">

                <!-- Columna Izquierda -->
                <section class="col-left">
                    <!-- Card 1: Perfil -->
                    <div class="card perfil-card">
                        <div class="perfil-foto">
                            <?php if ($equipoBanner->getUrlFotoPerfil()): ?>
                                <img src="<?= htmlspecialchars($equipoBanner->getUrlFotoPerfil(), ENT_QUOTES, 'UTF-8') ?>"
                                    alt="Foto de perfil">
                            <?php else: ?>
                                <div class="placeholder-foto">Sin foto de equipo</div>
                            <?php endif; ?>
                        </div>
                        <div class="perfil-info">
                            <h2>
                                <?= htmlspecialchars($equipoBanner->getNombreEquipo(), ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($miEquipo->fields['acronimo'], ENT_QUOTES, 'UTF-8') . ")" ?>
                            </h2>
                            <p class="lema"><?= htmlspecialchars($equipoBanner->getLema(), ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="sport-icons">
                                Deportividad:
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $equipoBanner->getDeportividad()): ?>
                                        <span class="icon">⚽</span>
                                    <?php else: ?>
                                        <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?= "(" . $cantidadDeVotos . ")" ?>
                            </div>
                            <p>Género: <?= htmlspecialchars($equipoBanner->getTipoEquipo(), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php foreach ($listLevelsElo as $row):
                                $id = $row['id_nivel_elo'];
                                $label = $row['descripcion'];
                                $desde = (float) $row['desde'];
                                $hasta = (float) $row['hasta'];
                                $colorInicio = $row['color_inicio'];
                                $colorFin = $row['color_fin'];
                                $gradient = "linear-gradient(90deg, $colorInicio, $colorFin)";
                                $eloActual = (float) $equipoBanner->getEloActual();

                                if ($eloActual >= $desde && $eloActual <= $hasta):
                                    $porcentaje = ($hasta > $desde)
                                        ? min(100, max(0, (($eloActual - $desde) / ($hasta - $desde)) * 100))
                                        : 0;
                                    ?>
                                    <div class="elo-bar">
                                        <span class="label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                        <div class="bar-bg">
                                            <div class="bar-fill" style="background: <?= htmlspecialchars($gradient, ENT_QUOTES, 'UTF-8') ?>;
                                                                        width: <?= round($porcentaje, 2) ?>%">
                                            </div>
                                        </div>
                                        <div class="elo-values">
                                            <span>Elo: <?= htmlspecialchars($eloActual, ENT_QUOTES, 'UTF-8') ?></span> /
                                            <span><?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                    <?php
                                endif;
                            endforeach;
                            ?>
                            <form action="/desafios" method="POST" class="form-desafiar">
                                <input type="hidden" name="id_equipo_desafiar"
                                    value="<?= htmlspecialchars($equipoBanner->getIdEquipo(), ENT_QUOTES, 'UTF-8') ?>">

                                <button type="submit" name="submit_my_form" class="btn btn-desafiar">
                                    <span class="btn-text">Desafiar</span>
                                    <span class="spinner" style="display:none;"></span>
                                </button>
                            </form>
                        </div>
                    </div>


                    <!-- Card 2: historial de partidos -->
                    <div class="card history-card">
                        <h3 class="title-subsection">Historial de partidos</h3>
                        <div class="comment-filter">
                            <label for="filtroHistorial">Ordenar por:</label>
                            <select id="filtroHistorial" name="filtroHistorial">
                                <option value="fecha_finalizacion-DESC" selected>Más recientes</option>
                                <option value="fecha_finalizacion-ASC">Más antiguos</option>
                            </select>
                        </div>
                        <div id="history-list" class="history-list"></div>
                        <div id="history-pagination" class="pagination"></div>
                    </div>

                </section>

                <!-- Columna Derecha -->
                <aside class="col-right">
                    <!-- Card 4: Estadísticas -->
                    <?php if ($estadisticas): ?>
                        <section class="card stats-card">
                            <h3 class="title-subsection">Estadísticas</h3>
                            <ul>
                                <li><strong>Partidos jugados:</strong>
                                    <?= htmlspecialchars($jugados, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Victorias:</strong> <?= htmlspecialchars($ganados, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Empates:</strong> <?= htmlspecialchars($empatados, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Derrotas:</strong> <?= htmlspecialchars($perdidos, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Goles a favor:</strong> <?= htmlspecialchars($goles, ENT_QUOTES, 'UTF-8') ?>
                                    (<?= $promedioGoles ?>
                                    por partido)
                                </li>
                                <li><strong>Goles en contra:</strong>
                                    <?= htmlspecialchars($golesEnContra, ENT_QUOTES, 'UTF-8') ?>
                                    (<?= $promedioGolesEnContra ?> por partido)</li>
                                <li><strong>Diferencia de gol:</strong>
                                    <?= $diferenciaGol >= 0 ? '+' : '' ?>     <?= $diferenciaGol ?></li>
                                <li><strong>ELO actual:</strong>
                                    <?= htmlspecialchars($equipoBanner->getEloActual(), ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>ELO más alto:</strong>
                                    <?= htmlspecialchars($eloMasAlto, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Tarjetas amarillas totales:</strong>
                                    <?= htmlspecialchars($amarillas, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Tarjetas amarillas por partido:</strong> <?= $promedioAmarillas ?></li>
                                <li><strong>Tarjetas rojas totales:</strong>
                                    <?= htmlspecialchars($rojas, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Asistencias:</strong>
                                    <?= htmlspecialchars($asistencias, ENT_QUOTES, 'UTF-8') ?></li>
                                <li><strong>Asistencias por partido:</strong> <?= $promedioAsistencias ?></li>
                                <?php if (!empty($resultadosPartidosEstadisticas['ultimos_5_partidos'])): ?>
                                    <li><strong>Últimos 5 partidos:</strong>
                                        <?= implode(' ', $resultadosPartidosEstadisticas['ultimos_5_partidos']) ?>
                                    </li>
                                <?php else: ?>
                                    <li><strong>Últimos 5 partidos:</strong> No hay partidos aún.</li>
                                <?php endif; ?>
                            </ul>
                        </section>
                    <?php else: ?>
                        <section class="card stats-card">
                            <h3 class="title-subsection">Estadísticas</h3>
                            <p>Este equipo aún no tiene estadísticas registradas.</p>
                        </section>
                    <?php endif; ?>

                    <!-- Card 5: Comentarios -->
                    <div class="card comments-card">
                        <h3 class="title-subsection">Comentarios</h3>
                        <div class="comment-filter">
                            <label for="filtroComentarios">Ordenar por:</label>
                            <select id="filtroComentarios" name="filtroComentarios">
                                <option value="fecha_creacion-DESC" selected>Más recientes</option>
                                <option value="fecha_creacion-ASC">Más antiguos</option>
                                <option value="deportividad-DESC">Deportividad (mayor a menor)</option>
                                <option value="deportividad-ASC">Deportividad (menor a mayor)</option>
                            </select>
                        </div>
                        <ul id="comment-list" class="comment-list"></ul>
                        <div id="comment-pagination" class="pagination"></div>
                    </div>
                </aside>

            </div>

    </main>

    <?php require "parts/footer.php"; ?>
    <script>
        const levelsEloMap = <?= json_encode(array_map(function ($row) {
            return [
                'descripcion' => $row['descripcion'],
                'color_inicio' => $row['color_inicio'],
                'color_fin' => $row['color_fin'],
            ];
        }, $listLevelsElo)) ?>;
    </script>
</body>

</html>