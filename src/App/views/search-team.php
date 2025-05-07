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
    <?php
        // Capturamos el orden o ponemos “desc” por defecto
        $orden = $_GET['orden'] ?? 'desc';
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
                <?php if ($equipos): ?>
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
                                    <a href="?<?= http_build_query(array_merge($_GET, ['id_equipo_desafiar' => $equipo['id_equipo']])) ?>">Desafiar</a>
                                </article>
                            </li><br>
                            <?php $first = false; ?>
                        <?php endforeach; ?>
                    </ul>
                    <section>
                        <?php if ($paginaActual > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual - 1])) ?>">Anterior</a>
                        <?php endif; ?>

                <ul class="lista-equipos">
                    <?php
                    // $equipos es tu array de equipos, cada uno con ['id_nivel_elo'] y ['nombre']
                    switch ($orden) {
                        case 'asc':
                        usort($equipos, function($a, $b) {
                            return $a['id_nivel_elo'] <=> $b['id_nivel_elo'];
                        });
                        break;
                        case 'alpha':
                        usort($equipos, function($a, $b) {
                            return strcasecmp($a['nombre'], $b['nombre']);
                        });
                        break;
                        case 'desc':
                        default:
                        usort($equipos, function($a, $b) {
                            return $b['id_nivel_elo'] <=> $a['id_nivel_elo'];
                        });
                        break;
                    }
                    ?>
                    <?php foreach ($equipos as $equipo): ?>
                        <li>
                            <?php
                                require __DIR__ . '/parts/tarjeta-envio-desafio.php';
                            ?>
                        </li><br>
                        <?php $first = false; ?>
                    <?php endforeach; ?>
                </ul>
                <section class="pagination">
                    <?php if ($paginaActual > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual - 1])) ?>" class="page-link prev">« Anterior</a>
                    <?php endif; ?>

                    <span class="page-info">Página <?= $paginaActual ?> de <?= $totalPaginas ?></span>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual + 1])) ?>" class="page-link next">Siguiente »</a>
                    <?php endif; ?>
                </section>
            </section>

            <aside> 
                <?php
                $rangoSeleccionado = $_GET['rango'] ?? '';
                ?>
                <section aria-labelledby="filtro-rango">
                <h2 id="filtro-rango">Filtrar por rango</h2>
                <form method="get" class="filtros-rango-form">
                    <ul class="filtros-rango">
                    <?php
                        // Definimos los rangos y su color semántico
                        $rangos = [
                        'Principiante'  => 'principiante',
                        'Amateur'       => 'amateur',
                        'SemiPro'       => 'semipro',
                        'Profesional'   => 'profesional',
                        ];
                        foreach ($rangos as $label => $clase) {
                        // ¿Es el que está seleccionado?
                        $activo = ($rangoSeleccionado === $label) ? ' activo' : '';
                        echo sprintf(
                            '<li><button type="submit" name="rango" value="%1$s" class="%2$s%3$s">%1$s</button></li>',
                            $label,
                            $clase,
                            $activo
                        );
                        }
                    ?>
                    </ul>
                </form>
                </section>

                
                <section aria-labelledby="ordenar">
                    <h2 id="ordenar">Ordenar por</h2>
                    <form class="radio-btns" method="get">
                        <label>
                        <input type="radio"
                                name="orden"
                                value="desc"
                                <?= $orden === 'desc' ? 'checked' : '' ?>>
                        Menor a mayor ELO
                        </label><br>
                        <label>
                        <input type="radio"
                                name="orden"
                                value="asc"
                                <?= $orden === 'asc' ? 'checked' : '' ?>>
                        Mayor a menor ELO
                        </label><br>
                        <label>
                        <input type="radio"
                                name="orden"
                                value="alpha"
                                <?= $orden === 'alpha' ? 'checked' : '' ?>>
                        Alfabéticamente
                        </label><br>
                        <button type="submit">Ordenar</button>
                    </form>
                </section>

                <section aria-labelledby="zona-busqueda">
                    <h2 id="zona-busqueda">Zona de búsqueda</h2>
                    <form id="mapForm" action="procesar.php" method="POST">
                        <div class="input-group">
                            <label for="lat">Latitud:</label>
                            <input type="text" id="lat" name="lat" readonly />
                        </div>
                        <div class="input-group">
                            <label for="lng">Longitud:</label>
                            <input type="text" id="lng" name="lng" readonly />
                        </div>
                        <div class="input-group">
                            <label for="radiusSlider">Radio del área (km)</label>
                        </div>
                        <div class="input-group">
                            <input type="range" id="radiusSlider" name="radius_km" min="0.1" max="10" step="0.1" value="1">
                            <span id="radiusValue">1.0</span>
                        </div>
                        <button type="submit">Enviar</button>
                    </form>
                    
                </section>
                <figure>
                    <div id="map"></div>
                </figure>
            </aside>
        </section>
    </main>
    <?php
        require "parts/footer.php";
    ?>
    <<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    var map = L.map('map').setView([-34.57, -59.11], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker, circle;
    var slider = document.getElementById('radiusSlider');
    var output = document.getElementById('radiusValue');
    output.textContent = slider.value;

    function updateLatLngFields(lat, lng) {
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);
    }

    function placeMarker(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        if (!marker) {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function(ev) {
            var pos = ev.target.getLatLng();
            updateLatLngFields(pos.lat, pos.lng);
            updateCircle(pos);
        });
        } else {
        marker.setLatLng([lat, lng]);
        }

        updateLatLngFields(lat, lng);
        updateCircle(e);
    }

    function updateCircle(e) {
        var km = parseFloat(slider.value);
        var m = km * 1000;
        if (!circle) {
        circle = L.circle(e.latlng, { radius: m }).addTo(map);
        } else {
        circle.setLatLng(e.latlng);
        circle.setRadius(m);
        }
    }

    map.on('click', placeMarker);

    slider.oninput = function() {
        output.textContent = parseFloat(this.value).toFixed(1);
        if (circle) {
        var m = parseFloat(this.value) * 1000;
        circle.setRadius(m);
        }
    };
    </script>
</body>
</html>