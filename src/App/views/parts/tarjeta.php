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
<section>
    <ol class="ranking-list">
      <li class="ranking-item">
        <article class="team-card">
          <figure class="team-image">
            <img src="<?= htmlspecialchars($equipo->getUrlFotoPerfil() ?? '/icons/defaultTeamIcon.png') ?>" alt="Escudo del equipo Nombre-equipo" />
            <figcaption class="team-rank" style="background: <?= htmlspecialchars($gradient) ?>;">
                <?= htmlspecialchars($equipo->getDescripcionElo()) ?>
            </figcaption>
          </figure>
          <div class="team-info">
            <h2 class="team-name"><?= htmlspecialchars($equipo->getNombreEquipo()) ?></h2>
            <p class="team-sportsmanship">Deportividad: 
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $equipo->getDeportividad()): ?>
                        <span class="icon">⚽</span>
                    <?php else: ?>
                        <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
                    <?php endif; ?>
                <?php endfor; ?>
            </p>
            <p class="team-lema"><?= htmlspecialchars($equipo->getLema()) ?></p>
            <p class="team-record">
              W/L/D: <span class="wins"><?= htmlspecialchars($equipo->ganados) ?></span>, 
              <span class="losses"><?= htmlspecialchars($equipo->perdidos) ?></span>, 
              <span class="draws"><?= htmlspecialchars($equipo->empatados) ?></span>
            </p>
          </div>
          <div class="team-elo">
            <strong>ELO:</strong><span class="elo-score"><?= htmlspecialchars((string)$equipo->getEloActual()) ?></span>
          </div>
          <div class="team-actions">
            <a href="#" class="btn-profile">Ver perfil del equipo</a>
          </div>
        </article>
      </li>
    </ol>
</section>