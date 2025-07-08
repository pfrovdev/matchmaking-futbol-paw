import ComentarioController from '../controllers/ComentarioController.js';
import DesafioController from '../controllers/DesafioController.js';
import PartidoController from '../controllers/PartidoController.js';

document.addEventListener('DOMContentLoaded', () => {

  const isOwner = document.body.dataset.isOwner === 'true';

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
      filterSelect: filterComentarios,
      paginationContainer: paginationComentarios
    });
    comentarioController.init();
  }

  if (!isOwner) {
    // no inicializo desafíos ni partidos
    return;
  }
  
  // ----------- Inicialización de Desafíos ------------
  const desafioContainer = document.getElementById('challenge-list');
  const filterDesafios = document.getElementById('filtroDesafios');
  const paginationDesafios = document.getElementById('desafios-pagination');

  if (!desafioContainer || !filterDesafios || !paginationDesafios) {
    console.error('Falta algún elemento HTML para Desafíos:', {
      desafioContainer,
      filterDesafios,
      paginationDesafios
    });
  } else {
    const desafioController = new DesafioController({
      desafioContainer,
      filterSelect: filterDesafios,
      paginationContainer: paginationDesafios
    });
    desafioController.init();
  }

  // ----------- Inicialización de partidos proximos ------------
  const partidoContainer = document.getElementById('match-list');
  const filterSelect = document.getElementById('filtroProximosPartidos');
  const paginationContainer = document.getElementById('partidos-pagination');

  if (!partidoContainer || !filterSelect || !paginationContainer) {
    console.error('Faltan elementos HTML para Partidos:', {
      partidoContainer,
      filterSelect,
      paginationContainer
    });
  } else {
    const controller = new PartidoController({
      partidoContainer,
      filterSelect,
      paginationContainer
    });
    controller.init();
  }

});