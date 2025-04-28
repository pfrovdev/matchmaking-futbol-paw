<nav class="mobile-menu">
    <label for="hamburger-checkbox" class="close-menu">
        <img src="../icons/go-back-arrow.png" alt="Cerrar" class="icon">
    </label>
    <ul>
        <?php foreach ($this->menu_nav as $item) : ?>
            <li><a href="<?= $item['href'] ?>"><?= $item['route_name'] ?></a></li>
        <?php endforeach ; ?>       
    </ul>
    <img src="../icons/PAWPrintsWhite.svg" class="enterprise-icon" alt="Logo de la Empresa"/>
</nav>
<nav class="desktop-nav">
    <ul>
        <?php foreach ($this->menu_nav as $item) : ?>
            <li><a href="<?= $item['href'] ?>"><?= $item['route_name'] ?></a></li>
        <?php endforeach ; ?>
    </ul>
</nav>