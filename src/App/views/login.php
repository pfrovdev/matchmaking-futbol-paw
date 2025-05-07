<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <?php require "parts/header.php"; ?>

    <main>
        <section class="container login-container">
            <h1>Iniciar sesión</h1>
            <p>Ingresá tus credenciales aquí</p>

            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p style="color: red; margin-bottom: 0.5em;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="ej: ejemplo@gmail.com" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="ej: contraseña123" required>
                <img src="../icons/close-eye.png" alt="Mostrar/ocultar contraseña" class="icon-forms">

                <button type="submit">Iniciar sesión</button>
            </form>

            <p>¿No tenés cuenta aún? <a href="/create-account">Crear cuenta</a></p>
            <img src="../icons/picture_messi.png" alt="messi picture" class="side-picture">
        </section>
    </main>

    <?php require "parts/footer.php"; ?>
</body>

</html>