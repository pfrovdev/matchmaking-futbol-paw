// /js/data/ChallengeTutorialSlides.js

const steps = [
    {
        intro: "¡🎉 Recibiste tu primer desafío! Aquí te explicamos cómo funciona esta tarjeta para que puedas tomar una decisión."
    },
    {
        element: '#challenge-list .challenge-item:first-child .team-name',
        intro: "Equipo Desafiante: Este es el equipo que te está invitando a jugar. El acrónimo entre paréntesis es su abreviación."
    },
    {
        element: '#challenge-list .challenge-item:first-child .team-rank',
        intro: "Rango del Equipo: El color y el texto indican su nivel ELO actual. Útil para medir la paridad."
    },
    {
        element: '#challenge-list .challenge-item:first-child .team-record',
        intro: "ELO y Récord: Puedes ver el valor numérico del ELO y su récord de Victorias/Derrotas/Empates (W/L/D)."
    },
    {
        element: '#challenge-list .challenge-item:first-child .sport-icons',
        intro: "Nivel de Deportividad: Basado en su historial de juego limpio. Un buen indicador de su actitud en el campo."
    },
    {
        element: '#challenge-list .challenge-item:first-child .profile-link',
        intro: "Ver Perfil: Si querés más detalles sobre el rival (stats, historial, etc.), hacé click aquí."
    },
    {
        element: '#challenge-list .challenge-item:first-child .btn-accept',
        intro: "✅ Aceptar Desafío: Presioná este botón para confirmar el partido. Actualizá el dashboard para sincronizar el encuentro con tu rival. ¡El juego ha comenzado!"
    },
    {
        element: '#challenge-list .challenge-item:first-child .btn-reject',
        intro: "❌ Rechazar Desafío: Si no puedes o no quieres jugar con este equipo, presiona aquí para rechazar la solicitud."
    },
    {
        intro: "¡Eso es todo! Ahora podés gestionar tus desafíos. ¡Mucha suerte!"
    }
];

export default steps;