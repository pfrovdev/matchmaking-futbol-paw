import TutorialCoordinarResultadoComponent from '../components/TutorialCoordinarResultadoComponent.js';

document.addEventListener('DOMContentLoaded', () => {
    // Calificación deportividad
    const ratingGroups = document.querySelectorAll('.rating-group');
    ratingGroups.forEach(group => {
        const ratingIcons = group.querySelectorAll('.rating-icon');
        ratingIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                const value = parseInt(icon.dataset.value, 10);
                ratingIcons.forEach(star => {
                    if (parseInt(star.dataset.value, 10) <= value) {
                        star.classList.remove('empty');
                    } else {
                        star.classList.add('empty');
                    }
                });
                console.log(`Calificación seleccionada: ${value} estrellas`);
            });
        });
    });

    // Definición de las diapositivas del tutorial
    const tutorialSlides = [
        {
            title: "Bienvenido a Coordinación de Resultados",
            text: "Aquí podrás cargar las estadísticas de tu equipo y coordinarlas con las del rival. ¡Es muy sencillo!",
            image: "../../img/tutorial/f5match-icon.png"
        },
        {
            title: "1. Tu Formulario: Carga tus estadísticas",
            text: "Ingresa la cantidad de Goles, Asistencias y Tarjetas (Amarillas y Rojas) de tu equipo y el del rival en los campos correspondientes. Puedes usar los botones de incremento/decremento o escribir directamente.",
            image: "../../img/tutorial/tu-formulario.gif"
        },
        {
            title: "2. Formulario del Rival: Revisa las coincidencias",
            text: "La sección 'Formulario del rival' muestra lo que ellos cargaron. Los campos con bordes rojos indican que no coinciden con tus valores. ¡Ajusta los tuyos hasta que todo esté verde!",
            image: "../../img/tutorial/rival-formulario-desincronizado.png"
        },
        {
            title: "3. Alterna Formularios (en móvil)",
            text: "Si estás en un dispositivo móvil, utiliza las pestañas en la parte superior para alternar fácilmente entre 'Tu formulario' y el 'Formulario del rival'.",
            image: "../../img/tutorial/mobile-tabs.gif"
        },
        {
            title: "4. Indicador de Intentos",
            text: "Tienes un número limitado de intentos para que los resultados coincidan. El progreso se muestra con los puntos justo arriba de los formularios.",
            image: "../../img/tutorial/intentos.png"
        },
        {
            title: "5. Notificar al Rival",
            text: "Si necesitas que el rival cargue o revise sus datos, haz clic en el botón 'Abrir WhatsApp' situado justo debajo del 'Formulario del rival' para enviarle un mensaje rápido.",
            image: "../../img/tutorial/whatsapp-button.png"
        },
        {
            title: "6. Califica la deportividad",
            text: "Una vez que todos los campos coincidan (¡estén en verde!), el botón 'Calificar deportividad' se activará. Dejá tu comentario sobre la actitud del rival y califica del 1 al 5 su deportividad.",
            image: "assets/img/tutorial/calificar.png"
        },
        {
            title: "7. Envía el Resultado Final",
            text: "Una vez que todos los campos coincidan (¡estén en verde!), el botón 'Enviar resultado' se activará. Haz clic para guardar los datos y finalizar la coordinación.",
            image: "assets/img/tutorial/enviar-resultado.png" // Imagen del botón de enviar resultado activado
        },
        {
            title: "¡Listo para Coordinar!",
            text: "Con estos pasos, coordinar los resultados de tus partidos será pan comido. ¡Mucha suerte!",
            image: "assets/img/tutorial/final-message.png" // Imagen final (opcional, como un thumbs-up o logo)
        }
    ];

    // Inicializar el carrusel del tutorial
    new TutorialCoordinarResultadoComponent(tutorialSlides, {
        tutorialKey: 'coordinarTutorialSeen'
    });

    // Pestañas en mobile
    const myFormColumn = document.getElementById('my-form');
    const rivalFormColumn = document.getElementById('rival-form');
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabNav = document.querySelector('.tab-nav');

    const activateTab = (tabId) => {
        tabButtons.forEach(button => {
            button.classList.toggle('active', button.dataset.tab === tabId);
        });

        if (tabId === 'my-form') {
            myFormColumn.classList.add('active');
            rivalFormColumn.classList.remove('active');
        } else {
            myFormColumn.classList.remove('active');
            rivalFormColumn.classList.add('active');
        }
    };

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            activateTab(button.dataset.tab);
        });
    });

    // Mostrar/ocultar tabs según ancho
    const mediaQuery = window.matchMedia('(max-width: 600px)');
    const handleMediaQueryChange = (e) => {
        if (e.matches) {
            tabNav.style.display = 'flex';
            activateTab('my-form');
        } else {
            tabNav.style.display = 'none';
            myFormColumn.classList.add('active');
            rivalFormColumn.classList.add('active');
            myFormColumn.style.display = '';
            rivalFormColumn.style.display = '';
        }
    };

    // Llamada inicial para establecer el estado correcto al cargar la página
    handleMediaQueryChange(mediaQuery);
    // Listener para cambios en el media query
    mediaQuery.addEventListener('change', handleMediaQueryChange);

    // Botón WhatsApp
    const whatsappBtn = document.querySelector('.btn-whatsapp[data-wa-url]');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', () => {
            const url = whatsappBtn.getAttribute('data-wa-url');
            if (url) window.open(url, '_blank');
        });
    }
});