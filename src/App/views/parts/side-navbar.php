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
    <a href="<?= htmlspecialchars($item['href']) ?>">
      <img src="<?= htmlspecialchars($item['icon']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>">
      <span class="label"><?= htmlspecialchars($item['label']) ?></span>
    </a>
  <?php endforeach; ?>
</nav>