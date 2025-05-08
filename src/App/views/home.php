<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página principal de F5 Futbol Match, Busqueda y organizacion de partidos de futbol">
    <meta name="author" content="F5 Futbol Match">
    <title>Home</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <?php
        require "parts/header-no-account.php";
    ?>
    
    <main>
        <section class="home-container">
          <div class="home-content">
            <img src="../icons/picture_messi.png" alt="messi picture">
            <h2>¿Tenés equipo? Nosotros te conseguimos rival</h2>
            <p>
                Armá tu equipo con amigos, publicá un partido y encontrá rivales reales en tu zona. 
                Organizá tus partidos, mejorá tu ranking y construí una reputación como jugador o como equipo.
            </p>
            <div class="button-group">
              <a href="create-account">Registrar mi equipo</a>
              <a href="login">Iniciar sesión</a>
            </div>
          </div>
        </section>
      <section class="benefits-section">
        <header class="benefits-header">
          <h2>¿Por qué elegir <span>F5 Futbol Match</span>?</h2>
        </header>

        <ul class="benefits-list">
          <li class="benefit-item">
            <img src="./icons/magnifying-glass.png" alt="Icono de búsqueda de rivales">
            <h3>Encontrá rivales fácilmente</h3>
            <p>
              Publicá desafíos o buscá equipos disponibles para jugar en tu zona. 
              Filtrá por nivel, horario o disponibilidad y confirmá el partido en segundos.
            </p>
          </li>

          <li class="benefit-item">
            <img src="./icons/organization-team.png" alt="Icono de organización de equipo">
            <h3>Organizá tu equipo</h3>
            <p>
              Gestioná planteles, posiciones y roles desde un solo lugar. 
              Asigná un capitán, aceptá nuevos jugadores y coordiná partidos en tiempo real.
            </p>
          </li>

          <li class="benefit-item">
            <img src="./icons/statistics-reports.png" alt="Icono de ranking de equipos">
            <h3>Subí en el ranking y mejorá tu reputación</h3>
            <p>
              Cada partido suma: goles, resultados, rendimiento y fair play. 
              Tu perfil y el de tu equipo crecen con cada desafío aceptado.
            </p>
          </li>
        </ul>
      </section>
    </main>
    <?php
        require "parts/footer.php";
    ?>

</body>
</html>


