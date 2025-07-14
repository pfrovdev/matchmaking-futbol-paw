<?php

/**
 * Partial para renderizar los campos de un equipo en el formulario de coordinación.
 *
 * @param string $teamAcronym El acrónimo del equipo (ej. "UV", "FCH").
 * @param string $prefixName El prefijo para los nombres e IDs de los inputs (ej. "local", "visitante").
 * @param array $values Array asociativo con 'goles', 'asistencias', 'amarillas', 'rojas'.
 * @param bool $disabled Indica si los campos deben estar deshabilitados o transformados a texto estático.
 * @param array $mismatchedFieldsFiltered Array de nombres de campos que no coinciden (ej. ['goles_local', 'tarjetas_amarillas_local']).
 * @param bool $primeraIteracion Indica si es la primera iteración del formulario.
 * @param bool $primeraIteracionRival Indica si es la primera iteración del rival.
 */
?>
<fieldset class="form-team">
    <legend class="team-name <?= htmlspecialchars($prefixName); ?>">
        <?= htmlspecialchars($teamAcronym); ?>
    </legend>

    <div class="field">
        <label for="goles_<?= htmlspecialchars($prefixName); ?>">
            <img src="icons/goles.png" alt="Icono Goles" class="icon" />
            Goles
            <?php if (! $primeraIteracion && ! $primeraIteracionRival): ?>
                <span class="field-status-icon <?= in_array('goles_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatch' : 'match'; ?>"></span>
            <?php endif; ?> </label>
        <?php if ($disabled): ?>
            <span class="static-value <?= in_array('goles_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>">
                <?= htmlspecialchars($values['goles']); ?>
            </span>
        <?php else: ?>
            <input
                type="number"
                id="goles_<?= htmlspecialchars($prefixName); ?>"
                name="goles_<?= htmlspecialchars($prefixName); ?>"
                min="0"
                value="<?= htmlspecialchars($values['goles']); ?>"
                class="<?= in_array('goles_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>" />
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="asistencias_<?= htmlspecialchars($prefixName); ?>">
            <img src="icons/asistencias.png" alt="Icono Asistencias" class="icon" />
            Asistencias
            <?php if (! $primeraIteracion && ! $primeraIteracionRival): ?>
                <span class="field-status-icon <?= in_array('asistencias_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatch' : 'match'; ?>"></span>
            <?php endif; ?>
        </label>
        <?php if ($disabled): ?>
            <span class="static-value <?= in_array('asistencias_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>">
                <?= htmlspecialchars($values['asistencias']); ?>
            </span>
        <?php else: ?>
            <input
                type="number"
                id="asistencias_<?= htmlspecialchars($prefixName); ?>"
                name="asistencias_<?= htmlspecialchars($prefixName); ?>"
                min="0"
                value="<?= htmlspecialchars($values['asistencias']); ?>"
                class="<?= in_array('asistencias_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>" />
        <?php endif; ?>
    </div>

    <div class="field tarjetas">
        <span class="field-title">
            Tarjetas
        </span>
        <div class="cards-group">
            <div class="card-field">
                <img src="icons/tarjetaAmarilla.png" alt="Tarjeta Amarilla" class="icon-card" />
                <?php if ($disabled): ?>
                    <span class="static-value <?= in_array('tarjetas_amarillas_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>">
                        <?= htmlspecialchars($values['amarillas']); ?>
                    </span>
                <?php else: ?>
                    <input
                        type="number"
                        name="tarjetas_amarillas_<?= htmlspecialchars($prefixName); ?>"
                        min="0"
                        value="<?= htmlspecialchars($values['amarillas']); ?>"
                        class="<?= in_array('tarjetas_amarillas_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>" />
                <?php endif; ?>
                <?php if (! $primeraIteracion && ! $primeraIteracionRival): ?>
                    <span class="field-status-icon <?= in_array('tarjetas_amarillas_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatch' : 'match'; ?>"></span>
                <?php endif; ?>
            </div>
            <div class="card-field">
                <img src="icons/tarjetaRoja.png" alt="Tarjeta Roja" class="icon-card" />
                <?php if ($disabled): ?>
                    <span class="static-value <?= in_array('tarjetas_rojas_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>">
                        <?= htmlspecialchars($values['rojas']); ?>
                    </span>
                <?php else: ?>
                    <input
                        type="number"
                        name="tarjetas_rojas_<?= htmlspecialchars($prefixName); ?>"
                        min="0"
                        value="<?= htmlspecialchars($values['rojas']); ?>"
                        class="<?= in_array('tarjetas_rojas_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatched-field' : ''; ?>" />
                <?php endif; ?>
                <?php if (! $primeraIteracion && ! $primeraIteracionRival): ?>
                    <span class="field-status-icon <?= in_array('tarjetas_rojas_' . $prefixName, $mismatchedFieldsFiltered) ? 'mismatch' : 'match'; ?>"></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</fieldset>