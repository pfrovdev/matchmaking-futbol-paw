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
<div class="challenge-card challenge-card--send">
  <div class="card-side">
    <div class="team-image">
      <figure class="team-image">
        <img
          src="<?= htmlspecialchars($equipo->getUrlFotoPerfil() ?? '/icons/defaultTeamIcon.png', ENT_QUOTES, 'UTF-8') ?>"
          alt="Escudo del equipo <?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>" />
        <figcaption class="team-rank" style="background: <?= htmlspecialchars($gradient, ENT_QUOTES, 'UTF-8') ?>;">
          <?= htmlspecialchars($equipo->getDescripcionElo(), ENT_QUOTES, 'UTF-8') ?>
        </figcaption>
      </figure>
    </div>
  </div>

  <div class="card-main">
    <div class="card-header">
      <h3 class="team-name"><?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?></h3>
      <div class="team-record"></div>
    </div>

    <div class="card-body">
      <p>Deportividad:
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <?php if ($i <= $equipo->getDeportividad()): ?>
            <span class="icon">⚽</span>
          <?php else: ?>
            <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
          <?php endif; ?>
        <?php endfor; ?>
      </p>
      <p class="team-motto"><?= htmlspecialchars($equipo->getLema(), ENT_QUOTES, 'UTF-8') ?></p>
      <small class="elo">ELO: <?= htmlspecialchars((string) $equipo->getEloActual(), ENT_QUOTES, 'UTF-8') ?></small>
      <a href="/dashboard?id=<?= urlencode((string) $equipo->getIdEquipo()) ?>" class="profile-link">
        Ver perfil del equipo
      </a>
    </div>

    <div class="card-actions">
      <a href="?<?= http_build_query(array_merge($_GET, [
        'id_equipo_desafiar' => $equipo->getIdEquipo()
      ])) ?>" class="btn btn-desafiar">
        <span class="btn-text">Desafiar</span>
        <span class="spinner" style="display:none;"></span>
      </a>
    </div>
  </div>
</div>