import ComentarioController from '../controllers/ComentarioController.js';

// Esperar a que termine de cargar el DOM para instanciar todo
document.addEventListener('DOMContentLoaded', () => {
  const comentarioContainer = document.getElementById('comment-list');
  const filterSelect = document.getElementById('filtroComentarios');
  const paginationContainer = document.getElementById('comment-pagination');

  const controller = new ComentarioController({
    comentarioContainer,
    filterSelect,
    paginationContainer
  });

  controller.init();
});