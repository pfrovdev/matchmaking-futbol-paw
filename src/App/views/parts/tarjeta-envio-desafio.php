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
<!-- <div class="challenge-card challenge-card--send"> -->
<article class="challenge-card challenge-card--send" itemscope itemtype="https://schema.org/SportsTeam">

  <div class="card-side">
    <figure class="team-image">
      <img
        src="<?= htmlspecialchars($equipo->getUrlFotoPerfil() ?? '/icons/defaultTeamIcon.png', ENT_QUOTES, 'UTF-8') ?>"
        alt="Escudo del equipo <?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>"
        itemprop="logo" />
      <figcaption class="team-rank" style="background: <?= htmlspecialchars($gradient, ENT_QUOTES, 'UTF-8') ?>;">
        <?= htmlspecialchars($equipo->getDescripcionElo(), ENT_QUOTES, 'UTF-8') ?>
      </figcaption>
    </figure>
  </div>

  <div class="card-main">
    <header class="card-header">
      <h3 class="team-name" itemprop="name">
        <?= htmlspecialchars($equipo->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?>
      </h3>
    </header>

    <section class="card-body" itemprop="description">
      <p>
        <strong>Deportividad:</strong>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <?php if ($i <= $equipo->getDeportividad()): ?>
            <span class="icon">⚽</span>
          <?php else: ?>
            <span class="icon" style="opacity: 0.4; color: grey;">⚽</span>
          <?php endif; ?>
        <?php endfor; ?>
      </p>

      <?php if ($equipo->getLema()): ?>
        <p class="team-motto"><em>"<?= htmlspecialchars($equipo->getLema(), ENT_QUOTES, 'UTF-8') ?>"</em></p>
      <?php endif; ?>

      <p><strong>Género:</strong> <?= htmlspecialchars($equipo->getTipoEquipo(), ENT_QUOTES, 'UTF-8') ?></p>

      <p>
        <small class="elo"><strong>ELO:</strong>
          <?= htmlspecialchars((string) $equipo->getEloActual(), ENT_QUOTES, 'UTF-8') ?></small>
      </p>

      <p>
        <a href="/dashboard?id=<?= urlencode((string) $equipo->getIdEquipo()) ?>" class="profile-link" itemprop="url">
          Ver perfil del equipo
        </a>
      </p>
    </section>

    <footer class="card-actions">
      <form method="post" action="/desafios" class="challenge-form">
        <input type="hidden" name="id_equipo_desafiar"
          value="<?= htmlspecialchars($equipo->getIdEquipo(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="return_to"
          value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/search-team', ENT_QUOTES, 'UTF-8') ?>">
        <button class="button btn-desafiar" type="submit">
          <span class="btn-text">Desafiar</span>
          <span class="spinner" style="display:none;"></span>
        </button>
      </form>
    </footer>
  </div>
</article>