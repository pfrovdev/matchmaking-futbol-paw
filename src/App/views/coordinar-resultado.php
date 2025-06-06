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
                Solo te quedan <span class="intentos">
                    <?=
                    htmlspecialchars(5 - $miUltimaIteracion);
                    ?>
                </span> intentos mas.
            </p>
            <div class="forms-wrapper">
                <div class="form-column">
                    <p>
                        <strong>
                            Formulario correspondiente a la iteración :
                            <?= htmlspecialchars($miUltimaIteracion + 1); ?>
                        </strong>
                    </p>
                    <form
                        method="POST"
                        action="procesar.php"
                        class="form-team">
                        <!-- Le agregamos también la clase grid-fields -->
                        <fieldset class="form-team">
                            <legend class="team-name cabj">
                                <?php
                                echo htmlspecialchars($formularioPartidoContrario->getEquipoVisitante()->getBadge()->getAcronimo());
                                ?>
                            </legend>

                            <!-- Goles (se muestra el valor que vino por POST o 0) -->
                            <div class="field">
                                <label for="goles_casla">
                                    <img
                                        src="icons/goles.png"
                                        alt="Icono Goles"
                                        class="icon" />
                                    Goles
                                </label>
                                <input
                                    type="number"
                                    id="goles_casla"
                                    name="goles_casla"
                                    min="0"
                                    value="0" />
                            </div>

                            <!-- Asistencias -->
                            <div class="field">
                                <label for="asistencias_casla">
                                    <img
                                        src="icons/asistencias.png"
                                        alt="Icono Asistencias"
                                        class="icon" />
                                    Asistencias
                                </label>
                                <input
                                    type="number"
                                    id="asistencias_casla"
                                    name="asistencias_casla"
                                    min="0"
                                    value="0" />
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
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_amarilla_casla"
                                            min="0"
                                            value="0" />
                                    </div>
                                    <div class="card-field">
                                        <img
                                            src="icons/tarjetaRoja.png"
                                            alt="Tarjeta Roja"
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_roja_casla"
                                            min="0"
                                            value="0" />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="form-team">
                            <legend class="team-name casla">
                                <?php
                                echo htmlspecialchars($formularioPartidoContrario->getEquipoLocal()->getBadge()->getAcronimo());
                                ?>
                            </legend>

                            <!-- Goles -->
                            <div class="field">
                                <label for="goles_casla">
                                    <img
                                        src="icons/goles.png"
                                        alt="Icono Goles"
                                        class="icon" />
                                    Goles
                                </label>
                                <input
                                    type="number"
                                    id="goles_casla"
                                    name="goles_casla"
                                    min="0"
                                    value="0" />
                            </div>

                            <!-- Asistencias -->
                            <div class="field">
                                <label for="asistencias_casla">
                                    <img
                                        src="icons/asistencias.png"
                                        alt="Icono Asistencias"
                                        class="icon" />
                                    Asistencias
                                </label>
                                <input
                                    type="number"
                                    id="asistencias_casla"
                                    name="asistencias_casla"
                                    min="0"
                                    value="0" />
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
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_amarilla_casla"
                                            min="0"
                                            value="0" />
                                    </div>
                                    <div class="card-field">
                                        <img
                                            src="icons/tarjetaRoja.png"
                                            alt="Tarjeta Roja"
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_roja_casla"
                                            min="0"
                                            value="0" />
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
                    <p>
                        <strong>
                            <?= $formularioPartidoContrario->getIteracionActual() == 0 ? "El rival aún no cargó su primer formulario" :
                                "Formulario correspondiente a la iteración : " . $formularioPartidoContrario->getIteracionActual() ?>
                        </strong>
                    </p>
                    <form
                        method="POST"
                        action="procesar.php"
                        class="form-team">
                        <!-- Le agregamos también la clase grid-fields -->
                        <fieldset class="form-team">
                            <legend class="team-name cabj">
                                <?php
                                echo htmlspecialchars($formularioPartidoContrario->getEquipoVisitante()->getBadge()->getAcronimo());
                                ?>
                            </legend>

                            <!-- Goles (se muestra el valor que vino por POST o 0) -->
                            <div class="field">
                                <label for="goles_casla">
                                    <img
                                        src="icons/goles.png"
                                        alt="Icono Goles"
                                        class="icon" />
                                    Goles
                                </label>
                                <input
                                    type="number"
                                    id="goles_casla"
                                    name="goles_casla"
                                    min="0"
                                    value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoVisitante()->getGoles()); ?>"
                                    disabled />
                            </div>

                            <!-- Asistencias -->
                            <div class="field">
                                <label for="asistencias_casla">
                                    <img
                                        src="icons/asistencias.png"
                                        alt="Icono Asistencias"
                                        class="icon" />
                                    Asistencias
                                </label>
                                <input
                                    type="number"
                                    id="asistencias_casla"
                                    name="asistencias_casla"
                                    min="0"
                                    value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoVisitante()->getAsistencias()); ?>"
                                    disabled />
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
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_amarilla_casla"
                                            min="0"
                                            value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoVisitante()->getTarjetasAmarilla()); ?>"
                                            disabled />
                                    </div>
                                    <div class="card-field">
                                        <img
                                            src="icons/tarjetaRoja.png"
                                            alt="Tarjeta Roja"
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_roja_casla"
                                            min="0"
                                            value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoVisitante()->getTarjetasRoja()); ?>"
                                            disabled />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="form-team">
                            <legend class="team-name casla">
                                <?php
                                echo htmlspecialchars($formularioPartidoContrario->getEquipoLocal()->getBadge()->getAcronimo());
                                ?>
                            </legend>

                            <!-- Goles (se muestra el valor que vino por POST o 0) -->
                            <div class="field">
                                <label for="goles_casla">
                                    <img
                                        src="icons/goles.png"
                                        alt="Icono Goles"
                                        class="icon" />
                                    Goles
                                </label>
                                <input
                                    type="number"
                                    id="goles_casla"
                                    name="goles_casla"
                                    min="0"
                                    value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoLocal()->getGoles()); ?>"
                                    disabled />
                            </div>

                            <!-- Asistencias -->
                            <div class="field">
                                <label for="asistencias_casla">
                                    <img
                                        src="icons/asistencias.png"
                                        alt="Icono Asistencias"
                                        class="icon" />
                                    Asistencias
                                </label>
                                <input
                                    type="number"
                                    id="asistencias_casla"
                                    name="asistencias_casla"
                                    min="0"
                                    value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoLocal()->getAsistencias()); ?>"
                                    disabled />
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
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_amarilla_casla"
                                            min="0"
                                            value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoLocal()->getTarjetasAmarilla()); ?>"
                                            disabled />
                                    </div>
                                    <div class="card-field">
                                        <img
                                            src="icons/tarjetaRoja.png"
                                            alt="Tarjeta Roja"
                                            class="icon-card" />
                                        <input
                                            type="number"
                                            name="tarjeta_roja_casla"
                                            min="0"
                                            value="<?php echo htmlspecialchars($formularioPartidoContrario->getEquipoLocal()->getTarjetasRoja()); ?>"
                                            disabled />
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

    <script src="/js/sidebar.js"></script>
</body>

</html>