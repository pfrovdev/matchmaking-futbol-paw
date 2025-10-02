<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Equipo</title>
    <link rel="stylesheet" href="css/register-login-form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="./css/parts/map.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script src="./js/maps.js" defer></script>
</head>

<body>
    <?php
    $errors = $_SESSION['errors'] ?? [];
    $equipo_temp = $_SESSION['equipo_temp'] ?? [];
    unset($_SESSION['errors']);
    $estaLogueado = false;
    require "parts/header.php";
  ?>

    <main>
        <section class="container register-container">
            <header class="register-header">
                <h1>Crear equipo</h1>
                <p>Crea tu equipo e invitá a tus amigos!</p>
            </header>

            <?php if (!empty($errors)): ?>
            <section class="error-messages">
                <?php foreach ($errors as $error): ?>
                <p class="error-text"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>

            <section class="register-body">
                <form action="/register-team" method="post" class="form-container">
                    <label for="team-name">Nombre completo del equipo *</label>
                    <input type="text" id="team-name" name="team-name" placeholder="Sacachispas F.C" required
                        value="<?php echo htmlspecialchars($equipo_temp['team-name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />

                    <label for="team-acronym">Acrónimo del equipo *</label>
                    <input type="text" id="team-acronym" name="team-acronym" placeholder="SFC" required
                        value="<?php echo htmlspecialchars($equipo_temp['team-acronym'] ?? '') ?>" />

                    <fieldset class="form-group">
                        <legend>Tipo de equipo *</legend>
                        <?php $first = true; ?>
                        <?php foreach ($tipos as $tipo): ?>
                        <label>
                            <input type="radio" name="tipo_equipo"
                                value="<?= htmlspecialchars($tipo->id_tipo_equipo, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $first ? 'checked' : '' ?>>
                            <?= htmlspecialchars($tipo->tipo, ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                        <?php $first = false; endforeach; ?>
                    </fieldset>

                    <label for="team-zone">Zona del equipo *</label>
                    <!-- El input de zona lo genera Leaflet Geocoder y le pondremos id/name en JS -->

                    <input type="hidden" id="latDesktop" name="lat"
                        value="<?php echo htmlspecialchars($equipo_temp['lat'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                    <input type="hidden" id="lngDesktop" name="lng"
                        value="<?php echo htmlspecialchars($equipo_temp['lng'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />

                    <section aria-label="Mapa de ubicación del equipo">
                        <div id="map"
                            data-team-zone="<?php echo htmlspecialchars($equipo_temp['team-zone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </section>

                    <label for="team-motto">Lema del equipo</label>
                    <input type="text" id="team-motto" name="team-motto" placeholder="Lema del equipo"
                        value="<?php echo htmlspecialchars($equipo_temp['team-motto'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />

                    <p class="mandatory-note">
                        (* Campo obligatorio) ** Tu teléfono será utilizado para la coordinación entre equipos
                    </p>
                    <button type="submit" class="button">Crear equipo</button>
                </form>

                <aside class="image-container">
                    <img src="../icons/picture_messi.png" alt="messi picture" class="side-picture" />
                </aside>
            </section>
        </section>
    </main>

    <?php require "parts/footer.php"; ?>
</body>

</html>