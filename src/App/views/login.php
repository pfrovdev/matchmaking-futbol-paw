<?php
$errors = $_SESSION['errors'] ?? [];
$email = $_SESSION['email'] ?? '';
$success = $_SESSION['success'] ?? '';

unset($_SESSION['errors']);
unset($_SESSION['email']);
unset($_SESSION['success']);
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

    <main role="main">
        <section class="container register-container" aria-labelledby="login-title">

            <header class="register-header">
                <h1>Iniciar sesión</h1>
                <p>Ingresá tus credenciales aquí</p>
            </header>

            <?php if (!empty($success)): ?>
                <section>
                    <p class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
                </section>
            <?php endif; ?>

            <!-- Errores -->
            <?php if (!empty($errors)): ?>
                <section class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p class="error-text"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <!-- Flex wrapper: formulario | imagen -->
            <section class="register-body">
                <form action="/login" method="POST" class="form-container login-container"
                    aria-describedby="login-title">

                    <label for="email">Correo electrónico *</label>
                    <input type="email" id="email" name="email" placeholder="ej: email@gmail.com"
                        value="<?php echo !empty($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>"
                        required>

                    <label for="password">Contraseña *</label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" placeholder="ej: contraseña123" required>
                        <img src="../icons/close-eye.png" alt="mostrar/ocultar" class="icon-forms icon-password">
                    </div>

                    <button type="submit" class="button">Iniciar sesión</button>

                    <p class="login-link">¿No tenés cuenta aún? <a href="/create-account">Crear cuenta</a></p>
                </form>

                <!-- Imagen lateral -->
                <aside class="image-container">
                    <img src="../icons/picture_messi.png" alt="Imagen ilustrativa de Lionel Messi" class="side-picture">
                </aside>
            </section>

        </section>
    </main>

    <?php require "parts/footer.php"; ?>
</body>

</html>