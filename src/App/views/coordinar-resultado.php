<?php
$flash = $_SESSION['flash'] ?? ['mensaje' => '', 'finalizado' => false, 'tipo' => ''];
unset($_SESSION['flash']);

$maxIntentos = 5;
$intentosRestantes = $maxIntentos - $miUltimaIteracion;
// Solo confía en el flag que viene del controlador
$partidoFinalizado = $flash['finalizado'];
$mismatchedFields = [];

// Estado inicial vacío o desde flash
$statusMessage = $flash['mensaje'] ?? '';
$statusType = $flash['tipo'] ?? 'info';

// Formularios deshabilitados si ya finalizó
$myFormDisabled = $partidoFinalizado;
$rivalFormDisabled = true;

// Si no hay mensaje en flash, generamos el estado intermedio para la vista
if (empty($flash['mensaje'])) {
    if ($formularioPartidoContrario->getIteracionActual() === 0) {
        if ($miUltimaIteracion === 0) {
            $statusMessage = "Ingresa el resultado de tu partido para iniciar la coordinación.";
            $statusType = "info";
            $myFormDisabled = false;
        } else {
            $statusMessage = "Tu resultado fue enviado. Esperando que el equipo contrario cargue el suyo.";
            $statusType = "info";
        }
    } else {
        // Ambos formularios existen, comparamos solo para resaltar discrepancias
        $statusMessage = "Revisa los campos resaltados e intenta de nuevo. Te quedan {$intentosRestantes} intentos.";
        $statusType = "warning";

        $fieldsToCompare = [
            'goles_local' => ['mine' => $miFormulario->getEquipoLocal()->getGoles(),       'rival' => $formularioPartidoContrario->getEquipoVisitante()->getGoles()],
            'asistencias_local' => ['mine' => $miFormulario->getEquipoLocal()->getAsistencias(),  'rival' => $formularioPartidoContrario->getEquipoVisitante()->getAsistencias()],
            'tarjetas_amarillas_local' => ['mine' => $miFormulario->getEquipoLocal()->getTarjetasAmarilla(), 'rival' => $formularioPartidoContrario->getEquipoVisitante()->getTarjetasAmarilla()],
            'tarjetas_rojas_local' => ['mine' => $miFormulario->getEquipoLocal()->getTarjetasRoja(),    'rival' => $formularioPartidoContrario->getEquipoVisitante()->getTarjetasRoja()],
            'goles_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getGoles(),   'rival' => $formularioPartidoContrario->getEquipoLocal()->getGoles()],
            'asistencias_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getAsistencias(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getAsistencias()],
            'tarjetas_amarillas_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getTarjetasAmarilla(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getTarjetasAmarilla()],
            'tarjetas_rojas_visitante' => ['mine' => $miFormulario->getEquipoVisitante()->getTarjetasRoja(), 'rival' => $formularioPartidoContrario->getEquipoLocal()->getTarjetasRoja()],
        ];
        foreach ($fieldsToCompare as $field => $vals) {
            if ((int)$vals['mine'] !== (int)$vals['rival']) {
                $mismatchedFields[] = $field;
            }
        }
    }
}

$myTeamAcronym    = $miEquipo->getAcronimo();
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
            <?php
            $dl = $partido->getDeadlineFormulario();
            ?>
            <?php if ($dl): ?>
                <p>🕑 Plazo para coordinar resultados hasta: 
                    <strong id="deadline-txt"><?= htmlspecialchars($dl) ?></strong>
                </p>
            <?php endif; ?>
            <div class="alert alert-<?= htmlspecialchars($statusType) ?>">
                <?= htmlspecialchars($statusMessage) ?>
            </div>

            <p class="subtitle">
                Si ambos formularios coinciden, podrás distribuir las estadísticas a tu equipo!
                <?php if (!$partidoFinalizado): ?>
                    <br>Solo te quedan <span class="intentos"><?= htmlspecialchars($intentosRestantes) ?></span> intentos más.
                <?php endif; ?>
            </p>

            <div class="progress-bar-container">
                <?php for ($i = 0; $i < $maxIntentos; $i++): ?>
                    <span class="progress-dot <?= ($i < $miUltimaIteracion) ? 'filled' : '' ?>"></span>
                <?php endfor; ?>
                <span class="progress-text">Intento <?= htmlspecialchars($miUltimaIteracion) ?>/<?= htmlspecialchars($maxIntentos) ?></span>
            </div>

            <div class="forms-wrapper">
                <!-- Formulario propio -->
                <div class="form-column my-form-column active" id="my-form">
                    <strong>Tu formulario (Iteración: <?= htmlspecialchars($miUltimaIteracion + 1) ?>)</strong>
                    <form method="POST" action="/coordinar-resultado?id_partido=<?= htmlspecialchars($id_partido) ?>" class="form-team">
                        <?php
                        // Campos Locales
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

                        // Campos Visitantes
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
                            <button type="submit" name="submit_my_form">Enviar resultado</button>
                        <?php endif; ?>

                    </form>
                </div>

                <!-- Formulario rival -->
                <div class="form-column rival-form-column" id="rival-form">
                    <strong><?= $formularioPartidoContrario->getIteracionActual() == 0 ? 'El rival aún no cargó su primer formulario' : 'Formulario del rival (Iteración: ' . htmlspecialchars($formularioPartidoContrario->getIteracionActual()) . ')' ?></strong>
                    <form class="form-team">
                        <?php
                        // Campos Locales
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

                        // Campos Visitantes
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
                            <button type="button" class="btn-whatsapp" onclick="window.open('https://wa.me/?text=Hola%2C soy%20<?= urlencode($myTeamAcronym) ?>. Por favor, carga el resultado o revisa las diferencias.', '_blank')">Abrir WhatsApp</button>
                        <?php endif; ?>

                    </form>
                </div>
            </div>

            <?php if ($partidoFinalizado): ?>
                <div class="final-buttons-container">
                    <form action="/terminar-partido" method="POST" style="display:inline;" class="btn">
                        <input type="hidden" name="id_partido" value="<?= htmlspecialchars($id_partido) ?>">
                        <input type="hidden" name="id_equipo_rival" value="<?= htmlspecialchars($formularioPartidoContrario->getIdEquipo()) ?>">
                        <button class="btn" type="submit">Terminar partido</button>
                    </form>
                    <button class="btn" type="button" id="btnCalificarDeportividad">Calificar deportividad</button>
                </div>

                <div id="calificacionModal" class="modal">
                    <div class="modal-content">
                        <span class="close-button">&times;</span>
                        <form id="formCalificacion" action="/comentarios" method="POST">
                            <h3>Califica la deportividad del equipo rival</h3>
                            <div class="rating-group" id="ratingGroup">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="rating-icon" data-value="<?= $i ?>">⚽</span>
                                <?php endfor; ?>
                            </div>

                            <input type="hidden" name="deportividad" id="deportividadInput" value="">
                            <input type="hidden" name="id_partido" value="<?= htmlspecialchars($id_partido) ?>">
                            <input type="hidden" name="idEquipoComentado"
                                value="<?= htmlspecialchars($formularioPartidoContrario->getIdEquipo()) ?>">

                            <textarea name="comentario"
                                class="textArea"
                                maxlength="100"
                                placeholder="Deja un comentario... (100 caracteres max.)"></textarea>
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