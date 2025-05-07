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
                            <label for="radiusSlider">Radio (m):</label>
                            <!-- Control tipo "range" para el radio -->
                            <input type="range" id="radiusSlider" name="radius" min="0" max="1000" step="10" value="100" />
                            <span id="radiusValue">100</span> metros
                        </div>
                        <button type="submit">Enviar</button>
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
    <!-- Incluir Leaflet JS (v1.9.4) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <!-- Incluir script personalizado (aquí se podría usar un archivo externo, p.ej. map.js) -->
    <script>
        // Inicializar el mapa centrado en Luján, Buenos Aires (lat ≈ -34.57, lon ≈ -59.11) con zoom 13
        var map = L.map('map').setView([-34.57, -59.11], 13);
        // Añadir capa de teselas (OpenStreetMap) al mapa
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker; // Variable global para el marcador
        var circle; // Variable global para el círculo

        // Función para actualizar los campos de texto de latitud/longitud
        function updateLatLngFields(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(6);
            document.getElementById('lng').value = lng.toFixed(6);
        }

        // Función que coloca o mueve el marcador en el mapa al hacer clic
        function placeMarker(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Si el marcador no existe, crearlo (puede ser draggable)
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                // Si el marcador es arrastrado, actualizar campos y círculo
                marker.on('dragend', function(ev) {
                    var pos = ev.target.getLatLng();
                    updateLatLngFields(pos.lat, pos.lng);
                    updateCircle(pos); // Recentrar el círculo
                });
            } else {
                // Si ya existe, solo moverlo a la nueva posición
                marker.setLatLng([lat, lng]);
            }

            // Actualizar los campos de lat y lng en el formulario
            updateLatLngFields(lat, lng);

            // Dibujar o actualizar el círculo centrado en el marcador
            updateCircle({ latlng: L.latLng(lat, lng) });
        }

        // Función para crear/actualizar el círculo alrededor del marcador
        function updateCircle(e) {
            var radius = document.getElementById('radiusSlider').value;
            if (!circle) {
                // Crear círculo por primera vez con radio inicial
                circle = L.circle(e.latlng, { radius: radius }).addTo(map);
            } else {
                // Si existe, actualizar centro y radio
                circle.setLatLng(e.latlng);
                circle.setRadius(radius);
            }
        }

        // Manejar evento de clic en el mapa: colocar o mover el marcador
        map.on('click', placeMarker);

        // Actualizar visualización del valor del slider al cambiar el radio
        var slider = document.getElementById('radiusSlider');
        var output = document.getElementById('radiusValue');
        output.textContent = slider.value;
        slider.oninput = function() {
            output.textContent = this.value;
            // Si el círculo ya está dibujado, actualizar su radio en tiempo real
            if (circle) {
                circle.setRadius(this.value);
            }
        };
        
        /*
        // Ejemplo de envío de datos con AJAX usando Fetch
        // Se intercepta el submit para enviar vía AJAX en lugar de recargar la página.
        document.getElementById('mapForm').addEventListener('submit', function(evt) {
            evt.preventDefault(); // Prevenir envío tradicional
            var formData = new FormData(this);
            fetch('procesar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                console.log('Respuesta del servidor:', result);
                alert('Datos enviados correctamente.');
            })
            .catch(error => console.error('Error al enviar los datos:', error));
        });*/
    </script>
</body>
</html>