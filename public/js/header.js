
  document.addEventListener('DOMContentLoaded', function() {
    // 1) Referencia al contenedor y al botón
    const contenedor = document.querySelector('.header-my-account');
    const boton = contenedor.querySelector('button');

    // 2) Al hacer clic en el botón, alternamos la clase "show" en el contenedor
    boton.addEventListener('click', function(e) {
      e.stopPropagation(); // evitamos que el clic pase al document
      contenedor.classList.toggle('show');
    });

    // 3) Si se hace clic en cualquier parte fuera del .header-my-account, cerramos el menú
    document.addEventListener('click', function() {
      contenedor.classList.remove('show');
    });
  });
