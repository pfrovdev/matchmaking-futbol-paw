<?php
$visibleClass = $success ? '' : 'hidden';
?>
<aside id="success-modal-overlay" class="modal-overlay <?= $visibleClass ?>"
  aria-hidden="<?= $success ? 'false' : 'true' ?>" aria-labelledby="success-modal-title"
  aria-describedby="success-modal-text" role="dialog">

  <article class="modal-content">
    <header class="modal-header">
      <button id="success-modal-close" class="modal-close" aria-label="Cerrar ventana">&times;</button>
      <span class="modal-icon" aria-hidden="true">✓</span>
      <h2 id="success-modal-title" class="modal-title">¡Listo!</h2>
    </header>

    <section id="success-modal-text" class="modal-body">
      <?= htmlspecialchars($success ?? '', ENT_QUOTES, 'UTF-8') ?>
    </section>

    <footer class="modal-actions">
      <button id="success-modal-ok" class="modal-ok-button">Aceptar</button>
    </footer>
  </article>
</aside>