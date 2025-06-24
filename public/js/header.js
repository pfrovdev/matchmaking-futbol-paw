document.addEventListener('DOMContentLoaded', ()=> {
  const toggle   = document.querySelector('.hamburger-checkbox');
  const sideNav  = document.querySelector('.side-navbar');
  const hamLabel = document.querySelector('.hamburger-menu');

  if (!toggle || !sideNav || !hamLabel) return;

  // Al clickear el checkbox: abrimos/cerramos
  toggle.addEventListener('change', ()=>{
    sideNav.classList.toggle('open', toggle.checked);
  });

  // Evitamos que clicks internos cierren el menú
  hamLabel.addEventListener('click', e => e.stopPropagation());
  toggle.addEventListener('click',   e => e.stopPropagation());
  sideNav.addEventListener('click',  e => e.stopPropagation());

  // Click fuera → cierro y desmarco
  document.addEventListener('click', ()=>{
    toggle.checked = false;
    sideNav.classList.remove('open');
  });
});

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

