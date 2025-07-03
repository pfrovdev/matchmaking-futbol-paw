<section class="zona-busqueda-container" aria-labelledby="zona-busqueda">
    <h2 id="zona-busqueda">Zona de búsqueda</h2>
    <section class="zona-busqueda-content">
        <form id="mapForm" method="GET">
            <input type="hidden" id="lat" name="lat" readonly value="<?= htmlspecialchars($_GET['lat'] ?? '') ?>" />
            <input type="hidden" id="lng" name="lng" readonly value="<?= htmlspecialchars($_GET['lng'] ?? '') ?>" />
            <div class="input-group">
                <label for="radiusSlider">Radio del área (km)</label>
            </div>
            <div class="input-group">
                <input type="range" id="radiusSlider" name="radius_km" min="0.1" max="10" step="0.1"
                    value="<?= htmlspecialchars($_GET['radius_km'] ?? 1) ?>">
                <span id="radiusValue"><?= htmlspecialchars($_GET['radius_km'] ?? 1.0) ?></span>
            </div>
            <button class="boton-filtro-zona" type="submit">Enviar</button>
        </form>
        <figure>
            <div id="map" data-team-zone="<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '') ?>">
            </div>
        </figure>
    </section>
</section>
