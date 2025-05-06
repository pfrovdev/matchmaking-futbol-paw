<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <?php
        require "parts/header-no-account.php";
    ?>
    <main>
        
        <section class="container login-container">
            <h1>Iniciar sesión</h1>
            <p>Ingresá tus credenciales aquí</p>
            <form>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="ej: ejemplo@gmail.com" required>
                
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="ej: contraseña123" required>
                <img src="../icons/close-eye.png" alt="" class="icon-forms">
                
                <button type="submit">Iniciar sesión</button>
            </form>
            <p>
                ¿No tenés cuenta aún? <a href="/create-account">Crear cuenta</a>
            </p>
            <img src="../icons/picture_messi.png" alt="messi picture" class="side-picture">
        </section>
    </main>
    
    <?php
        require "parts/footer.php";
    ?>
    
</body>
</html>