<?php
$equipoComentador = $dto->getEquipoComentador();
?>
<li>
  <a href="#"> <strong><?= htmlspecialchars($equipoComentador->getNombreEquipo(), ENT_QUOTES, 'UTF-8') ?></strong> </a>
  <p class="comment-rating">
    Calificación:
    <?php
      $score = (int) $equipoComentador->getDeportividad();
      echo str_repeat('<span class="rating-icon">⚽</span>', $score);
      echo str_repeat('<span class="rating-icon empty">○</span>', 5 - $score);
    ?>
  </p>
  <p class="comment-text">
    Comentario: <?= nl2br(htmlspecialchars($dto->getComentario(), ENT_QUOTES, 'UTF-8')) ?>
  </p>
  <small class="comment-date">
    <?= htmlspecialchars($dto->getFechaCreacion(), ENT_QUOTES, 'UTF-8') ?>
  </small>
</li>