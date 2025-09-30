const steps = [
    {
        intro: "👋 Esta página sirve para buscar un rival y desafiarlo: podés buscar por nombre, filtrar por nivel o zona y luego enviar un desafío."
    },
    // PASOS PARA DESKTOP
    {
        element: document.querySelector('.desktop-filters'),
        intro: "Filtros: Buscá por nombre, elegí nivel/ELO y otros filtros para ajustar la búsqueda.",
        context: 'desktop'
    },
    {
        element: document.querySelector('#map'),
        intro: "Mapa: mové el mapa para centrar la zona y encontrá equipos cercanos.",
        context: 'desktop'
    },
    {
        element: document.querySelector('#radiusSliderDesktop'),
        intro: "Radio de búsqueda: ajustá el radio en kilómetros y enviá el formulario para aplicar la zona.",
        context: 'desktop'
    },
    // PASOS PARA MOBILE
    {
        element: document.querySelector('#openFiltersBtn'),
        intro: "Filtros y Mapa: Tocá este botón para abrir las opciones de filtro, buscá por nombre, nivel/ELO y mapa de zona. ¡Debés abrir el modal para usar el mapa!",
        context: 'mobile'
    },

    {
        element: document.querySelector('.lista-equipos'),
        intro: "Resultados: acá aparece la lista de equipos que coinciden con tu búsqueda y filtros."
    },

    {
        element: document.querySelector('.challenge-card .team-rank'),
        intro: "Rango: indica el nivel (Principiante, Amateur, SemiPro, Profesional)."
    },

    {
        element: document.querySelector('.challenge-card .elo'),
        intro: "ELO: número que te ayuda a medir paridad, es útil para elegir un rival equilibrado."
    },

    {
        element: document.querySelector('.challenge-card .team-motto'),
        intro: "Lema: breve info del equipo que marca su actitud y puede ayudarte a decidir."
    },

    {
        element: document.querySelector('.challenge-card .profile-link'),
        intro: "Ir al perfil: revisá historial, reputación y stats completas antes de desafiar."
    },

    {
        element: document.querySelector('.challenge-card .btn-desafiar'),
        intro: "Botón DESAFIAR: envía la solicitud. Aparecerá un spinner; luego aguardá a que el otro equipo acepte o rechace (Te vamos a informar por mail cuando lo haga)."
    },

    {
        element: document.querySelector('#clearFilters'),
        intro: "Limpiar filtros: volvé a la búsqueda por defecto si querés empezar de nuevo."
    },

    {
        intro: "🎯 Listo ya estás listo/a para desafiar equipos y empezar tu camino"
    }
];

export default steps;