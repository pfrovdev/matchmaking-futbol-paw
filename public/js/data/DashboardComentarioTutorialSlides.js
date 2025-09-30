const steps = [
    {
        intro: "¡✍️ Recibiste tu primer comentario! Esta sección muestra cómo te califica la comunidad después de los partidos."
    },
    {
        element: '#comment-list .comment-item:first-child strong',
        intro: "Equipo Comentador: Aquí ves quién dejó el comentario y la puntuación."
    },
    {
        element: '#comment-list .comment-item:first-child .comment-rating',
        intro: "Calificación de Deportividad: Te califican del 1 al 5 en juego limpio y actitud. ¡Busca siempre el 5!"
    },
    {
        element: '#comment-list .comment-item:first-child p:nth-of-type(2)',
        intro: "Comentario: Este es el feedback específico que dejó el equipo rival sobre tu desempeño y comportamiento."
    },
    {
        element: '#comment-list .comment-item:first-child .comment-date',
        intro: "Fecha: Indica cuándo se publicó el comentario."
    },
    {
        element: '.sport-icons',
        intro: "Deportividad Promedio: Acá ves tu calificación promedio actual. ¡Mejora tu reputación con cada partido!"
    },
    {
        intro: "¡Perfecto! Revisa siempre tus comentarios para mejorar tu reputación en la plataforma."
    }
];

export default steps;