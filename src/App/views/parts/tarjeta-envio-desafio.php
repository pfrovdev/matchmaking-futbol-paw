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
        src="<?= !empty($equipo['url_foto_perfil'])
                ? htmlspecialchars($equipo['url_foto_perfil'])
                : '/icons/defaultTeamIcon.png' ?>"
        alt=""
      >
      <span class="level-badge"><?= htmlspecialchars($equipo['nivel_elo_descripcion']) ?></span>
    </div>
  </div>

  <div class="card-main">
    <div class="card-header">
      <h3 class="team-name"><?= htmlspecialchars($equipo['nombre']) ?></h3>
      <div class="team-record"></div>
    </div>

    <div class="card-body">
      <p>Deportividad:
        <?php for($i=0;$i<$equipo['deportividad'];$i++): ?> 
            <span class="icon">⚽</span>
        <?php endfor; ?>
      </p>
      <p class="team-motto"><?= htmlspecialchars($equipo['lema']) ?></p>
      <small class="elo">ELO: <?= htmlspecialchars($equipo['elo_actual']) ?></small>
      <a href="<?= !empty($equipo['profileUrl'])
                   ? htmlspecialchars($equipo['profileUrl'])
                   : '#' ?>"
         class="profile-link">
        Ver perfil del equipo
      </a>
    </div>

    <div class="card-actions">
      <button class="btn btn-accept">Desafiar</button>
    </div>

  </div>
</div>