<header id="header">
    <input type="checkbox" id="hamburger-checkbox" class="hamburger-checkbox">
    <label for="hamburger-checkbox" class="hamburger-menu">
        <img src="../icons/hamburguer-menu.png" alt="Menú" class="icon">
    </label>
    
    <h1>
        <a href="./dashboard">
            <img src="../icons/enterprise-icon.svg" id="enterprise-icon"/>
        </a>
    </h1>
   
    <?php if ($miEquipo->fields['nombre']): ?>
        <section class="header-my-account">
            <button type="button" aria-label="Mi equipo">
                <img src="../icons/defaultTeamIcon.png" class="icon">
                <?= htmlspecialchars($miEquipo->fields['nombre'], ENT_QUOTES, 'UTF-8') ?>
            </button>
            <ul>
                <li><a href="./dashboard">Mi Perfil</a></li>
                <li><a href="./">Cerrar sesión</a></li>
            </ul>
        </section>
    <?php endif; ?>
    <script src="/js/header.js"></script>
</header>