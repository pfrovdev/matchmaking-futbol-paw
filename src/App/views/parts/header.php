<header class="<?= $estaLogueado ? 'header header-login' : 'header' ?>">
    <?php if ($estaLogueado): ?>
        <input type="checkbox" id="hamburger-checkbox" class="hamburger-checkbox">
        <label for="hamburger-checkbox" class="hamburger-menu">
            <img src="../icons/hamburguer-menu.png" alt="Menú" class="icon">
        </label>
    <?php endif; ?>
    <h1>
        <a href="<?= htmlspecialchars($estaLogueado ? './dashboard' : './', ENT_QUOTES, 'UTF-8') ?>">
            <img src="../icons/enterprise-icon.svg" id="enterprise-icon" />
        </a>
    </h1>
    <?php if ($estaLogueado): ?>
        <?php if ($miEquipo->fields['nombre']): ?>
            <section class="header-my-account">
                <button type="button" class="mi-equipo-button" aria-label="Mi equipo">
                    <?php if ($miEquipo->fields['url_foto_perfil']): ?>
                        <?php
                        $foto = $miEquipo->fields['url_foto_perfil'] ?? '';
                        if (empty($foto) || !filter_var($foto, FILTER_VALIDATE_URL)) {
                            $foto = '../icons/defaultTeamIcon.png';
                        }
                        ?>
                        <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil" class="icon">
                    <?php else: ?>
                        <img src="../icons/defaultTeamIcon.png" class="icon">
                    <?php endif; ?>

                    <?= htmlspecialchars($miEquipo->fields['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </button>
                <ul>
                    <li><a href="./dashboard">Mi Perfil</a></li>
                    <li><a href="./logout">Cerrar sesión</a></li>
                </ul>
            </section>
        <?php endif; ?>
    <?php endif; ?>
    <script src="/js/header.js"></script>
</header>