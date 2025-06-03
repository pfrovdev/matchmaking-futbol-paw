<?php
  $datos_contrario = [
    'goles'            => $_POST['goles_contrario']            ?? 0,
    'asistencias'      => $_POST['asistencias_contrario']      ?? 0,
    'tarjeta_amarilla' => $_POST['tarjeta_amarilla_contrario'] ?? 0,
    'tarjeta_roja'     => $_POST['tarjeta_roja_contrario']     ?? 0,
  ];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Formulario para coordinar resultados de un partido - F5 Futbol Match">
  <title>Crear Equipo</title>
  <link rel="stylesheet" href="css/coordinar-resultado.css">
  <script src="/js/sidebar.js"></script>
</head>

<body>
  <?php require "parts/header.php"; ?>
  <?php require "parts/side-navbar.php"; ?>

  <main class="container">
    <section class="coordinar-resultado">
      <h1>Coordinar resultado</h1>
      <p class="subtitle">
        Si ambos formularios coinciden, podrás distribuir las estadísticas a tu equipo! 
        <br>
        Solo te quedan <span class="intentos">3</span> intentos.
      </p>
      <div class="forms-wrapper">
        <div class="form-column">
        <form
            method="POST"
            action="procesar.php"
            class="form-team"
        >
            <!-- Le agregamos también la clase grid-fields -->
            <fieldset class="form-team">
                    <legend class="team-name cabj">CABJ</legend>

                    <!-- Goles (se muestra el valor que vino por POST o 0) -->
                    <div class="field">
                    <label for="goles_casla">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_casla"
                        name="goles_casla"
                        min="0"
                        value="0"
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_casla">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_casla"
                        name="asistencias_casla"
                        min="0"
                        value="0"
                    />
                    </div>

                    <!-- Tarjetas -->
                    <div class="field tarjetas">
                    <span class="field-title">
                        Tarjetas
                    </span>
                    <div class="cards-group">
                        <div class="card-field">
                        <img
                            src="icons/tarjetaAmarilla.png"
                            alt="Tarjeta Amarilla"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_amarilla_casla"
                            min="0"
                            value="0"
                        />
                        </div>
                        <div class="card-field">
                        <img
                            src="icons/tarjetaRoja.png"
                            alt="Tarjeta Roja"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_roja_casla"
                            min="0"
                            value="0"
                        />
                        </div>
                    </div>
                    </div>
                    </fieldset>
                    <fieldset class="form-team">
                    <legend class="team-name casla">CASLA</legend>

                    <!-- Goles -->
                    <div class="field">
                    <label for="goles_casla">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_casla"
                        name="goles_casla"
                        min="0"
                        value="0"
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_casla">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_casla"
                        name="asistencias_casla"
                        min="0"
                        value="0"
                    />
                    </div>

                    <!-- Tarjetas -->
                    <div class="field tarjetas">
                    <span class="field-title">
                        Tarjetas
                    </span>
                    <div class="cards-group">
                        <div class="card-field">
                        <img
                            src="icons/tarjetaAmarilla.png"
                            alt="Tarjeta Amarilla"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_amarilla_casla"
                            min="0"
                            value="0"
                        />
                        </div>
                        <div class="card-field">
                        <img
                            src="icons/tarjetaRoja.png"
                            alt="Tarjeta Roja"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_roja_casla"
                            min="0"
                            value="0"
                        />
                        </div>
                    </div>
                    </div>

            
            </fieldset>
                <button type="submit">Enviar resultado</button>

            </form>
                </div>

                <!-- ===============================================
                    2) SEGUNDO FORMULARIO: datos “EQUIPO CONTRARIO”
                    =============================================== -->
                    <div class="form-column">
                <form
            method="POST"
            action="procesar.php"
            class="form-team"
        >
            <!-- Le agregamos también la clase grid-fields -->
            <fieldset class="form-team">
                    <legend class="team-name cabj">CABJ</legend>

                    <!-- Goles (se muestra el valor que vino por POST o 0) -->
                    <div class="field">
                    <label for="goles_casla">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_casla"
                        name="goles_casla"
                        min="0"
                        value="<?php echo htmlspecialchars($datos_contrario['goles']); ?>"
                        disabled
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_casla">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_casla"
                        name="asistencias_casla"
                        min="0"
                        value="<?php echo htmlspecialchars($datos_contrario['asistencias']); ?>"
                        disabled
                    />
                    </div>

                    <!-- Tarjetas -->
                    <div class="field tarjetas">
                    <span class="field-title">
                        Tarjetas
                    </span>
                    <div class="cards-group">
                        <div class="card-field">
                        <img
                            src="icons/tarjetaAmarilla.png"
                            alt="Tarjeta Amarilla"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_amarilla_casla"
                            min="0"
                            value="<?php echo htmlspecialchars($datos_contrario['tarjeta_amarilla']); ?>"
                            disabled
                        />
                        </div>
                        <div class="card-field">
                        <img
                            src="icons/tarjetaRoja.png"
                            alt="Tarjeta Roja"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_roja_casla"
                            min="0"
                            value="<?php echo htmlspecialchars($datos_contrario['tarjeta_roja']); ?>"
                            disabled
                        />
                        </div>
                    </div>
                    </div>
                    </fieldset>
                    <fieldset class="form-team">
                    <legend class="team-name casla">CASLA</legend>

                    <!-- Goles (se muestra el valor que vino por POST o 0) -->
                    <div class="field">
                    <label for="goles_casla">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_casla"
                        name="goles_casla"
                        min="0"
                        value="<?php echo htmlspecialchars($datos_contrario['goles']); ?>"
                        disabled
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_casla">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_casla"
                        name="asistencias_casla"
                        min="0"
                        value="<?php echo htmlspecialchars($datos_contrario['asistencias']); ?>"
                        disabled
                    />
                    </div>

                    <!-- Tarjetas -->
                    <div class="field tarjetas">
                    <span class="field-title">
                        Tarjetas
                    </span>
                    <div class="cards-group">
                        <div class="card-field">
                        <img
                            src="icons/tarjetaAmarilla.png"
                            alt="Tarjeta Amarilla"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_amarilla_casla"
                            min="0"
                            value="<?php echo htmlspecialchars($datos_contrario['tarjeta_amarilla']); ?>"
                            disabled
                        />
                        </div>
                        <div class="card-field">
                        <img
                            src="icons/tarjetaRoja.png"
                            alt="Tarjeta Roja"
                            class="icon-card"
                        />
                        <input
                            type="number"
                            name="tarjeta_roja_casla"
                            min="0"
                            value="<?php echo htmlspecialchars($datos_contrario['tarjeta_roja']); ?>"
                            disabled
                        />
                        </div>
                    </div>
                    </div>

            
            </fieldset>
            <button class="btn-whatsapp" type="submit">Abrir whatsapp</button>
            </form>
        </div>
      </div>
    </section>
  </main>
 
  <?php require "parts/footer.php"; ?>

</body>

</html> 