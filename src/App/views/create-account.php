<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulario para crear una cuenta en F5 Futbol Match">
    <meta name="keywords" content="Registro, Crear cuenta, F5 Futbol Match">
    <title>Crear Cuenta</title>
    <link rel="stylesheet" href="./css/register-login-form.css">
    <script src="./js/helper.js"></script>
</head>

<body>
    <?php
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors']);
        unset($_SESSION['old']);
        $estaLogueado = false;
        require "parts/header.php";
    ?>
    
    <main>
        <section class="container register-container">

            <!-- Header interno -->
            <header class="register-header">
                <h1>Crear cuenta</h1>
                <p>Crea tu cuenta y equipo ya!</p>
            </header>

            <!-- Errores -->
            <?php if (!empty($errors)): ?>
                <section class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p class="error-text"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <!-- Flex wrapper -->
            <div class="register-body">
                <!-- Formulario -->
                <form action="/register" method="post" class="form-container">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" placeholder="ej: email@gmail.com"
                        value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label for="confirm-email">Confirma tu correo *</label>
                    <input type="email" id="confirm-email" name="confirm-email" placeholder="ej: email@gmail.com"
                        value="<?php echo htmlspecialchars($old['confirm-email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required>

                    <label for="password">Contraseña *</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" placeholder="ej: contraseña123" required>
                        <img src="../icons/close-eye.png" alt="mostrar/ocultar" class="icon-forms icon-password">
                    </div>

                    <label for="confirm_password">Confirmar contraseña *</label>
                    <div class="input-with-icon">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="ej: contraseña123" required>
                        <img src="../icons/close-eye.png" alt="mostrar/ocultar"
                            class="icon-forms icon-confirm-password">
                    </div>

                    <label for="telefono">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="ej: +54 1108111111"
                        value="<?php echo htmlspecialchars($old['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

                    <p class="mandatory-note">(* Campo obligatorio)</p>
                    <button type="submit">Siguiente</button>

                    <p class="login-link">¿Ya tenés una cuenta? <a href="/login">Iniciar sesión</a></p>
                </form>

                <!-- Imagen lateral -->
                <div class="image-container">
                    <img src="../icons/picture_messi.png" alt="messi picture" class="side-picture">
                </div>
            </div>

        </section>
    </main>

</body>

</html>