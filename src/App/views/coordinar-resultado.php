<?php
  $datos_contrario = [
    'acronimo'         => $_POST['acronimo_contrario']         ?? 'CASLA',
    'goles'            => $_POST['goles_contrario']            ?? 0,
    'asistencias'      => $_POST['asistencias_contrario']      ?? 0,
    'tarjeta_amarilla' => $_POST['tarjeta_amarilla_contrario'] ?? 0,
    'tarjeta_roja'     => $_POST['tarjeta_roja_contrario']     ?? 0,
  ];
  echo "<script>window.resultadosCoinciden = " . ($confirmacion ? 'true' : 'false') . ";</script>";
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
  <script src="/js/coordinar-resultados.js"></script>
</head>

<body>
  <?php require "parts/header.php"; ?>
  <?php require "parts/side-navbar.php"; ?>

  <main class="container">
    <section class="coordinar-resultado">
      <h1>Coordinar resultado</h1>
      <p class="subtitle">
        Si ambos formularios coinciden, el partido sera valido para las estadísticas de tu equipo! 
        <br>
        Solo te quedan <span class="intentos">3</span> intentos.
      </p>
      <div class="forms-wrapper">
        <div class="form-column">
        <form
            id="coordinar"
            method="POST"
            action="procesar.php"
            class="form-team"
        >
            <!-- Le agregamos también la clase grid-fields -->
            <fieldset class="form-team">
                    <legend class="team-name local"><?=htmlspecialchars($miEquipo->fields['acronimo'])?></legend>

                    <!-- Goles (se muestra el valor que vino por POST o 0) -->
                    <div class="field">
                    <label for="goles_local">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_local"
                        name="goles_local"
                        min="0"
                        value="0"
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_local">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_local"
                        name="asistencias_local"
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
                            name="tarjeta_amarilla_local"
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
                            name="tarjeta_roja_local"
                            min="0"
                            value="0"
                        />
                        </div>
                    </div>
                    </div>
                    </fieldset>
                    <fieldset class="form-team">
                    <legend class="team-name visitante"><?=htmlspecialchars($datos_contrario['acronimo'])?></legend>

                    <!-- Goles -->
                    <div class="field">
                    <label for="goles_visitante">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_visitante"
                        name="goles_visitante"
                        min="0"
                        value="0"
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_visitante">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_visitante"
                        name="asistencias_visitante"
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
                            name="tarjeta_amarilla_visitante"
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
                            name="tarjeta_roja_visitante"
                            min="0"
                            value="0"
                        />
                        </div>
                    </div>
                    </div>

            
            </fieldset>
               


            </form>
                </div>

                <!-- ===============================================
                    2) SEGUNDO FORMULARIO: datos “EQUIPO CONTRARIO”
                    =============================================== -->
                    <div class="form-column">
                <form
                class="form-team"
                >
            <!-- Le agregamos también la clase grid-fields -->
            <fieldset class="form-team">
                    <legend class="team-name local"><?=htmlspecialchars($miEquipo->fields['acronimo'])?></legend>

                    <!-- Goles (se muestra el valor que vino por POST o 0) -->
                    <div class="field">
                    <label for="goles_local">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_local"
                        name="goles_local"
                        min="0"
                        value="<?php echo htmlspecialchars($datos_contrario['goles']); ?>"
                        disabled
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_local">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_local"
                        name="asistencias_local"
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
                            name="tarjeta_amarilla_local"
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
                            name="tarjeta_roja_local"
                            min="0"
                            value="<?php echo htmlspecialchars($datos_contrario['tarjeta_roja']); ?>"
                            disabled
                        />
                        </div>
                    </div>
                    </div>
                    </fieldset>
                    <fieldset class="form-team">
                    <legend class="team-name visitante"><?=htmlspecialchars($datos_contrario['acronimo'])?></legend>

                    <!-- Goles (se muestra el valor que vino por POST o 0) -->
                    <div class="field">
                    <label for="goles_visitante">
                        <img
                        src="icons/goles.png"
                        alt="Icono Goles"
                        class="icon"
                        />
                        Goles
                    </label>
                    <input
                        type="number"
                        id="goles_visitante"
                        name="goles_visitante"
                        min="0"
                        value="<?php echo htmlspecialchars($datos_contrario['goles']); ?>"
                        disabled
                    />
                    </div>

                    <!-- Asistencias -->
                    <div class="field">
                    <label for="asistencias_visitante">
                        <img
                        src="icons/asistencias.png"
                        alt="Icono Asistencias"
                        class="icon"
                        />
                        Asistencias
                    </label>
                    <input
                        type="number"
                        id="asistencias_visitante"
                        name="asistencias_visitante"
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
                            name="tarjeta_amarilla_visitante"
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
                            name="tarjeta_roja_visitante"
                            min="0"
                            value="<?php echo htmlspecialchars($datos_contrario['tarjeta_roja']); ?>"
                            disabled
                        />
                        </div>
                    </div>
                    </div>

            
            </fieldset>
            
            
        </div>
        </form>
        <div class="btn-group">
            <button type="submit" id="btn-enviar" form="coordinar">Enviar resultado</button>
            <div id="contenedor-calificar" style="display: none; margin-top: 1rem;">
                <!-- botón “Calificar deportividad” -->
            </div>
            <button type="button" id="btn-whatsapp" class="btn-whatsapp">Abrir whatsapp</button>
            <div id="contenedor-estrellas" style="display: none; margin-top: 1rem;">
            <!--  5 estrellas y la casilla de comentarios -->
            </div>
        </div>
      </div>
    </section>
  </main>
 
  <?php require "parts/footer.php"; ?>

</body>

</html> 