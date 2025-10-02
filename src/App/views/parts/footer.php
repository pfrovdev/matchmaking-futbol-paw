<footer id="main-footer">
    <section>
        <h2>Sobre nosotros</h2>
        <p>
            Somos una plataforma dedicada a la organización de partidos de fútbol 5.
            Nuestra app permite crear equipos, encontrar rivales, coordinar partidos y registrar
            resultados de manera sencilla. Además, fomentamos el fair play con un sistema de
            calificaciones, estadísticas y ranking dinámico que premia el esfuerzo y la deportividad.
        </p>

    </section>

    <section>
        <h2>Redes sociales</h2>
        <ul class="social-media">
            <li><a href="#"><img src="../icons/instagram-icon.png" alt="Instagram"></a></li>
            <li><a href="#"><img src="../icons/facebook-icon.png" alt="Facebook"></a></li>
            <li><a href="#"><img src="../icons/youtube-icon.png" alt="YouTube"></a></li>
        </ul>
    </section>

    <section>
        <h2>Contacto</h2>
        <address>
            <ul>
                <li>Teléfono: <a href="tel:+123456789">+1 234 567 89</a></li>
                <li>Email: <a href=<?php echo ("mailto:" . getenv("MAIL_USERNAME")) ?>> <?php echo (getenv("MAIL_USERNAME")) ?></a></li>
            </ul>
        </address>
    </section>

    <p><small>&copy; 2025 Matchmaking Futbol 5. Todos los derechos reservados.</small></p>

</footer>