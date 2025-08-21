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
$mostrar_estadisticas ?? true;
?>
<section>
  <ol class="ranking-list">
    <li class="ranking-item">
      <article class="team-card">
        <figure class="team-image">
          <img
            src="<?= htmlspecialchars($equipo->getUrlFotoPerfil() ?? '/icons/defaultTeamIcon.png', ENT_QUOTES, 'UTF-8') ?>"
            alt="Escudo del equipo <?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>" />
          <figcaption class="team-rank" style="background: <?= htmlspecialchars($gradient, ENT_QUOTES, 'UTF-8') ?>;">
            <?= htmlspecialchars($equipo->getDescripcionElo(), ENT_QUOTES, 'UTF-8') ?>
          </figcaption>
        </figure>
        <div class="team-info">
          <h2 class="team-name"><?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?></h2>
          <p class="team-sportsmanship">Deportividad:
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?php if ($i <= $equipo->getDeportividad()): ?>
                <span class="icon">⚽</span>
              <?php else: ?>
                <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
              <?php endif; ?>
            <?php endfor; ?>
          </p>
          <p class="team-lema"><?= htmlspecialchars($equipo->getLema(), ENT_QUOTES, 'UTF-8') ?></p>
          <p class="team-record">
            <span class="wins">W</span>/
            <span class="losses">L</span>/
            <span class="draws">D:</span>

            <span class="wins"><?= htmlspecialchars($equipo->ganados, ENT_QUOTES, 'UTF-8') ?></span>/
            <span class="losses"><?= htmlspecialchars($equipo->perdidos, ENT_QUOTES, 'UTF-8') ?></span>/
            <span class="draws"><?= htmlspecialchars($equipo->empatados, ENT_QUOTES, 'UTF-8') ?></span>
          </p>
        </div>
        <div class="team-actions">
          <a href="/details-team?id=<?= urlencode((string) $equipo->getIdEquipo()) ?>" class="button btn-profile">
            Ver perfil
          </a>
        </div>
        <?php if ($mostrar_estadisticas): ?>
          <?php if (!empty($estadisticas)): ?>
            <section class="card stats-card">
              <h3 class="title-subsection"><strong>Estadísticas</strong></h3>
              <ul>
                <li><strong>Partidos jugados:</strong> <?= htmlspecialchars($jugados, ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Victorias:</strong> <?= htmlspecialchars($ganados, ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Empates:</strong> <?= htmlspecialchars($empatados, ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Derrotas:</strong> <?= htmlspecialchars($perdidos, ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Goles a favor:</strong> <?= htmlspecialchars($goles, ENT_QUOTES, 'UTF-8') ?>
                  (<?= $promedioGoles ?> por partido)
                </li>
                <li><strong>Goles en contra:</strong> <?= htmlspecialchars($golesEnContra, ENT_QUOTES, 'UTF-8') ?>
                  (<?= $promedioGolesEnContra ?> por partido)</li>
                <li><strong>Diferencia de gol:</strong> <?= $diferenciaGol >= 0 ? '+' : '' ?><?= $diferenciaGol ?>
                </li>
                <li><strong>ELO actual:</strong>
                  <?= htmlspecialchars($equipo->getEloActual(), ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>ELO más alto:</strong> <?= htmlspecialchars($eloMasAlto, ENT_QUOTES, 'UTF-8') ?></li>
                <li><strong>Tarjetas amarillas totales:</strong>
                  <?= htmlspecialchars($amarillas, ENT_QUOTES, 'UTF-8') ?>
                </li>
                <li><strong>Tarjetas amarillas por partido:</strong> <?= $promedioAmarillas ?></li>
                <li><strong>Tarjetas rojas totales:</strong> <?= htmlspecialchars($rojas, ENT_QUOTES, 'UTF-8') ?>
                </li>
                <li><strong>Asistencias:</strong> <?= htmlspecialchars($asistencias, ENT_QUOTES, 'UTF-8') ?></li>
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
        <?php endif; ?>
      </article>
    </li>
  </ol>
</section>