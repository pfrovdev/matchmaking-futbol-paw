<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulario para crear un equipo en F5 Futbol Match">
    <meta name="keywords" content="Registro equipo, Crear equipo, F5 Futbol Match">
    <title>Crear Equipo</title>
    <link rel="stylesheet" href="css/register-login-form.css">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"
    />
</head>
<body>
    <?php
        session_start();
        $errors = $_SESSION['errors'] ?? [];
        $equipo_temp = $_SESSION['equipo_temp'] ?? [];
        unset($_SESSION['errors']);
        unset($_SESSION['equipo_temp']);
    ?>
    <?php require "parts/header-no-account.php"; ?>

    <main>
      <section class="container register-container">
        <!-- Header interno -->
        <header class="register-header">
          <h1>Crear equipo</h1>
          <p>Crea tu equipo e invitá a tus amigos!</p>
        </header>

        <!-- Errores -->
        <?php if (!empty($errors)): ?>
        <section class="error-messages">
          <?php foreach ($errors as $error): ?>
            <p class="error-text"><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <div class="register-body">
          <!-- Formulario -->
          <form action="/register-team" method="post" class="form-container">
            <label for="team-name">Nombre completo del equipo *</label>
            <input
              type="text"
              id="team-name"
              name="team-name"
              placeholder="Sacachispas F.C"
              required
              value="<?php echo htmlspecialchars($equipo_temp['team-name'] ?? '') ?>"
            />

            <label for="team-acronym">Acrónimo del equipo *</label>
            <input
              type="text"
              id="team-acronym"
              name="team-acronym"
              placeholder="SFC"
              required
              value="<?php echo htmlspecialchars($equipo_temp['team-acronym'] ?? '') ?>"
            />

            
            <fieldset class="form-group">
              <legend>Tipo de equipo *</legend>
              <?php $first = true; ?>
              <?php foreach ($tipos as $tipo): ?>
              <label>
                <input
                  type="radio"
                  name="tipo_equipo"
                  value="<?= htmlspecialchars($tipo['id_tipo_equipo']) ?>"
                  <?= $first ? 'checked' : '' ?>
                >
                <?= htmlspecialchars($tipo['tipo']); ?>
              </label>
              <?php $first = false; endforeach; ?>
            </fieldset>
            

            <label for="team-zone">Zona del equipo *</label>
            <input
              type="text"
              id="team-zone"
              name="team-zone"
              placeholder="Buscá en el mapa..."
              required
              value="<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '') ?>"
            />

            <!-- Campos para latitud y longitud -->
            <input type="hidden" id="lat" name="lat" />
            <input type="hidden" id="lng" name="lng" />

            <!-- Mapa -->
            <section aria-label="Mapa de ubicación del equipo">
              <div id="map" style="height: 300px; margin-bottom: 1.5rem;"></div>
            </section>

            <label for="team-motto">Lema del equipo</label>
            <input
              type="text"
              id="team-motto"
              name="team-motto"
              placeholder="Lema del equipo"
              value="<?php echo htmlspecialchars($equipo_temp['team-motto'] ?? '') ?>"
            />

            <p class="mandatory-note">
              (* Campo obligatorio) ** Tu teléfono será utilizado para la coordinación entre equipos
            </p>
            <button type="submit">Crear equipo</button>
          </form>

          <!-- Imagen lateral -->
          <div class="image-container">
            <img
              src="../icons/picture_messi.png"
              alt="messi picture"
              class="side-picture"
            />
          </div>
        </div>
      </section>
    </main>

    <?php require "parts/footer.php"; ?>

    <!-- Leaflet JS -->
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"
    ></script>
    <script>
      // Inicializa mapa en Luján, Buenos Aires
      var map = L.map('map').setView([-34.57, -59.11], 13);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      }).addTo(map);

      var marker;
      function updateLatLng(lat, lng) {
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);
      }

      function onMapClick(e) {
        var lat = e.latlng.lat,
          lng = e.latlng.lng;

        if (!marker) {
          marker = L.marker([lat, lng], { draggable: true }).addTo(map);
          marker.on('dragend', function (ev) {
            var pos = ev.target.getLatLng();
            updateLatLng(pos.lat, pos.lng);
          });
        } else {
          marker.setLatLng([lat, lng]);
        }

        updateLatLng(lat, lng);
      }

      map.on('click', onMapClick);
    </script>
</body>
</html>