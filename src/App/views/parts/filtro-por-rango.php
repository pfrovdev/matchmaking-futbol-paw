
<section class="filters-container">
    <h2>Filtrar por rango</h2>
    <form method="GET" action="">
        <ul>
            <?php foreach ($listLevelsElo as $row):            
                $id = $row['id_nivel_elo'];
                $label = $row['descripcion'];
                $colorInicio = $row['color_inicio'];
                $colorFin = $row['color_fin'];
                $clase = strtolower($row['descripcion_corta']);
                $activo = (isset($_GET['id_nivel_elo']) && $_GET['id_nivel_elo'] == $id) ? ' activo' : '';
                $gradient = "linear-gradient(90deg, $colorInicio, $colorFin)";
            ?>
                <li>
                    <button type="submit"
                        name="id_nivel_elo"
                        value="<?= $id ?>"
                        class="boton-filtro <?= $activo ?>"
                        style="background: <?= htmlspecialchars($gradient) ?>;">
                        <?= $label ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </form>
</section>
