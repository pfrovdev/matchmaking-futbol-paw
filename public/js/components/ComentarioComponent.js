export default class ComentarioComponent {
  constructor(comentarios, container) {
    this.originalComentarios = [...comentarios];
    this.comentarios = comentarios;
    this.container = container;
  }

  updateData(nuevos) {
    this.comentarios = nuevos;
    this.render();
  }

  render() {
    this.container.innerHTML = '';

    if (this.comentarios.length === 0) {
      const li = document.createElement('li');
      li.textContent = 'No hay comentarios para mostrar.';
      this.container.appendChild(li);
      return;
    }

    this.comentarios.forEach(c => {
      const li = document.createElement('li');
      li.classList.add('comment-item');
      li.dataset.deportividad = c.deportividad;
      li.dataset.fechaCreacion = c.fechaCreacion.toISOString();

      // 1) Nombre de equipo
      const strong = document.createElement('strong');
      strong.textContent = c.equipoComentador.nombreEquipo + ' (' +  c.equipoComentador.acronimo + ') ';
      li.appendChild(strong);

      // 2) Calificación (deportividad)
      const pCalif = document.createElement('p');
      pCalif.classList.add('comment-rating');
      pCalif.textContent = 'Calificación: ';

      // Estrellas llenas (⚽ sin clase "empty")
      for (let i = 0; i < c.deportividad; i++) {
        const spanLlena = document.createElement('span');
        spanLlena.classList.add('rating-icon');
        spanLlena.textContent = '⚽';
        pCalif.appendChild(spanLlena);
      }
      // Estrellas vacías (⚽ con clase "empty" para que herede opacity:0.4 y color:grey)
      for (let i = c.deportividad; i < 5; i++) {
        const spanVacia = document.createElement('span');
        spanVacia.classList.add('rating-icon', 'empty');
        spanVacia.textContent = '⚽';
        pCalif.appendChild(spanVacia);
      }

    li.appendChild(pCalif);

      // 3) Texto del comentario (escapando < & > y convirtiendo saltos de línea en <br>)
      const pComent = document.createElement('p');
      const safeText = c.comentario
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\r?\n/g, '<br>');
      pComent.innerHTML = `Comentario: ${safeText}`;
      li.appendChild(pComent);

      // 4) Fecha formateada
      const small = document.createElement('small');
      small.classList.add('comment-date');
      small.textContent = c.getFechaFormateada();
      li.appendChild(small);

      this.container.appendChild(li);
    });
  }
}