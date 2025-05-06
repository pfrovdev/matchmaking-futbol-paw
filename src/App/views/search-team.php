<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Equipo</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="./css/search-team.css">
</head>
<body>
    <?php
        require "parts/header.php";
    ?>
    <main>
        <header>
            <h1>Buscar desafío</h1>
            <p>Busca rivales, por rango o zona</p>
        </header>

        <section class="grid-layout">
        
            <section class="left-size" aria-labelledby="buscar-nombre">
                <h2 id="buscar-nombre">Buscar por nombre</h2>
                <form method="get" action="/search-team">
                    <input type="text" id="nombre" name="nombre" placeholder="Ejemplo FC" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" />
                    <button type="submit">Buscar</button>
                </form>

                <ul class="lista-equipos">
                    <?php foreach ($equipos as $equipo): ?>
                        <li>
                            <article>
                                <img src="<?= !empty($equipo['url_foto_perfil']) ? htmlspecialchars($equipo['url_foto_perfil']) : '/icons/defaultTeamIcon.png' ?>" alt="imagen del equipo">
                                <span class="rango"><?= htmlspecialchars($equipo['nivel_elo_descripcion']) ?></span>
                                <h3 class="team-name"><?= htmlspecialchars($equipo['nombre']) ?></h3>
                                <p>Deportividad: <?= isset($equipo['deportividad']) ? number_format($equipo['deportividad'], 2) : 'Sin datos' ?></p>
                                <p>Lema: <?= htmlspecialchars($equipo['lema']) ?></p>
                                <p>ELO: <?= htmlspecialchars($equipo['elo_actual']) ?></p>
                                <a href="#">Ver perfil del equipo</a>
                                <button type="button">Desafiar</button>
                            </article>
                        </li><br>
                        <?php $first = false; ?>
                    <?php endforeach; ?>
                </ul>
                <section>
                    <?php if ($paginaActual > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual - 1])) ?>">Anterior</a>
                    <?php endif; ?>

                    Página <?= $paginaActual ?> de <?= $totalPaginas ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual + 1])) ?>">Siguiente</a>
                    <?php endif; ?>
                </section>
            </section>

            <aside>
                <section aria-labelledby="filtro-rango">
                <h2 id="filtro-rango">Filtrar por rango</h2>
                <ul class="filtros-rango">
                    <li><button type="button">Principiante</button></li>
                    <li><button type="button">Amateur</button></li>
                    <li><button type="button">SemiPro</button></li>
                    <li><button type="button" class="activo">Profesional ✕</button></li>
                </ul>
                </section>

                <section aria-labelledby="ordenar">
                    <h2 id="ordenar">Ordenar por</h2>
                    <form>
                        <label><input type="radio" name="orden" checked> Mayor a menor ELO</label><br>
                        <label><input type="radio" name="orden"> Menor a mayor ELO</label><br>
                        <label><input type="radio" name="orden"> Alfabéticamente</label>
                </form>
                </section>

                <section aria-labelledby="zona-busqueda">
                    <h2 id="zona-busqueda">Zona de búsqueda</h2>
                    <form>
                        <label for="lugar">Buscar lugar</label>
                        <input type="text" id="lugar" placeholder="La canchita, Luján" />

                        <label for="kms">Rango búsqueda (KMS)</label>
                        <input type="number" id="kms" value="1" />
                    </form>
                    <figure>
                        <div id="map"></div>
                    </figure>
                </section>
            </aside>
        </section>
    </main>
    <?php
        require "parts/footer.php";
    ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Coordenadas por defecto (Luján, Buenos Aires)
        const defaultLat = -34.6545508;
        const defaultLng = -59.4168298;

        const map = L.map('map').setView([defaultLat, defaultLng], 13);

        // Capa base OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marcador central
        L.marker([defaultLat, defaultLng]).addTo(map)
            .openPopup();
    </script>
</body>
</html>