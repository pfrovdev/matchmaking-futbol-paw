export default class TutorialCoordinarResultadoComponent {
    constructor(slides, options = {}) {
        this.slides = slides;
        this.currentSlideIndex = 0;
        this.options = {
            tutorialKey: 'coordinarTutorialSeen',
            closeButtonText: 'X',
            prevButtonText: '<',
            nextButtonText: '>',
            lastSlideButtonText: 'Finalizar',
            actionButtonInitialText: 'Entendido',
            ...options
        };

        this.overlay = null;
        this.elements = {};
        this.init();
    }

    init() {
        // Verificar si el tutorial ya se vio
        if (!localStorage.getItem(this.options.tutorialKey)) {
            this.createAndAppendOverlay();
            this.attachEvents();
            this.updateCarousel();
            localStorage.setItem(this.options.tutorialKey, 'true');
        }
    }

    createAndAppendOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.className = 'tutorial-overlay';

        const modal = document.createElement('div');
        modal.className = 'tutorial-modal';

        // Botón de cerrar
        const closeButton = document.createElement('button');
        closeButton.id = 'tutorial-close';
        closeButton.className = 'btn-estilo tutorial-close-button';
        closeButton.textContent = this.options.closeButtonText;
        modal.appendChild(closeButton);

        // Contenedor principal del carrusel
        const carousel = document.createElement('div');
        carousel.className = 'tutorial-carousel';

        // Contenido de la diapositiva (imagen, título, texto)
        const carouselContent = document.createElement('div');
        carouselContent.className = 'carousel-content';

        const title = document.createElement('h2');
        title.id = 'tutorial-title';
        carouselContent.appendChild(title);

        const image = document.createElement('img');
        image.id = 'tutorial-image';
        image.alt = 'Tutorial Image';
        carouselContent.appendChild(image);

        const text = document.createElement('p');
        text.id = 'tutorial-text';
        carouselContent.appendChild(text);

        carousel.appendChild(carouselContent);

        // Navegación del carrusel (botones Anterior, Siguiente y puntos de paginación)
        const carouselNavigation = document.createElement('div');
        carouselNavigation.className = 'carousel-navigation';

        const prevButton = document.createElement('button');
        prevButton.id = 'prev-slide';
        prevButton.className = 'button btn-estilo tutorial-nav-btn';
        prevButton.innerHTML = this.options.prevButtonText;
        carouselNavigation.appendChild(prevButton);

        const paginationDotsContainer = document.createElement('div');
        paginationDotsContainer.id = 'pagination-dots';
        paginationDotsContainer.className = 'pagination-dots';
        carouselNavigation.appendChild(paginationDotsContainer);

        const nextButton = document.createElement('button');
        nextButton.id = 'next-slide';
        nextButton.className = 'button btn-estilo tutorial-nav-btn';
        nextButton.innerHTML = this.options.nextButtonText;
        carouselNavigation.appendChild(nextButton);

        carousel.appendChild(carouselNavigation);
        modal.appendChild(carousel);

        // Botón de acción final (Entendido / Comenzar)
        const actionButton = document.createElement('button');
        actionButton.id = 'tutorial-action-button';
        actionButton.className = 'button btn-estilo tutorial-final-button';
        actionButton.textContent = this.options.actionButtonInitialText;
        modal.appendChild(actionButton);

        this.overlay.appendChild(modal);
        document.body.appendChild(this.overlay);

        // Almacenar referencias a los elementos DOM para acceso rápido
        this.elements = {
            title: title,
            text: text,
            image: image,
            prevButton: prevButton,
            nextButton: nextButton,
            paginationDotsContainer: paginationDotsContainer,
            actionButton: actionButton,
            closeButton: closeButton
        };
    }

    attachEvents() {
        this.elements.prevButton.addEventListener('click', () => this.goToPrevSlide());
        this.elements.nextButton.addEventListener('click', () => this.goToNextSlide());
        this.elements.actionButton.addEventListener('click', () => this.closeTutorial());
        this.elements.closeButton.addEventListener('click', () => this.closeTutorial());
    }

    updateCarousel() {
        const slide = this.slides[this.currentSlideIndex];
        this.elements.title.textContent = slide.title;
        this.elements.text.textContent = slide.text;
        this.elements.image.src = slide.image;

        // Visibilidad y texto de los botones de navegación
        this.elements.prevButton.style.display = this.currentSlideIndex === 0 ? 'none' : 'block';
        this.elements.nextButton.innerHTML = this.currentSlideIndex === this.slides.length - 1 ? this.options.lastSlideButtonText : this.options.nextButtonText;

        // El botón de acción principal ("Entendido" / "Comenzar")
        if (this.currentSlideIndex === 0) {
            this.elements.actionButton.textContent = this.options.actionButtonInitialText;
            this.elements.actionButton.style.display = 'block';
        } else {
            this.elements.actionButton.style.display = 'none';
        }

        // Actualizar los puntos de paginación
        this.elements.paginationDotsContainer.innerHTML = '';
        this.slides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (index === this.currentSlideIndex) {
                dot.classList.add('active');
            }
            dot.addEventListener('click', () => {
                this.currentSlideIndex = index;
                this.updateCarousel();
            });
            this.elements.paginationDotsContainer.appendChild(dot);
        });
    }

    goToPrevSlide() {
        if (this.currentSlideIndex > 0) {
            this.currentSlideIndex--;
            this.updateCarousel();
        }
    }

    goToNextSlide() {
        if (this.currentSlideIndex < this.slides.length - 1) {
            this.currentSlideIndex++;
            this.updateCarousel();
        } else {
            this.closeTutorial();
        }
    }

    closeTutorial() {
        if (this.overlay && this.overlay.parentNode) {
            this.overlay.parentNode.removeChild(this.overlay);
        }
    }
}