<section class="pagination">
    <?php if ($paginaActual > 1): ?>
        <?php 
        $prevParams = $queryParams;
        $prevParams['page'] = $paginaActual - 1;
        ?>
        <a href="?<?= http_build_query($prevParams) ?>" class="page-link prev">
        « Anterior
        </a>
    <?php endif; ?>

    <span class="page-info">
        Página <?= $paginaActual ?> de <?= $totalPaginas ?>
    </span>

    <?php if ($paginaActual < $totalPaginas): ?>
        <?php 
        $nextParams = $queryParams;
        $nextParams['page'] = $paginaActual + 1;
        ?>
        <a href="?<?= http_build_query($nextParams) ?>" class="page-link next">
        Siguiente »
        </a>
    <?php endif; ?>
</section>