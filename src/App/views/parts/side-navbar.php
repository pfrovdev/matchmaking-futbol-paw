<?php
$menuItems = [
  [
    'href'  => 'dashboard',
    'icon'  => '/icons/organization-team.png',
    'alt'   => 'Mi equipo',
    'label' => 'Mi equipo'
  ],
  [
    'href'  => 'search-team',
    'icon'  => '/icons/magnifying-glass.png',
    'alt'   => 'Buscar Desafios',
    'label' => 'Buscar desafio'
  ],
  [
    'href'  => 'ranking-teams',
    'icon'  => '/icons/ranking-icon.png',
    'alt'   => 'Ranking de equipos',
    'label' => 'Ranking'
  ],
];
?>
<nav class="side-navbar">
  <?php foreach ($menuItems as $item): ?>
    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
      <img src="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['alt'], ENT_QUOTES, 'UTF-8') ?>">
      <span class="label"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
    </a>
  <?php endforeach; ?>
</nav>