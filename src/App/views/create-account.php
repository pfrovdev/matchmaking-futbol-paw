<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulario para crear una cuenta en PAWPrints">
    <meta name="keywords" content="Registro, Crear cuenta, PAWPrints, Libros">
    <title>Crear Cuenta</title>
    <link rel="stylesheet" href="./css/create-acount.css">
</head>
<body>
    <?php
        session_start();
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors']);
        unset($_SESSION['old']);
    ?>
    <?php
        require "parts/header.php";
    ?>

    <main>       
        <section class="container register-container">
            <head>
            <h1>Crear cuenta</h1>
            <p>Crea tu cuenta y equipo ya!</p>
            </head>
            
            <?php if (!empty($errors)): ?>
                <section class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
            <form action="/register" method="post">              
                <label for="email">Correo electronico *</label>
                <input type="email" id="email" name="email" placeholder="ej: email@gmail.com" required>
                <label for="confirm-email">Confirma tu correo *</label>
                <input type="email" id="confirm-email" name="confirm-email" placeholder="ej: email@gmail.com" required>
                
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="ej: contraseña123" required>
                <img src="../icons/close-eye.png" alt="" class="icon-forms icon-password">
                
                <label for="confirm_password">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="ej: contraseña123" required>
                <img src="../icons/close-eye.png" alt="" class="icon-forms icon-confirm-password">

                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" placeholder="ej: +54 1108111111" required>

                <p>(* Campo obligatorio)</p>
                <button type="submit">Siguiente</button>
            </form>
            <p>¿Ya tenés una cuenta? <a href="/login">Iniciar sesión</a></p>
            <img src="../icons/picture_messi.png" alt="messi picture" class="side-picture">
        </section>
    </main>

    <?php
        require "parts/footer.php";
    ?>
    
</body>
</html>/home/eiarza/universidad/1er_cuatri_2025/PAW_2025/matchmaking-futbol-paw/src/App/views/create-team.php