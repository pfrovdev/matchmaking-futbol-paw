<?php
// src/App/views/dashboard.php
// Variables: $miEquipo, $comentariosPag (array de Comentario), $desafiosRecib (array de Desafio), $nivelDesc, $deportividad, $ultimoPartidoJugado $page, $per, $order, $dir
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Pagina principal del equipo de futbol del usuario">
  <title>Dashboard - <?= htmlspecialchars($miEquipo->fields['nombre']) ?></title>
  <link rel="stylesheet" href="css/dashboard.css">
  <script type="module" src="js/pages/Dashboard.js" defer></script>
</head>

<body data-profile-id="<?= htmlspecialchars($equipoVistoId, ENT_QUOTES) ?>">

  <?php require "parts/header.php"; ?>
  <?php require "parts/side-navbar.php"; ?>
  <main>

    <div class="dashboard-container">


      <!-- GRID PRINCIPAL -->
      <div class="dashboard-grid">

        <!-- Columna Izquierda -->
        <section class="col-left">
          <!-- Card 1: Perfil -->
          <div class="card perfil-card">
            <div class="perfil-foto">
              <?php if ($equipoBanner->getUrlFotoPerfil()): ?>
                <img src="<?= htmlspecialchars($equipoBanner->getUrlFotoPerfil()) ?>" alt="Foto de perfil">
              <?php else: ?>
                <div class="placeholder-foto">Arrastra-soltar la foto de tu equipo aquí<br>
                  <button class="btn-link">Cargar un documento</button>
                </div>
              <?php endif; ?>
            </div>
            <div class="perfil-info">
              <h2><?= htmlspecialchars($equipoBanner->getNombreEquipo()) . " (" . htmlspecialchars($miEquipo->fields['acronimo']) . ")" ?></h2>
              <p class="lema"><?= htmlspecialchars($equipoBanner->getLema()) ?></p>
              <div class="sport-icons">
                Deportividad:
                <!-- Faltaria hacer algo tipo, hasta la cantidad que me mandan
                pongo pelotitas, y relleno hasta 5 espacios vacios -->
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?php if ($i <= $equipoBanner->getDeportividad()): ?>
                    <span class="icon">⚽</span>
                  <?php else: ?>
                    <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
                  <?php endif; ?>
                <?php endfor; ?>
                <?= "(" . $cantidadDeVotos . ")" ?>
              </div>
              <p>Género: <?= htmlspecialchars($equipoBanner->getTipoEquipo()) ?></p>
              <div class="elo-bar">
                <span class="label"><?= htmlspecialchars($equipoBanner->getDescripcionElo()) ?></span>
                <div class="bar-bg">
                  <div class="bar-fill" style="width:<?= min(100, ($equipoBanner->getEloActual() / 1300) * 100) ?>%"></div>
                </div>
                <div class="elo-values">
                  <span>Elo: <?= htmlspecialchars($equipoBanner->getEloActual()) ?></span> / <span>1300</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Último partido -->
          <div class="card last-match-card">
            <?php if ($historial):
              // Construyo el array $match
              $match = [
                'soyGanador' => $soyGanador,
                'eloChange'  => $eloChange,
                'matchUrl'   => '#',
                'date'       => (new \DateTime($ultimoPartidoJugado->getFechaFinalizacion()))->format('d-m-Y'),
                'home'       => [
                  'abbr'      => $equipoLocal->fields['acronimo'],
                  'name'      => $equipoLocal->fields['nombre'],
                  'logo'      => $equipoLocal->fields['url_foto_perfil'],
                  'tarjetas'  => [
                    'yellow' => $soyGanador
                      ? $ultimoPartidoJugado->getResultadoGanador()->getTarjetasAmarillas()
                      : $ultimoPartidoJugado->getResultadoPerdedor()->getTarjetasAmarillas(),
                    'red'    => $soyGanador
                      ? $ultimoPartidoJugado->getResultadoGanador()->getTarjetasRojas()
                      : $ultimoPartidoJugado->getResultadoPerdedor()->getTarjetasRojas(),
                  ],
                ],
                'away'       => [
                  'abbr'      => $equipoRival->fields['acronimo'],
                  'name'      => $equipoRival->fields['nombre'],
                  'logo'      => $equipoRival->fields['url_foto_perfil'],
                  'tarjetas'  => [
                    'yellow' => $soyGanador
                      ? $ultimoPartidoJugado->getResultadoGanador()->getTarjetasAmarillas()
                      : $ultimoPartidoJugado->getResultadoPerdedor()->getTarjetasAmarillas(),
                    'red'    => $soyGanador
                      ? $ultimoPartidoJugado->getResultadoGanador()->getTarjetasRojas()
                      : $ultimoPartidoJugado->getResultadoPerdedor()->getTarjetasRojas(),
                  ],
                ],
                'score'      => $soyGanador
                  ? $ultimoPartidoJugado->getResultadoGanador()->getGoles() . '-' . $ultimoPartidoJugado->getResultadoPerdedor()->getGoles()
                  : $ultimoPartidoJugado->getResultadoPerdedor()->getGoles() . '-' . $ultimoPartidoJugado->getResultadoGanador()->getGoles(),
              ];

              // Incluyo la tarjeta de historial
              require 'parts/tarjeta-historial.php';

            else: ?>
              <p>No jugó ningún partido aún.</p>
            <?php endif; ?>
          </div>

          <!-- Card 3: Desafíos recibidos -->
          <div class="card challenges-card">
            <h3 class="title-subsection">Últimos desafíos recibidos</h3>
            <div class="comment-filter">
              <label for="filtroDesafios">Ordenar por:</label>
              <select id="filtroDesafios" name="filtroDesafios">
                <option value="fecha_creacion-DESC" selected>Más recientes</option>
                <option value="fecha_creacion-ASC">Más antiguos</option>
              </select>
            </div>
            <ul id="challenge-list" class="challenge-list"></ul>
            <div id="desafios-pagination" class="pagination"></div>
          </div>
        </section>

        <!-- Columna Derecha -->
        <aside class="col-right">
          <!-- Card 4: Estadísticas -->
          <div class="card stats-card">
            <h3 class="title-subsection">Estadísticas</h3>
            <dl>
              <dt>G/P:</dt>
              <dd>1.2</dd>
              <dt>A/P:</dt>
              <dd>1.2</dd>
              <dt>%G/A:</dt>
              <dd>50%</dd>
            </dl>
            <h4>Coleadores</h4>
            <ol>
              <li>Nombre Jugador - 50</li>
              <li>Nombre Jugador - 40</li>
              <li>Nombre Jugador - 30</li>
            </ol>
            <h4>Asistidores</h4>
            <ol>
              <li>Nombre Jugador - 20</li>
              <li>Nombre Jugador - 15</li>
              <li>Nombre Jugador - 10</li>
            </ol>
          </div>

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

        <!-- SECCIÓN INFERIOR: Proximos partidos full-width -->
        <section class="next-matches">
          <h3 class="title-subsection">Próximos partidos</h3>
          <ul class="match-list">

            <?php
            // array $proximosPartidos con todos los $match
            // Cada $match debería tener: 
            //   'name', 'nivel', 'deportividad', 'lema', 'record', 'url_logo'

            foreach ($proximosPartidos as $match):
              $equipo = $match->getEquipo();
              $equipoResultados = $equipo->getResultadosEquipo();
              $isFinalizado = $match->getFinalizado();
              $match = [
                'name' => $equipo->getNombreEquipo(),
                'nivel' => $equipo->getDescripcionElo(),
                'deportividad' => $equipo->getDeportividad(),
                'lema' => $equipo->getLema(),
                'record' => $equipoResultados['ganados'] . '-' . $equipoResultados['perdidos'] . '-' . $equipoResultados['empates'],
                'url_logo' => $equipo->getUrlFotoPerfil(),
                'profile-link' =>  "/team/{$equipo->getIdEquipo()}"
              ];
            ?>
              <li>
                <?php require __DIR__ . '/parts/tarjeta-proximo.php'; ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="pagination">

          </div>
        </section>

      </div>

  </main>

  <?php require "parts/footer.php"; ?>

  <script src="/js/sidebar.js"></script>
</body>

</html>