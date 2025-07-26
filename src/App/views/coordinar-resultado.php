<?php
$flash = $_SESSION['flash'] ?? ['mensaje' => '', 'finalizado' => false, 'tipo' => ''];
unset($_SESSION['flash']);

$maxIntentos = 5;
$intentosRestantes = $maxIntentos - $miUltimaIteracion;
$partidoFinalizado = false;
$mismatchedFields = [];
$statusMessage = "";
$statusType = "info";

$myFormDisabled = false;
$rivalFormDisabled = true;

// Lógica de comparación y determinación de estado
if ($formularioPartidoContrario->getIteracionActual() === 0) {
    if ($miUltimaIteracion === 0) {
        $statusMessage = "Ingresa el resultado de tu partido para iniciar la coordinación.";
        $statusType = "info";
        $myFormDisabled = false; // El usuario puede ingresar su primer resultado
    } else {
        $statusMessage = "Tu resultado fue enviado. Esperando que el equipo contrario cargue el suyo.";
        $statusType = "info";
        $myFormDisabled = true; // Se deshabilita mientras espera al rival
    }
} else {
    // Ambos equipos cargaron al menos una vez, ahora comparamos
    $match = true;
    $fieldsToCompare = [
        // Tu equipo (visitante desde la perspectiva del formulario del rival)
        'goles_local' => ['mine' => $miFormulario->getEquipoLocal()->getGoles(), 'rival' => $formularioPartidoContrario->getEquipoVisitante()->getGoles()],
        'asistencias_local' => ['mine' => $miFormulario->getEquipoLocal()->getAsistencias(), 'rival' => $formularioPartidoContrario->getEquipoVisitante()->getAsistencias()],
        'tarjetas_amarillas_local' => ['mine' => $miFormulario->getEquipoLocal()->getTarjetasAmarilla(), 'rival' => $formularioPartidoContrario->getEquipoVisitante()->getTarjetasAmarilla()],
        'tarjetas_rojas_local' => ['mine' => $miFormulario->getEquipoLocal()->getTarjetasRoja(), 'rival' => $formularioPartidoContrario->getEquipoVisitante()->getTarjetasRoja()],

        // Equipo rival (local desde la perspectiva del formulario del rival)
        'goles_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getGoles(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getGoles()],
        'asistencias_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getAsistencias(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getAsistencias()],
        'tarjetas_amarillas_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getTarjetasAmarilla(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getTarjetasAmarilla()],
        'tarjetas_rojas_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getTarjetasRoja(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getTarjetasRoja()],
    ];

    foreach ($fieldsToCompare as $field_name => $values) {
        if ((int)$values['mine'] !== (int)$values['rival']) {
            $match = false;
            $mismatchedFields[] = $field_name;
        }
    }
    if ($match) {
        $statusMessage = "¡Resultados coinciden! El partido ha sido coordinado con éxito.";
        $statusType = "success";
        $partidoFinalizado = true;
        $myFormDisabled = true;
    } else {
        if ($miUltimaIteracion >= $maxIntentos) {
            $statusMessage = "¡Límite de intentos alcanzado! Los resultados no coinciden. Se requiere revisión manual.";
            $statusType = "error";
            $partidoFinalizado = true;
            $myFormDisabled = true;
        } else {
            $statusMessage = "¡Resultados no coinciden! Revisa los campos resaltados e intenta de nuevo. Tienes {$intentosRestantes} intentos restantes.";
            $statusType = "warning";
            $myFormDisabled = false;
        }
    }
}

if ($flash['mensaje']) {
    $statusMessage = $flash['mensaje'];
    $statusType = $flash['finalizado'] ? 'success' : ($flash['tipo'] ?? 'info');

    if ($flash['finalizado']) {
        $partidoFinalizado = true;
        $myFormDisabled = true;
    }
}

$myTeamAcronym = $miEquipo->getAcronimo();
$rivalTeamAcronym = $formularioPartidoContrario->getEquipoLocal()->getBadge()->getAcronimo();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulario para coordinar resultados de un partido - F5 Futbol Match">
    <title>Coordinar Resultado</title>
    <link rel="stylesheet" href="css/coordinar-resultado.css">
    <script src="/js/sidebar.js"></script>
</head>

<body>
    <?php require "parts/header.php"; ?>
    <?php require "parts/side-navbar.php"; ?>

    <main class="container">
        <section class="coordinar-resultado">
            <h1>Coordinar resultado</h1>

            <div class="alert alert-<?= htmlspecialchars($statusType); ?>">
                <?= htmlspecialchars($statusMessage); ?>
            </div>

            <p class="subtitle">
                Si ambos formularios coinciden, podrás distribuir las estadísticas a tu equipo!
                <?php if (!$partidoFinalizado): ?>
                    <br>
                    Solo te quedan <span class="intentos"><?= htmlspecialchars($intentosRestantes); ?></span> intentos más.
                <?php endif; ?>
            </p>

            <div class="progress-bar-container">
                <?php for ($i = 0; $i < $maxIntentos; $i++): ?>
                    <span class="progress-dot <?= ($i < $miUltimaIteracion) ? 'filled' : ''; ?>"></span>
                <?php endfor; ?>
                <span class="progress-text">Intento <?= htmlspecialchars($miUltimaIteracion); ?>/<?= htmlspecialchars($maxIntentos); ?></span>
            </div>


            <div class="forms-wrapper">
                <div class="tab-nav" style="display: none;">
                    <button class="tab-button active" data-tab="my-form"><?= htmlspecialchars($myTeamAcronym); ?> (Tú)</button>
                    <button class="tab-button" data-tab="rival-form"><?= htmlspecialchars($rivalTeamAcronym); ?> (Rival)</button>
                </div>

                <div class="form-column my-form-column active" id="my-form">
                    <p>
                        <strong>
                            Tu formulario (Iteración: <?= htmlspecialchars($miUltimaIteracion + 1); ?>)
                        </strong>
                    </p>
                    <form
                        method="POST"
                        action="/coordinar-resultado?id_partido=<?= htmlspecialchars($id_partido); ?>"
                        class="form-team">
                        <?php
                        $teamAcronym = $miEquipo->getAcronimo();
                        $values = [
                            'goles' => $miFormulario->getEquipoLocal()->getGoles(),
                            'asistencias' => $miFormulario->getEquipoLocal()->getAsistencias(),
                            'amarillas' => $miFormulario->getEquipoLocal()->getTarjetasAmarilla(),
                            'rojas' => $miFormulario->getEquipoLocal()->getTarjetasRoja(),
                        ];
                        $prefixName = 'local';
                        $mismatchedFieldsFiltered = array_filter($mismatchedFields, function ($field) use ($prefixName) {
                            return strpos($field, '_' . $prefixName) !== false;
                        });
                        $disabled = $myFormDisabled;
                        $primeraIteracion = $miUltimaIteracion === 0;
                        $primeraIteracionRival = $formularioPartidoContrario->getIteracionActual() == 0;
                        require 'parts/match-form-fields.php';

                        $teamAcronym = $formularioPartidoContrario->getEquipoLocal()->getBadge()->getAcronimo();
                        $values = [
                            'goles' => $miFormulario->getEquipoVisitante()->getGoles(),
                            'asistencias' => $miFormulario->getEquipoVisitante()->getAsistencias(),
                            'amarillas' => $miFormulario->getEquipoVisitante()->getTarjetasAmarilla(),
                            'rojas' => $miFormulario->getEquipoVisitante()->getTarjetasRoja(),
                        ];
                        $prefixName = 'visitante';
                        $mismatchedFieldsFiltered = array_filter($mismatchedFields, function ($field) use ($prefixName) {
                            return strpos($field, '_' . $prefixName) !== false;
                        });
                        $disabled = $myFormDisabled;
                        $primeraIteracion = $miUltimaIteracion === 0;
                        $primeraIteracionRival = $formularioPartidoContrario->getIteracionActual() == 0;
                        require 'parts/match-form-fields.php';
                        ?>

                        <?php if (!$partidoFinalizado && !$myFormDisabled): ?>
                            <button type="submit" name="submit_my_form">
                                Enviar resultado
                            </button>
                        <?php endif; ?>

                    </form>
                </div>

                <div class="form-column rival-form-column" id="rival-form">
                    <p>
                        <strong>
                            <?php
                            if ($formularioPartidoContrario->getIteracionActual() == 0) {
                                echo "El rival aún no cargó su primer formulario";
                            } else {
                                echo "Formulario del rival (Iteración: " . htmlspecialchars($formularioPartidoContrario->getIteracionActual()) . ")";
                            }
                            ?>
                        </strong>
                    </p>
                    <form class="form-team">
                        <?php
                        $teamAcronym = $formularioPartidoContrario->getEquipoVisitante()->getBadge()->getAcronimo();
                        $values = [
                            'goles' => $formularioPartidoContrario->getEquipoVisitante()->getGoles(),
                            'asistencias' => $formularioPartidoContrario->getEquipoVisitante()->getAsistencias(),
                            'amarillas' => $formularioPartidoContrario->getEquipoVisitante()->getTarjetasAmarilla(),
                            'rojas' => $formularioPartidoContrario->getEquipoVisitante()->getTarjetasRoja(),
                        ];
                        $disabled = $rivalFormDisabled;
                        $prefixName = 'local';
                        $mismatchedFieldsFiltered = array_filter($mismatchedFields, function ($field) use ($prefixName) {
                            return strpos($field, '_' . $prefixName) !== false;
                        });
                        $primeraIteracion = $miUltimaIteracion === 0;
                        $primeraIteracionRival = $formularioPartidoContrario->getIteracionActual() == 0;
                        require 'parts/match-form-fields.php';

                        $teamAcronym = $formularioPartidoContrario->getEquipoLocal()->getBadge()->getAcronimo();
                        $values = [
                            'goles' => $formularioPartidoContrario->getEquipoLocal()->getGoles(),
                            'asistencias' => $formularioPartidoContrario->getEquipoLocal()->getAsistencias(),
                            'amarillas' => $formularioPartidoContrario->getEquipoLocal()->getTarjetasAmarilla(),
                            'rojas' => $formularioPartidoContrario->getEquipoLocal()->getTarjetasRoja(),
                        ];
                        $disabled = $rivalFormDisabled;
                        $prefixName = 'visitante';
                        $mismatchedFieldsFiltered = array_filter($mismatchedFields, function ($field) use ($prefixName) {
                            return strpos($field, '_' . $prefixName) !== false;
                        });
                        $primeraIteracion = $miUltimaIteracion === 0;
                        $primeraIteracionRival = $formularioPartidoContrario->getIteracionActual() == 0;
                        require 'parts/match-form-fields.php';
                        ?>

                        <?php if (!$partidoFinalizado): ?>
                            <button type="button"
                                class="btn-whatsapp"
                                onclick="window.open('https://wa.me/?text=Hola%2C%20soy%20del%20equipo%20<?= urlencode($myTeamAcronym); ?>.%20Por%20favor%2C%20carga%20el%20resultado%20del%20partido%20o%20revisa%20nuestras%20diferencias.', '_blank')">
                                Abrir WhatsApp
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php if ($partidoFinalizado): ?>
                <div class="final-buttons-container">
                    <button class="btn" type="button" id="btnTerminarPartido">Terminar partido</button>
                    <button class="btn" type="button" id="btnCalificarDeportividad">Calificar deportividad</button>
                </div>

                <div id="calificacionModal" class="modal">
                    <div class="modal-content">
                        <span class="close-button">&times;</span>
                        <form class="calificar-seccion" id="formCalificacion" action="/comentarios" method="POST">
                            <h3>Califica la deportividad del equipo rival</h3>
                            <div class="rating-group" id="ratingGroup">
                                <span class="rating-icon" data-value="1">⚽</span>
                                <span class="rating-icon" data-value="2">⚽</span>
                                <span class="rating-icon" data-value="3">⚽</span>
                                <span class="rating-icon" data-value="4">⚽</span>
                                <span class="rating-icon" data-value="5">⚽</span>
                            </div>

                            <input type="hidden" name="deportividad" id="deportividadInput" value="">
                            <input type="hidden" name="id_partido" value="123"> <textarea class="textArea" maxlength="100" name="comentario" placeholder="Deja un comentario... (100 caracteres max.)"></textarea>
                            <button type="submit" class="btn">Enviar Calificación</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php require "parts/footer.php"; ?>
    <script src="/js/sidebar.js"></script>
    <script type="module" src="/js/pages/CoordinarResultado.js"></script>
</body>

</html>