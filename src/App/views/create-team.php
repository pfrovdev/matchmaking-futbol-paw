<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulario para crear una cuenta en PAWPrints">
    <meta name="keywords" content="Registro, Crear cuenta, PAWPrints, Libros">
    <title>Crear Cuenta - PAWPrints</title>
    <link rel="stylesheet" href="./css/create-acount.css">
</head>
<body>
    <?php
        session_start();
        $errors = $_SESSION['errors'] ?? [];
        $equipo_temp = $_SESSION['equipo_temp'] ?? [];
    ?>
    <?php
        require "parts/header.php";
    ?>

    <main>       
        <section class="container register-container">
            <header>
                <h1>Crear equipo</h1>
                <p>Crea tu equipo e invitá a tus amigos!</p>
            </header>

            <?php if (!empty($errors)): ?>
                <section class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <form action="/register-team" method="post">              
                <label for="team-name">Nombre completo del equipo *</label>
                <input type="text" id="team-name" name="team-name" placeholder="Sacachispas F.C" required>

                <label for="team-acronym">Acrónimo del equipo*</label>
                <input type="text" id="team-acronym" name="team-acronym" placeholder="SFC" required>
                
                <fieldset class="form-group">
                    <legend>Tipo de equipo *</legend>

                        <input type="radio" id="masculino" name="team-type" value="Masculino" checked required>
                        <label for="masculino">Masculino</label>

                        <input type="radio" id="femenino" name="team-type" value="Femenino" required>
                        <label for="femenino">Femenino</label>

                        <input type="radio" id="mixto" name="team-type" value="Mixto" required>
                        <label for="mixto">Mixto</label>

                </fieldset>
                
                
                <label for="team-zone">Zona del equipo *</label>
                <input type="text" id="team-zone" name="team-zone" placeholder="Buscá en el mapa..." required>
                
                <section aria-label="Mapa de ubicación del equipo">
                    <div id="map">
                        <!-- Acá deberíamos poner el mapita -->
                    </div>
                </section>

                <label for="team-motto">Lema del equipo</label>
                <input type="text" id="team-motto" name="team-motto" placeholder="Lema del equipo">

                <p>(* Campo obligatorio) ** Tu teléfono será utilizado para la coordinación entre equipos</p>
                <button type="submit">Crear equipo</button>
            </form>
            <img src="../icons/picture_messi.png" alt="messi picture" class="side-picture">
        </section>
    </main>

    <?php
        require "parts/footer.php";
    ?>
    
</body>
</html>