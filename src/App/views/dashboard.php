<?php
// src/App/views/dashboard.php
// Variables: $miEquipo, $comentariosPag (array de Comentario), $desafiosRecib (array de Desafio), $nivelDesc, $deportividad, $ultimoPartidoJugado $page, $per, $order, $dir
$isOwner = ($equipoVistoId === $miEquipo->getIdEquipo());
if (!$isOwner) {
  require $this->viewsDir . 'profile.php';
  return;
}
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Pagina principal del equipo de futbol del usuario">
  <title>Dashboard - <?= htmlspecialchars($miEquipo->fields['nombre'], ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="./css/spinner.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
  <script type="module" src="js/pages/Dashboard.js" defer></script>
  <script src="/js/sidebar.js"></script>
  <script src="./js/components/spinner.js" defer></script>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SportsTeam",
      "name": "<?= htmlspecialchars($equipoBanner->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>",
      "sport": "Soccer",
      "memberOf": {
        "@type": "SportsOrganization",
        "name": "Ligas de Fútbol Amateur"
      },
      "identifier": {
        "@type": "PropertyValue",
        "name": "Elo Ranking",
        "value": "<?= htmlspecialchars($equipoBanner->getEloActual(), ENT_QUOTES, 'UTF-8') ?>"
      },
      "alternateName": "<?= htmlspecialchars($miEquipo->fields['acronimo'], ENT_QUOTES, 'UTF-8') ?>",
      "description": "<?= htmlspecialchars($equipoBanner->getLema(), ENT_QUOTES, 'UTF-8') ?>",
      <?php if ($equipoBanner->getUrlFotoPerfil()): ?> "image": "<?= htmlspecialchars($equipoBanner->getUrlFotoPerfil(), ENT_QUOTES, 'UTF-8') ?>",
      <?php endif; ?> "gender": "<?= htmlspecialchars($equipoBanner->getTipoEquipo(), ENT_QUOTES, 'UTF-8') ?>",
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

<body data-profile-id="<?= $equipoVistoId ?>"
  data-is-owner="<?= ($equipoVistoId === $miEquipo->getIdEquipo()) ? 'true' : 'false' ?>">

  <?php
  $estaLogueado = !!$miEquipo->getIdEquipo();
  require "parts/header.php";
  ?>
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
                <?php
                $foto = $equipoBanner->getUrlFotoPerfil();
                if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                  $foto = 'icons/defaultTeamIcon.png';
                }
                ?>
                <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil">
              <?php else: ?>
                <div class="placeholder-foto">Coloca la foto de tu equipo aquí<br>
                  <button class="btn-link">Cargando un enlace</button>
                </div>
              <?php endif; ?>
            </div>
            <div class="perfil-info">
              <h2 class="team-header">
                <?= htmlspecialchars($equipoBanner->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>
                <span
                  class="acronym">(<?= htmlspecialchars($miEquipo->fields['acronimo'], ENT_QUOTES, 'UTF-8') ?>)</span>
                <button type="button" class="btn-link open-edit-modal" title="Editar perfil">
                  ✎
                </button>
              </h2>
              <p class="lema"><?= htmlspecialchars($equipoBanner->getLema(), ENT_QUOTES, 'UTF-8') ?></p>
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
          <section class="card stats-card">
            <?php include 'parts/tarjeta-estadistica.php'; ?>
          </section>

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
      <div id="edit-team-modal" class="modal-overlay hidden">
        <div class="modal-content">
          <button id="close-modal" class="modal-close">&times;</button>
          <h2>Editar perfil de equipo</h2>
          <form action="/update-team" method="POST" class="edit-team-form">
            <label>
              Acrónimo (máx. 3 chars)
              <input type="text" name="team-acronym" id="team-acronym" maxlength="3"
                value="<?= htmlspecialchars($miEquipo->getAcronimo(), ENT_QUOTES, 'UTF-8') ?>">
            </label>
            <small id="acronym-error" class="error-message" style="display:none; color:#d32f2f; font-size:0.8rem;">
              El acrónimo no puede tener más de 3 caracteres.
            </small>
            <label>
              Lema
              <input type="text" name="team-motto" value="<?= htmlspecialchars($miEquipo->getLema()) ?>">
            </label>
            <label>
              URL foto perfil
              <input type="url" name="team-url" id="team-url"
                value="<?= htmlspecialchars($miEquipo->getUrlFotoPerfil()) ?>" maxlength="255" pattern="https?://.+"
                title="Debe empezar con http:// o https:// y tener como máximo 255 caracteres.">
            </label>
            <small id="url-error" class="error-message" style="display:none; color:#d32f2f; font-size:0.8rem;">
              La URL debe empezar con http:// o https:// y no superar 255 caracteres.
            </small>
            <button type="submit" class="btn-primary">Guardar</button>
          </form>
        </div>
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