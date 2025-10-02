import Tutorial from '../components/Tutorial.js';
import steps from '../data/SearchTeamsTutorialSlides.js';

// Define el punto de corte (breakpoint) de mobile. Ajusta este valor si tu CSS usa otro.
const MOBILE_BREAKPOINT = 768; 

/**
 * Determina si la vista actual es "mobile".
 * @returns {boolean} True si el ancho de la ventana es menor al breakpoint.
 */
const isMobileView = () => window.innerWidth < MOBILE_BREAKPOINT;

/**
 * Filtra los pasos del tutorial según el contexto (mobile o desktop).
 * Requiere que los pasos tengan la propiedad 'context: "mobile"' o 'context: "desktop"'.
 * @param {Array} originalSteps - La lista original de pasos.
 * @returns {Array} Los pasos filtrados.
 */
const filterStepsByContext = (originalSteps) => {
    const context = isMobileView() ? 'mobile' : 'desktop';

    return originalSteps.filter(step => {
        // Incluye pasos sin contexto específico (pasos generales, ej: intro, lista, tarjeta)
        if (!step.context) {
            return true;
        }
        // Incluye pasos que coinciden con el contexto actual
        return step.context === context;
    });
};


document.addEventListener("DOMContentLoaded", () => {
    const filtersBtn = document.getElementById("openFiltersBtn");
    const orderBtn = document.getElementById("openOrderBtn");
    const filtersModal = document.getElementById("filtersModal");
    const orderModal = document.getElementById("orderModal");
    const closeButtons = document.querySelectorAll(".close-modal");
    const overlay = document.getElementById("modalOverlayInfo");

    const openModal = (modal) => {
        modal.classList.add("show");
        overlay.classList.add("show");
    };

    const closeModal = () => {
        filtersModal?.classList.remove("show");
        orderModal?.classList.remove("show");
        overlay.classList.remove("show");
    };

    const closeFiltersModal = () => {
        const filtersModal = document.getElementById("filtersModal");
        const overlay = document.getElementById("modalOverlayInfo");
        const orderModal = document.getElementById("orderModal");

        filtersModal?.classList.remove("show");
        orderModal?.classList.remove("show");
        overlay?.classList.remove("show");
    };

    filtersBtn?.addEventListener("click", () => openModal(filtersModal));
    orderBtn?.addEventListener("click", () => openModal(orderModal));

    closeButtons.forEach((btn) => btn.addEventListener("click", closeModal));
    overlay?.addEventListener("click", closeModal);

    [filtersModal, orderModal].forEach((modal) => {
        modal?.addEventListener("click", (e) => {
            if (e.target === modal) closeModal();
        });
    });

    window.closeFiltersModal = closeFiltersModal;


    // Inicializa el tutorial
    const filteredSteps = filterStepsByContext(steps);

    const tutorial = new Tutorial({
        key: 'SearchTeams',
        steps: filteredSteps 
    });
    
    tutorial.init();

});