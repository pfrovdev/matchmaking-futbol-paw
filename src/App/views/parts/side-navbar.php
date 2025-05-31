<?php
$menuItems = [
    [
        'href' => 'dashboard',
        'icon' => '/icons/organization-team.png',
        'alt'  => 'Mi equipo'
    ],
    [
        'href' => 'search-team',
        'icon' => '/icons/magnifying-glass.png',
        'alt'  => 'Buscar Equipos'
    ],
    [
        'href' => 'ranking-teams',
        'icon' => '/icons/ranking-icon.png',
        'alt'  => 'Buscar Equipos'
    ],
    
];
?>
<nav class="side-navbar">
  <?php foreach ($menuItems as $item): ?>
    <a href="<?= htmlspecialchars($item['href']) ?>">
      <img src="<?= htmlspecialchars($item['icon']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>">
    </a>
  <?php endforeach; ?>
</nav>