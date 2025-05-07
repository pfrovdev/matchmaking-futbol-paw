<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pagina principal del equipo de futbol del usuario">
    <title>Crear Equipo</title>
    <link rel="stylesheet" href="http://127.0.0.1:5500/public/css/dashboard.css">
<body>
  <?php
          require "parts/header.php";
      ?>
    <main>
    <div class="dashboard-container">
    <!-- GRID PRINCIPAL -->
    <div class="dashboard-grid">
      <!-- Columna Izquierda -->
      <section class="col-left">
        <!-- Card 1: Perfil -->
        <div class="card perfil-card">
          <div class="perfil-foto">
          <!--TODO agregar todo lo que seria la verificacion
          tipo si existe la imagen te la muestra, sino te manda lo de aca abajo 
          con el background de defaultTeamIcon.png-->
            Arrastra-soltar la foto de tu equipo aquí<br>
            <button class="btn-link">Cargar un documento</button>
          </div>
          <div class="perfil-info">
            <h2>Nombre equipo (PSG)</h2>
            <p class="lema">Lema corto de equipo</p>
            <div class="deportividad">
              Deportividad:
              <span class="icon-star"></span>
              <span class="icon-star"></span>
              <span class="icon-star"></span>
              <span class="icon-star"></span>
              <span class="icon-star"></span>
            </div>
            <p>Género: Masculino</p>
            <div class="elo-bar">
              <span class="label">Amateur</span>
              <div class="bar-bg">
                <div class="bar-fill" style="width:40%"></div>
              </div>
              <div class="elo-values">
                <span>Elo: 1265</span> / <span>1300</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: Último partido -->
        <div class="card last-match-card">
            <?php
              $match = [
                   'eloChange' => +20,                       // número positivo o negativo
                   'matchUrl'  => '#',                       // enlace a detalle
                   'date'      => '21/02/2025',
                   'home'      => [
                     'abbr'      => 'CABJ',
                     'name'      => 'Boca Juniors',
                     'logo'      => 'defaultTeamIcon.png',              // ruta relativa a img/
                     'tarjetas'     => ['yellow'=>1,'red'=>1], // cantidad de tarjetas
                   ],
                   'away'      => [
                     'abbr'      => 'CASLA',
                     'name'      => 'San Lorenzo',
                     'logo'      => 'defaultTeamIcon.png',
                     'tarjetas'     => ['yellow'=>1,'red'=>1],
                   ],
                   'score'     => '4-0',
                  ];
              require "parts/tarjeta-historial.php";
            ?>
        </div>

        <!-- Card 3: Desafíos recibidos -->
        <div class="card challenges-card">
          <h3>Últimos desafíos recibidos</h3>
          <ul class="challenge-list">
            <?php for($i=1;$i<=3;$i++): ?>
              <li>
              <?php
                $challenge = [
                  'name'       => 'Nombre-equipo',
                  'level'      => 'Principiante II',
                  'icons'      => 5,
                  'motto'      => 'Lema del equipo corto',
                  'elo'        => ['wins'=>10,'losses'=>7,'draws'=>0],
                  'record'     => '10-7-2',
                  'profileUrl' => '#',
                ];
                require "parts/tarjeta-desafio.php";
                ?>
              </li>
            <?php endfor; ?>
          </ul>
          <div class="pagination">1 2 3 4 5 6</div>
        </div>
      </section>

      <!-- Columna Derecha -->
      <aside class="col-right">
        <!-- Card 4: Estadísticas -->
        <div class="card stats-card">
          <h3>Estadísticas</h3>
          <dl>
            <dt>G/P:</dt><dd>1.2</dd>
            <dt>A/P:</dt><dd>1.2</dd>
            <dt>%G/A:</dt><dd>50%</dd>
          </dl>
          <h4>Coleadores</h4>
          <ol>
            <li>Nombre Jugador - 50</li>
            <li>Nombre Jugador - 40</li>
            <li>Nombre Jugador - 30</li>
          </ol>
          <h4>Asistidores</h4>
          <ol>
            <li>Nombre Jugador - 20</li>
            <li>Nombre Jugador - 15</li>
            <li>Nombre Jugador - 10</li>
          </ol>
        </div>

        <!-- Card 5: Comentarios -->
        <div class="card comments-card">
          <h3>Comentarios</h3>
          <ul class="comment-list">
            <?php for($i=1;$i<=3;$i++): ?>
              <li>
                <strong>Nombre-equipo</strong>
                <p>Calificación: ★★★★☆</p>
                <p>comentario: Me siento solo…</p>
              </li>
            <?php endfor; ?>
          </ul>
          <div class="pagination">1 2 3 4 5 6</div>
        </div>
      </aside>
    </div>

    <!-- SECCIÓN INFERIOR: Proximos partidos full-width -->
    <section class="next-matches">
      <h3>Próximos partidos</h3>
      <ul class="match-list">
        <?php for($i=1;$i<=2;$i++): ?>
          <li class="match-item">
            <div class="match-info">
              <strong>Nombre-equipo</strong>
              <p>Principiante II</p>
            </div>
            <div class="match-actions">
              <button class="btn-secondary small">Abrir wapp</button>
              <button class="btn-primary small">Coordinar resultado</button>
              <button class="btn-danger small">Cancelar</button>
            </div>
          </li>
        <?php endfor; ?>
      </ul>
    </section>
  </main>
    <?php
        require "parts/footer.php";
    ?>
</body>
</html>
