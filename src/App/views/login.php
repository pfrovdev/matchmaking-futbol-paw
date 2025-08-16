<?php
    $errors = $_SESSION['errors'] ?? [];
    $email = $_SESSION['email'] ?? '';
    unset($_SESSION['errors']);
    unset($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="./css/register-login-form.css">
    <script src="./js/helper.js" defer></script>
</head>

<body>
    
    <?php
        $estaLogueado = false;
        require "parts/header.php";
    ?>
    
    <main>
        <section class="container register-container">

            <header class="register-header">
                <h1>Iniciar sesión</h1>
                <p>Ingresá tus credenciales aquí</p>
            </header>

            <!-- Errores -->
            <?php if (!empty($errors)): ?>
                <section class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p class="error-text"><?php echo htmlspecialchars($error,  ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <!-- Flex wrapper: formulario | imagen -->
            <div class="register-body">
                <form action="/login" method="POST" class="form-container">
                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" placeholder="ej: email@gmail.com"
                        value="<?php echo htmlspecialchars($email,  ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label for="password">Contraseña *</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" placeholder="ej: contraseña123" required>
                        <img src="../icons/close-eye.png" alt="mostrar/ocultar" class="icon-forms icon-password">
                    </div>

                    <button type="submit">Iniciar sesión</button>

                    <p class="login-link">¿No tenés cuenta aún? <a href="/create-account">Crear cuenta</a></p>
                </form>

                <!-- Imagen lateral -->
                <div class="image-container">
                    <img src="../icons/picture_messi.png" alt="Messi picture" class="side-picture">
                </div>
            </div>

        </section>
    </main>

    <?php require "parts/footer.php"; ?>
</body>

</html>
