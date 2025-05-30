<?php
// parts/tarjeta-envio-desafio.php
// Espera un array $equipo con las claves:
// 'url_foto_perfil', 'nivel_elo_descripcion', 'nombre',
// 'deportividad', 'lema', 'elo_actual', 'profileUrl' 
?>
<div class="challenge-card challenge-card--send">
  <div class="card-side">
    <div class="team-image">
      <img
        src="<?= htmlspecialchars($equipo->getUrlFotoPerfil() ?? '/icons/defaultTeamIcon.png') ?>"
        alt=""
      >
      <span class="level-badge">
       <!-- <?= htmlspecialchars($nivelEloDescripcion?? 'refactorizar') ?> -->
      </span>
    </div>
  </div>

  <div class="card-main">
    <div class="card-header">
      <h3 class="team-name"><?= htmlspecialchars($equipo->getNombre()) ?></h3>
      <div class="team-record"></div>
    </div>

    <div class="card-body">
      <p>Deportividad:
        <?php for ($i = 0; $i < $equipo->promediarDeportividad(); $i++): ?> 
          <span class="icon">⚽</span>
        <?php endfor; ?>
      </p>
      <p class="team-motto"><?= htmlspecialchars($equipo->getLema()) ?></p>
      <small class="elo">ELO: <?= htmlspecialchars((string)$equipo->getEloActual()) ?></small>
      <a href="/team/<?= urlencode((string)$equipo->getIdEquipo()) ?>"
         class="profile-link">
        Ver perfil del equipo
      </a>
    </div>

    <div class="card-actions">
      <a href="?<?= http_build_query(array_merge($_GET, [
            'id_equipo_desafiar' => $equipo->getIdEquipo()
          ])) ?>"
         class="btn btn-accept">
        Desafiar
      </a>
    </div>
  </div>
</div>