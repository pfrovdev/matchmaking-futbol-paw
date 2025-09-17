import TutorialCoordinarResultadoComponent from '../components/TutorialCoordinarResultadoComponent.js';
import RatingComponent from '../components/RatingComponent.js';
import ModalComponent from '../components/ModalComponent.js';
import TabNavComponent from '../components/TabNavComponent.js';
import tutorialSlides from '../data/TutorialSlides.js';

document.addEventListener('DOMContentLoaded', () => {
    // Tutorial
    new TutorialCoordinarResultadoComponent(tutorialSlides, { tutorialKey: 'coordinarTutorialSeen' });

    // Pestañas responsive
    new TabNavComponent({
        tabButtonsSelector: '.tab-button',
        sectionsSelector: '#my-form, #rival-form'
    });

    // Botón WhatsApp
    document.querySelectorAll('.btn-whatsapp[data-wa-url]').forEach(btn => {
        btn.addEventListener('click', () => window.open(btn.dataset.waUrl, '_blank'));
    });

    // Calificación de deportividad
    const calificarBtn = document.getElementById('btnCalificarDeportividad');
    const modalSelector = '#calificacionModal';
    if (calificarBtn && document.querySelector(modalSelector)) {
        new RatingComponent({ containerSelector: '#ratingGroup', inputName: 'deportividad' });
        new ModalComponent({ triggerSelector: '#btnCalificarDeportividad', modalSelector });
    }
    
    const dlElem = document.getElementById('deadline-txt');
    if (dlElem) {
    const submitBtn = document.querySelector('form button[type=submit]');
    const deadline = new Date(dlElem.textContent).getTime();

    const tick = setInterval(() => {
        if (Date.now() >= deadline) {
        clearInterval(tick);
        if (submitBtn) {
            submitBtn.textContent = 'Plazo vencido';
        }
        alert('Se superó el plazo de coordinación; se tomó el último resultado cargado.');
        }
    }, 1000);
    }
});
