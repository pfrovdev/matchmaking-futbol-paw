<?php
// Antes de hacer el require, define el array $match con estos campos:
// $match = [
//   'soyGanador' => true/false                // para cambiar color de tarjeta
//   'eloChange' => +20,                       // número positivo o negativo
//   'matchUrl'  => '#',                       // enlace a detalle
//   'date'      => '21/02/2025',
//   'home'      => [
//     'abbr'      => 'CABJ',
//     'name'      => 'Boca Juniors',
//     'logo'      => 'boca.png',              // ruta relativa a img/
//     'cards'     => ['yellow'=>1,'red'=>0], // cantidad de tarjetas
//   ],
//   'away'      => [
//     'abbr'      => 'CASLA',
//     'name'      => 'San Lorenzo',
//     'logo'      => 'sanlorenzo.png',
//     'cards'     => ['yellow'=>1,'red'=>0],
//   ],
//   'score'     => '4-0',
// ];
?>
<div class=<?= $soyGanador? "history-card-win" : "history-card-lose" ?> >
  <div class="hc-header">
    <span class="elo-change <?= $match['eloChange'] >= 0 ? 'up' : 'down' ?>">
      <?= ($match['eloChange'] >= 0 ? '+' : '') . $match['eloChange'] ?> ELO
    </span>
    <a href="<?= htmlspecialchars($match['matchUrl']) ?>" class="hc-btn-link">ver partido</a>
    <span class="match-date"><?= htmlspecialchars($match['date']) ?></span>
  </div>

  <div class="hc-body">
    <!-- Equipo local -->
    <div class="team-block home">
        <div class="team-img" style="background-image:url('../../../public/icons/<?= $match['home']['logo'] ?>')">
            <span class="team-abbr"><?= htmlspecialchars($match['home']['abbr']) ?></span>
        </div>
        <div class="team-info">
            
            <div class="tarjetas">
                <?php if ($match['home']['tarjetas']['yellow']): ?>
                <span class="tarjeta yellow"><?= $match['home']['tarjetas']['yellow'] ?></span>
                <?php endif; ?>
                <?php if ($match['home']['tarjetas']['red']): ?>
                <span class="tarjeta red"><?= $match['home']['tarjetas']['red'] ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Marcador -->
    <div class="hc-score"><?= htmlspecialchars($match['score']) ?></div>

    <!-- Equipo visitante -->
    <div class="team-block away">
        <div class="team-info">
                
                <div class="tarjetas">
                    <?php if ($match['away']['tarjetas']['yellow']): ?>
                    <span class="tarjeta yellow"><?= $match['away']['tarjetas']['yellow'] ?></span>
                    <?php endif; ?>
                    <?php if ($match['away']['tarjetas']['red']): ?>
                    <span class="tarjeta red"><?= $match['away']['tarjetas']['red'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <div class="team-img" style="background-image:url('../../../public/icons/<?= $match['away']['logo'] ?>')">
            <span class="team-abbr"><?= htmlspecialchars($match['away']['abbr']) ?></span>
        </div>
        
    </div>
  </div>
</div>
