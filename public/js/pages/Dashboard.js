import ComentarioController from '../controllers/ComentarioController.js';
import DesafioController from '../controllers/DesafioController.js';
import PartidoController from '../controllers/PartidoController.js';
import HistorialController from '../controllers/HistorialController.js';

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

  // ------------ Inicialización de Historial de Partidos ------------
  const historyContainer = document.getElementById('history-list');
  const filterHistorial = document.getElementById('filtroHistorial');
  const paginationHistorial = document.getElementById('history-pagination');
  const equipoId = document.body.dataset.profileId;

  if (historyContainer && filterHistorial && paginationHistorial) {
    const hc = new HistorialController({
      historyContainer,
      filterSelect: filterHistorial,
      paginationContainer: paginationHistorial,
      equipoId
    });
    hc.init();
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
 // ----------- Lógica del Modal de edición ------------
 const modalTriggers = document.querySelectorAll('.open-edit-modal, .perfil-foto .btn-link');
 const modal = document.getElementById('edit-team-modal');
 const closeBtn = document.getElementById('close-modal');
 const form = modal?.querySelector('form');
 const urlInput = form?.querySelector('#team-url');
 const urlError = form.querySelector('#url-error');

 modalTriggers.forEach(btn => {
   btn.addEventListener('click', () => {
     modal.classList.remove('hidden');
     document.body.style.overflow = 'hidden'; // evitar scroll atrás
   });
 });

 closeBtn?.addEventListener('click', () => {
   modal.classList.add('hidden');
   document.body.style.overflow = '';
   urlError.style.display = 'none';
 });

 modal?.addEventListener('click', e => {
   if (e.target === modal) {
     modal.classList.add('hidden');
     document.body.style.overflow = '';
     urlError.style.display = 'none';
   }
 });

 if (form && urlInput && urlError) {
  const acronymInput = form.querySelector('#team-acronym');
  const acronymError = form.querySelector('#acronym-error');

  form.addEventListener('submit', e => {
    let hayError = false;

    // — Validación del acrónimo —
    const acr = acronymInput.value.trim();
    if (acr.length > 3) {
      hayError = true;
      acronymError.style.display = 'block';
      acronymError.textContent = 'El acrónimo no puede exceder 3 caracteres.';
      acronymInput.focus();
    } else {
      acronymError.style.display = 'none';
    }

    // — Validación de la URL (solo si no hubo error de acrónimo) —
    if (!hayError) {
      const url = urlInput.value.trim();
      if (url !== '') {
        const validPattern = /^https?:\/\/.+/;
        if (url.length > 255 || !validPattern.test(url)) {
          hayError = true;
          urlError.style.display = 'block';
          urlError.textContent = url.length > 255
            ? 'La URL no puede superar 255 caracteres.'
            : 'La URL debe empezar con http:// o https://';
          urlInput.focus();
        } else {
          urlError.style.display = 'none';
        }
      } else {
        urlError.style.display = 'none';
      }
    }

    if (hayError) {
      e.preventDefault();
    }
  });
 }
});