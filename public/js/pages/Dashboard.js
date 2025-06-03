import ComentarioController from '../controllers/ComentarioController.js';
import DesafioController    from '../controllers/DesafioController.js';

document.addEventListener('DOMContentLoaded', () => {
  // ----------- Inicialización de Comentarios -----------
  const comentarioContainer = document.getElementById('comment-list');
  const filterComentarios = document.getElementById('filtroComentarios');
  const paginationComentarios = document.getElementById('comment-pagination');

  if (!comentarioContainer || !filterComentarios || !paginationComentarios) {
    console.error('Falta algún elemento HTML para Comentarios:', {
      comentarioContainer,
      filterComentarios,
      paginationComentarios
    });
  } else {
    const comentarioController = new ComentarioController({
      comentarioContainer,
      filterSelect:       filterComentarios,
      paginationContainer: paginationComentarios
    });
    comentarioController.init();
  }

  // ----------- Inicialización de Desafíos ------------
  const desafioContainer = document.getElementById('challenge-list');
  const filterDesafios = document.getElementById('filtroDesafios');
  const paginationDesafios  = document.getElementById('desafios-pagination');

  if (!desafioContainer || !filterDesafios || !paginationDesafios) {
    console.error('Falta algún elemento HTML para Desafíos:', {
      desafioContainer,
      filterDesafios,
      paginationDesafios
    });
  } else {
    const desafioController = new DesafioController({
      desafioContainer,
      filterSelect:       filterDesafios,
      paginationContainer: paginationDesafios
    });
    desafioController.init();
  }
});