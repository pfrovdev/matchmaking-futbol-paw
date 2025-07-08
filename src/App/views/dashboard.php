<?php
// src/App/views/dashboard.php
// Variables: $miEquipo, $comentariosPag (array de Comentario), $desafiosRecib (array de Desafio), $nivelDesc, $deportividad, $ultimoPartidoJugado $page, $per, $order, $dir
$isOwner = ($equipoVistoId === $miEquipo->getIdEquipo());
if (!$isOwner) {
  require $this->viewsDir . 'profile.php';
  return;
}
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
  <script src="/js/sidebar.js"></script>
</head>

<body
  data-profile-id="<?= $equipoVistoId ?>"
  data-is-owner="<?= ($equipoVistoId === $miEquipo->getIdEquipo()) ? 'true' : 'false' ?>">

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
              <h2>
                <?= htmlspecialchars($equipoBanner->getNombreEquipo()) . " (" . htmlspecialchars($miEquipo->fields['acronimo']) . ")" ?>
              </h2>
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
                  <div class="bar-fill" style="width:<?= min(100, ($equipoBanner->getEloActual() / 1300) * 100) ?>%">
                  </div>
                </div>
                <div class="elo-values">
                  <span>Elo: <?= htmlspecialchars($equipoBanner->getEloActual()) ?></span> / <span>1300</span>
                </div>
              </div>
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
          <div class="comment-filter">
            <label for="filtroProximosPartidos">Ordenar por:</label>
            <select id="filtroProximosPartidos" name="filtroProximosPartidos">
              <option value="fecha_creacion-DESC" selected>Más recientes</option>
              <option value="fecha_creacion-ASC">Más antiguos</option>
            </select>
          </div>
          <ul class="match-list" id="match-list"></ul>
          <div class="pagination" id="partidos-pagination"></div>
        </section>

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