<?php
$visibleClass = $success ? '' : 'hidden';
?>
<div id="success-modal-overlay" class="modal-overlay <?= $visibleClass ?>" aria-hidden="<?= $success ? 'false' : 'true' ?>">
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="success-modal-title">
    <button id="success-modal-close" class="modal-close" aria-label="Cerrar">&times;</button>

    <div class="modal-header">
      <div class="modal-icon">✓</div>
      <h2 id="success-modal-title" class="modal-title">¡Listo!</h2>
    </div>

    <div class="modal-body" id="success-modal-text"><?= htmlspecialchars($success ?? '', ENT_QUOTES, 'UTF-8') ?></div>

    <div class="modal-actions">
      <button id="success-modal-ok" class="modal-ok-button">Aceptar</button>
    </div>
  </div>
</div>