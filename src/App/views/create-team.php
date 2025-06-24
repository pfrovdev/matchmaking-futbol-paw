<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Equipo</title>
  <link rel="stylesheet" href="css/register-login-form.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
</head>

<body>
  <?php
    $errors      = $_SESSION['errors'] ?? [];
    $equipo_temp = $_SESSION['equipo_temp'] ?? [];
    unset($_SESSION['errors']);
  ?>
  <?php require "parts/header-no-account.php"; ?>

  <main>
    <section class="container register-container">
      <header class="register-header">
        <h1>Crear equipo</h1>
        <p>Crea tu equipo e invitá a tus amigos!</p>
      </header>

      <?php if (!empty($errors)): ?>
        <section class="error-messages">
          <?php foreach ($errors as $error): ?>
            <p class="error-text"><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>

      <div class="register-body">
        <form action="/register-team" method="post" class="form-container">
          <label for="team-name">Nombre completo del equipo *</label>
          <input
            type="text"
            id="team-name"
            name="team-name"
            placeholder="Sacachispas F.C"
            required
            value="<?php echo htmlspecialchars($equipo_temp['team-name'] ?? '') ?>" />

          <label for="team-acronym">Acrónimo del equipo *</label>
          <input
            type="text"
            id="team-acronym"
            name="team-acronym"
            placeholder="SFC"
            required
            value="<?php echo htmlspecialchars($equipo_temp['team-acronym'] ?? '') ?>" />

          <fieldset class="form-group">
            <legend>Tipo de equipo *</legend>
            <?php $first = true; ?>
            <?php foreach ($tipos as $tipo): ?>
              <label>
                <input
                  type="radio"
                  name="tipo_equipo"
                  value="<?= htmlspecialchars($tipo->id_tipo_equipo) ?>"
                  <?= $first ? 'checked' : '' ?>>
                <?= htmlspecialchars($tipo->tipo); ?>
              </label>
            <?php $first = false; endforeach; ?>
          </fieldset>

          <label for="team-zone">Zona del equipo *</label>
          <!-- El input de zona lo genera Leaflet Geocoder y le pondremos id/name en JS -->

          <input type="hidden" id="lat" name="lat"
                 value="<?php echo htmlspecialchars($equipo_temp['lat'] ?? '') ?>" />
          <input type="hidden" id="lng" name="lng"
                 value="<?php echo htmlspecialchars($equipo_temp['lng'] ?? '') ?>" />

          <section aria-label="Mapa de ubicación del equipo">
            <div id="map" style="height: 300px; margin-bottom: 1.5rem;"></div>
          </section>

          <label for="team-motto">Lema del equipo</label>
          <input
            type="text"
            id="team-motto"
            name="team-motto"
            placeholder="Lema del equipo"
            value="<?php echo htmlspecialchars($equipo_temp['team-motto'] ?? '') ?>" />

          <p class="mandatory-note">
            (* Campo obligatorio) ** Tu teléfono será utilizado para la coordinación entre equipos
          </p>
          <button type="submit">Crear equipo</button>
        </form>

        <div class="image-container">
          <img
            src="../icons/picture_messi.png"
            alt="messi picture"
            class="side-picture" />
        </div>
      </div>
    </section>
  </main>

  <?php require "parts/footer.php"; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){

      // 1) Inicializo mapa y geocoder
      var map = L.map('map').setView([-34.57, -59.11], 13);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
      }).addTo(map);

      var geocoder = L.Control.geocoder({
        collapsed: false,
        placeholder: 'Buscá tu zona...',
        defaultMarkGeocode: false
      }).addTo(map);

      // 2) Referencia única al input “team-zone”
      var gcContainer = geocoder._container; 
      var inputs = gcContainer.querySelectorAll('input[type="search"]');
      var gcInput = Array.from(inputs).find(function(i){
        return i.offsetParent !== null; 
        });
      gcInput.id = 'team-zone';
      gcInput.name = 'team-zone';
      gcInput.placeholder = 'Buscá en el mapa…';
      gcInput.required = true;
      gcInput.value = "<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '') ?>";

      // 3) Marker y actualizar lat/lng
      var marker;
      function updateLatLng(lat, lng) {
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);
      }

      // 4) Reverse geocode usando siempre getElementById
      function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`)
          .then(r => r.json())
          .then(data => {
            if (data.display_name) {
              gcInput.value = data.display_name;
            }
          })
          .catch(console.error);
      }

      // 5) Crear/mover marcador
      function placeMarker(lat, lng) {
        if (!marker) {
          marker = L.marker([lat, lng], { draggable: true }).addTo(map);
          marker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            updateLatLng(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng);
          });
        } else {
          marker.setLatLng([lat, lng]);
        }
        updateLatLng(lat, lng);
        reverseGeocode(lat, lng);
      }

      map.on('click', function(e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
      });

      // 6) Cuando el geocoder devuelve algo
      geocoder.on('markgeocode', function(e) {
        var c = e.geocode.center;
        map.setView(c, 15);
        placeMarker(c.lat, c.lng);
        gcInput.value = e.geocode.name;
      });
    });
  </script>
</body>

</html>