<?php
// src/App/views/dashboard.php
// Variables: $equipo, $comentariosPag (array de Comentario), $desafiosRecib (array de Desafio), $nivelDesc, $deportividad, $ultimoPartidoJugado $page, $per, $order, $dir
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Pagina principal del equipo de futbol del usuario">
  <title>Dashboard - <?= htmlspecialchars($equipo->fields['nombre']) ?></title>
  <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
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
              <?php if ($equipo->fields['url_foto_perfil']): ?>
                <img src="<?= htmlspecialchars($equipo->fields['url_foto_perfil']) ?>" alt="Foto de perfil">
              <?php else: ?>
                <div class="placeholder-foto">Arrastra-soltar la foto de tu equipo aquí<br>
                  <button class="btn-link">Cargar un documento</button>
                </div>
              <?php endif; ?>
            </div>
            <div class="perfil-info">
              <h2><?= htmlspecialchars($equipo->fields['nombre']) . " (" . htmlspecialchars($equipo->fields['acronimo']) . ")" ?></h2>
              <p class="lema"><?= htmlspecialchars($equipo->fields['lema']) ?></p>
              <div class="sport-icons">
                Deportividad:
                <!-- Faltaria hacer algo tipo, hasta la cantidad que me mandan
                pongo pelotitas, y relleno hasta 5 espacios vacios -->
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?php if ($i <= $equipo->promediarDeportividad()): ?>
                    <span class="icon">⚽</span>
                  <?php else: ?>
                    <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
                  <?php endif; ?>
                <?php endfor; ?>
                <?= "(".$cantidadDeVotos.")" ?>
              </div>
              <p>Género: <?= htmlspecialchars($equipo->getTipoEquipo()) ?></p>
              <div class="elo-bar">
                <span class="label"><?= htmlspecialchars($nivelDesc) ?></span>
                <div class="bar-bg">
                  <div class="bar-fill" style="width:<?= min(100, ($equipo->fields['elo_actual'] / 1300) * 100) ?>%"></div>
                </div>
                <div class="elo-values">
                  <span>Elo: <?= htmlspecialchars($equipo->fields['elo_actual']) ?></span> / <span>1300</span>
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
                'date'       => (new \DateTime($ultimoPartidoJugado->fields['fecha_jugado']))->format('d-m-Y'),
                'home'       => [
                  'abbr'      => $equipoLocal->fields['acronimo'],
                  'name'      => $equipoLocal->fields['nombre'],
                  'logo'      => $equipoLocal->fields['url_foto_perfil'],
                  'tarjetas'  => [
                    'yellow' => $soyGanador
                      ? $ultimoPartidoJugado->fields['total_amarillas_ganador']
                      : $ultimoPartidoJugado->fields['total_amarillas_perdedor'],
                    'red'    => $soyGanador
                      ? $ultimoPartidoJugado->fields['total_rojas_ganador']
                      : $ultimoPartidoJugado->fields['total_rojas_perdedor'],
                  ],
                ],
                'away'       => [
                  'abbr'      => $equipoRival->fields['acronimo'],
                  'name'      => $equipoRival->fields['nombre'],
                  'logo'      => $equipoRival->fields['url_foto_perfil'],
                  'tarjetas'  => [
                    'yellow' => !$soyGanador
                      ? $ultimoPartidoJugado->fields['total_amarillas_ganador']
                      : $ultimoPartidoJugado->fields['total_amarillas_perdedor'],
                    'red'    => !$soyGanador
                      ? $ultimoPartidoJugado->fields['total_rojas_ganador']
                      : $ultimoPartidoJugado->fields['total_rojas_perdedor'],
                  ],
                ],
                'score'      => $soyGanador
                  ? $ultimoPartidoJugado->fields['goles_equipo_ganador'] . '-' . $ultimoPartidoJugado->fields['goles_equipo_perdedor']
                  : $ultimoPartidoJugado->fields['goles_equipo_perdedor'] . '-' . $ultimoPartidoJugado->fields['goles_equipo_ganador'],
              ];

              // Incluyo la tarjeta de historial
              require 'parts/tarjeta-historial.php';

            else: ?>
              <p>No jugó ningún partido aún.</p>
            <?php endif; ?>
          </div>

          <!-- Card 3: Desafíos recibidos -->
          <div class="card challenges-card">
            <h3>Últimos desafíos recibidos</h3>
            <ul class="challenge-list">
              <?php foreach ($desafiosRecib as $d): ?>
                <?php $retador = $d->getEquipoDesafiante(); ?>
                <li>
                  <?php
                  $challenge = [
                    'name'       => $retador->fields['nombre'],
                    'level'      => $retador->getNivelElo(),
                    'icons'      => 0,
                    'lema'      => $retador->fields['lema'] ?? '',
                    'elo'        => ['wins' => 10, 'losses' => 7, 'draws' => 0],
                    'record'     => '',
                    'id_nivel_elo' => $retador->getNivelElo(),
                    'deportividad' => $retador->promediarDeportividad(),
                    'profile-link' => "/team/{$retador->fields['id_equipo']}",
                  ];
                  require "parts/tarjeta-desafio.php";
                  ?>
                </li>
              <?php endforeach; ?>
            </ul>
            <div class="pagination">
              <?php
              $totalC = count($comentariosPag);
              $pagesC = ceil($totalC / $per);
              for ($p = 1; $p <= $pagesC; $p++): ?>
                <a href="?page=<?= $p ?>&amp;order=<?= urlencode($order) ?>&amp;dir=<?= $dir ?>"
                  class="<?= ($p === $page) ? 'active' : '' ?>"><?= $p ?></a>
              <?php endfor; ?>
            </div>
          </div>
        </section>

        <!-- Columna Derecha -->
        <aside class="col-right">
          <!-- Card 4: Estadísticas -->
          <div class="card stats-card">
            <h3>Estadísticas</h3>
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
            <h3>Comentarios</h3>
            <ul class="comment-list">
              <?php foreach ($comentariosPag as $c): ?>
                <?php $autor = $c->getEquipoComentador(); ?>
                <li>
                  <strong><?= htmlspecialchars($autor->fields['nombre']) ?></strong>
                  <p>Calificación: <?= str_repeat('●', $c->fields['deportividad']) . str_repeat('○', 5 - $c->fields['deportividad']) ?></p>
                  <p>comentario: <?= htmlspecialchars($c->fields['comentario']) ?></p>
                </li>
              <?php endforeach; ?>
            </ul>
            <div class="pagination">
              <?php
              $totalC = count($comentariosPag);
              $pagesC = ceil($totalC / $per);
              for ($p = 1; $p <= $pagesC; $p++): ?>
                <a href="?page=<?= $p ?>&order=<?= urlencode($order) ?>&dir=<?= $dir ?>"
                  class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
              <?php endfor; ?>
            </div>
          </div>
        </aside>
      </div>

      <!-- SECCIÓN INFERIOR: Proximos partidos full-width -->
      <section class="next-matches">
        <h3>Próximos partidos</h3>
        <ul class="match-list">
          <?php for ($i = 1; $i <= 2; $i++): ?>
            <li class="match-item">
              <div class="match-info">
                <strong>Nombre-equipo</strong>
                <p>Principiante II</p>
              </div>
              <div class="match-actions">
                <button class="btn-secondary small">Abrir wapp</button>
                <button class="btn-primary small">Coordinar resultado</button>
                <button class="btn-danger small">Cancelar</button>
              </div>
            </li>
          <?php endfor; ?>
        </ul>
      </section>
      
    </div>
   
  </main>

  <?php require "parts/footer.php"; ?>
</body>

</html>