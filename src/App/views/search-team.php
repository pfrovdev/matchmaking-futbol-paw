<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - PAWPrints</title>
    <link rel="stylesheet" href="./css/search-team.css">
</head>
<body>
    <?php
        require "parts/header.php";
    ?>
    <main>
        <header>
            <h1>Buscar desafío</h1>
            <p>Busca rivales, por rango o zona</p>
        </header>

        <section class="grid-layout">
        
            <section class="left-size" aria-labelledby="buscar-nombre">
                <h2 id="buscar-nombre">Buscar por nombre</h2>
                <form>
                    <input type="text" id="nombre-equipo" placeholder="San Silencio de Amargo" />
                </form>

                <ul class="lista-equipos">
                    <li>
                        <article>
                        <header>
                            <h3 class="team-name">Nombre-equipo</h3>
                            <p>Deportividad: ⚽⚽⚽⚽⚽</p>
                            <p>Lema: lema del equipo corto</p>
                        </header>
                        <p>ELO W/L/D: +10, -7, 0</p>
                        <span class="rango">Principiante II</span>
                        <a href="#">Ver perfil del equipo</a>
                        <button type="button">Desafiar</button>
                        </article>
                    </li>
                </ul>
            </section>

            <aside>
                <section aria-labelledby="filtro-rango">
                <h2 id="filtro-rango">Filtrar por rango</h2>
                <ul class="filtros-rango">
                    <li><button type="button">Principiante</button></li>
                    <li><button type="button">Amateur</button></li>
                    <li><button type="button">SemiPro</button></li>
                    <li><button type="button" class="activo">Profesional ✕</button></li>
                </ul>
                </section>

                <section aria-labelledby="ordenar">
                    <h2 id="ordenar">Ordenar por</h2>
                    <form>
                        <label><input type="radio" name="orden" checked> Mayor a menor ELO</label><br>
                        <label><input type="radio" name="orden"> Menor a mayor ELO</label><br>
                        <label><input type="radio" name="orden"> Alfabéticamente</label>
                </form>
                </section>

                <section aria-labelledby="zona-busqueda">
                    <h2 id="zona-busqueda">Zona de búsqueda</h2>
                    <form>
                        <label for="lugar">Buscar lugar</label>
                        <input type="text" id="lugar" placeholder="La canchita, Luján" />

                        <label for="kms">Rango búsqueda (KMS)</label>
                        <input type="number" id="kms" value="1" />
                    </form>
                    <figure>
                        mapita
                    </figure>
                </section>
            </aside>
        </section>
    </main>
    <?php
        require "parts/footer.php";
    ?>
    
</body>
</html>