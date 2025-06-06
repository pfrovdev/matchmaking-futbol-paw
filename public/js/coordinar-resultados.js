document.addEventListener('DOMContentLoaded', function() {
    // 1) Verifico si el back me indicó que “resultadosCoinciden” es true
    if (window.resultadosCoinciden === true) {

      const btnEnviar = document.getElementById('btn-enviar');
      const contenedorCalificar = document.getElementById('contenedor-calificar');

      if (btnEnviar && contenedorCalificar) {
        // Oculto el botón original
        btnEnviar.style.display = 'none';

        // Creo el nuevo botón “Calificar deportividad”
        const botonCalificar = document.createElement('button');
        botonCalificar.type = 'button';
        botonCalificar.id = 'btn-calificar';
        botonCalificar.textContent = 'Calificar deportividad';
        botonCalificar.classList.add('btn-calificar');
        contenedorCalificar.appendChild(botonCalificar);
        contenedorCalificar.style.display = 'flex';
        contenedorCalificar.classList.add('contenedor-calificar')
      }

      // 3) Reemplazar el botón “Abrir whatsapp” por 5 estrellas y caja de comentarios
      const btnWhatsapp = document.getElementById('btn-whatsapp');
      const contenedorEstrellas = document.getElementById('contenedor-estrellas');

      if (btnWhatsapp && contenedorEstrellas) {
        // Oculto botón original
        btnWhatsapp.style.display = 'none';
      
        // Creo un <div> para el rating de estrellas
        const ratingDiv = document.createElement('div');
        ratingDiv.id = 'rating-stars';
        ratingDiv.style.display = 'flex';
        ratingDiv.style.alignItems = 'center';
        ratingDiv.style.gap = '0.2rem'; // ajustado para coincidir con margin-right: 0.2rem del CSS
      
        // Genero 5 estrellas (span), sin marcar inicialmente
        for (let i = 1; i <= 5; i++) {
          const star = document.createElement('span');
          star.classList.add('rating-icon', 'empty');   // <span class="rating-icon empty">☆</span>
          star.dataset.value = i;
          star.textContent = '⚽';     // siempre ponemos el carácter “estrella vacía”
      
          // Cuando el usuario haga hover o click, coloreamos hasta esa posición
          star.addEventListener('mouseenter', function() {
            iluminarHasta(i);
          });
          star.addEventListener('mouseleave', function() {
            resetearEstrellas();
          });
          star.addEventListener('click', function() {
            establecerCalificacion(i);
          });
      
          ratingDiv.appendChild(star);
        }
      
        // Agrego un campo oculto para guardar la calificación numérica (1 a 5)
        const inputCalificacion = document.createElement('input');
        inputCalificacion.type = 'hidden';
        inputCalificacion.id = 'input-calificacion';
        inputCalificacion.name = 'calificacion_deportividad'; 
        contenedorEstrellas.appendChild(ratingDiv);
        contenedorEstrellas.appendChild(inputCalificacion);
      
        // 4) Agrego ahora la casilla de comentarios
        const comentarioLabel = document.createElement('label');
        comentarioLabel.setAttribute('for', 'comentarios');
        comentarioLabel.textContent = 'Comentarios:';
        comentarioLabel.style.display = 'block';
        comentarioLabel.style.marginTop = '1rem';
      
        const comentarioTextarea = document.createElement('textarea');
        comentarioTextarea.id = 'comentarios';
        comentarioTextarea.name = 'comentarios';
        comentarioTextarea.rows = 3;
        comentarioTextarea.style.width = '100%';
        comentarioTextarea.classList.add('textArea');
        contenedorEstrellas.appendChild(comentarioLabel);
        contenedorEstrellas.appendChild(comentarioTextarea);
      
        // Finalmente muestro el contenedor
        contenedorEstrellas.style.display = 'block';
      
        // ----- FUNCIONES AUXILIARES para manejar la interacción con estrellas -----
      
        // Mantengo internamente la calificación actual (por defecto 0)
        let calificacionActual = 0;
      
        // Al pasar el mouse por encima (hover), “ilumino” (le quito la clase .empty) hasta cierta estrella
        function iluminarHasta(valor) {
          const estrellas = document.querySelectorAll('#rating-stars .rating-icon');
          estrellas.forEach(star => {
            const v = parseInt(star.dataset.value, 10); 
            if (v <= valor) {
              // Si está dentro del rango hover, la dejamos “llena”
              star.classList.remove('empty');   // quito la clase .empty (hereda color y opacidad por defecto)
            } else {
              // Si está fuera del rango hover, queda vacía
              star.classList.add('empty');
            }
          });
        }
      
        // Cuando quito el hover, vuelvo al estado real según calificacionActual
        function resetearEstrellas() {
          const estrellas = document.querySelectorAll('#rating-stars .rating-icon');
          estrellas.forEach(star => {
            const v = parseInt(star.dataset.value, 10);
            if (v <= calificacionActual) {
              // Si el valor es <= calificacionActual, debo mostrarla llena
              star.classList.remove('empty');
            } else {
              // Si es mayor, sigue vacía
              star.classList.add('empty');
            }
          });
        }
      
        // Al hacer click, establezco calificacionActual, guardo el valor en el input y reseteo
        function establecerCalificacion(valor) {
          calificacionActual = valor;
          document.getElementById('input-calificacion').value = valor;
          resetearEstrellas();
        }
      }
    }
  });