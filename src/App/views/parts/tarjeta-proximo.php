<?php
// parts/tarjeta-proximo.php
// Este parcial recibe un array $match con los datos de cada partido.
// Ejemplo de uso antes de require:
// 
 $match = [
   'name'       => 'Nombre-equipo',
   'nivel'      => 'Principiante II',
   'deportividad' => 3,                    
   'lema'       => 'Lema del equipo corto',
   'record'     => '10-7-2',               
   'url_logo'   => '/images/defaultTeamIcon.png',  
   'profile-link' => '#',
 ];
?>
<div class="nm-card">
  <!-- Lado izquierdo: logo/nivel -->
  <div class="nm-card-side">
    <div class="nm-team-image">
      <img
        src="<?= !empty($match['url_logo'])
                ? htmlspecialchars($match['url_logo'])
                : '/icons/defaultTeamIcon.png' ?>"
        alt="Logo de <?= htmlspecialchars($match['name']) ?>">
      <span class="nm-level-badge"><?= htmlspecialchars($match['nivel']) ?></span>
    </div>
  </div>

  <!-- Zona principal de contenido -->
  <div class="nm-card-main">
    <div class="nm-card-header">
      <h3 class="nm-team-name"><?= htmlspecialchars($match['name']) ?></h3>
      <div class="nm-team-record"><?= htmlspecialchars($match['record']) ?></div>
    </div>
    <div class="nm-card-body">
      <div class="nm-sport-icons">
        Deportividad:
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <?php if ($i <= (int)$match['deportividad']): ?>
            <span class="nm-icon">⚽</span>
          <?php else: ?>
            <span class="nm-icon" style="opacity: 0.4; color: grey;">⚽</span>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
      <p class="nm-team-motto"><?= htmlspecialchars($match['lema']) ?></p>
      <a href="<?php echo $match['profile-link'] ?>" class="profile-link">ver perfil del equipo</a>
    </div>
    <div class="nm-card-actions">
      <!-- Botones de acción para cada partido -->
      <button class="nm-btn-secondary nm-small">Abrir wapp</button>
      <button class="nm-btn-primary nm-small">Coordinar resultado</button>
      <button class="nm-btn-danger nm-small">Cancelar</button>
    </div>
  </div>
</div>