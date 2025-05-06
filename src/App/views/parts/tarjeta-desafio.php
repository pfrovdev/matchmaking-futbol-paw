<?php
// Recibe un array $challenge con todos los datos necesarios
// Ejemplo de uso antes de require:
// $challenge = [
//   'name'       => 'Nombre-equipo',
//   'level'      => 'Principiante II',
//   'icons'      => 5,                // número de pelotas/fútbol
//   'motto'      => 'Lema del equipo corto',
//   'elo'        => ['wins'=>10,'losses'=>7,'draws'=>0],
//   'record'     => '10-7-2',         // texto en la esquina superior
//   'profileUrl' => '#',
// ];
?>
<div class="challenge-card">
  <div class="card-side">
    <div class="team-image">
      <span class="level-badge"><?php echo htmlspecialchars($challenge['level']) ?></span>
    </div>
  </div>

  <div class="card-main">
    <div class="card-header">
      <h3 class="team-name"><?php echo htmlspecialchars($challenge['name']) ?></h3>
      <div class="team-record"><?php echo htmlspecialchars($challenge['record']) ?></div>
    </div>

    <div class="card-body">
      <div class="sport-icons">
            <!-- Faltaria hacer algo tipo, hasta la cantidad que me mandan
             pongo pelotitas, y relleno hasta 5 espacios vacios -->
        <?php for($i=0;$i<$challenge['icons'];$i++): ?> 
          <span class="icon">⚽</span>
        <?php endfor; ?>
      </div>
      <p class="team-motto"><?php echo htmlspecialchars($challenge['motto']) ?></p>
      <small class="elo">
        Elo W/L/D: +
        <?php echo $challenge['elo']['wins'] ?>,
        -<?php echo $challenge['elo']['losses'] ?>,
        <?php echo $challenge['elo']['draws'] ?>
      </small>
      <a href="<?php echo $challenge['profileUrl'] ?>" class="profile-link">ver perfil del equipo</a>
    </div>

    <div class="card-actions">
      <button class="btn btn-accept">Aceptar desafío</button>
      <button class="btn btn-reject">Rechazar desafío</button>
    </div>
  </div>
</div>