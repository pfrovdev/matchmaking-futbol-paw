const steps = [
    {
        intro: "¡⚽ Tienes un partido próximo! Aquí te explicamos los detalles clave para coordinar tu encuentro."
    },
    {
        // El contenedor es #match-list y el elemento de la lista es .match-item
        element: '#match-list .match-item:first-child .nm-team-name',
        intro: "Rival: Este es el nombre del equipo contra el que vas a jugar. Revisa su perfil si es necesario."
    },
    {
        element: '#match-list .match-item:first-child .team-rank',
        intro: "Rango del Rival: Muestra su nivel ELO actual. Recuerda que ya aceptaste el desafío, ¡ahora a jugar!"
    },
    {
        element: '#match-list .match-item:first-child .nm-team-record',
        intro: "Récord: Victorias/Derrotas/Empates (W/L/D) del equipo rival."
    },
    {
        element: '#match-list .match-item:first-child .nm-sport-icons',
        intro: "Deportividad: Un indicador de cómo se comporta el equipo. ¡Siempre respeta el fair play!"
    },
    {
        element: '#match-list .match-item:first-child .profile-link',
        intro: "Ver Perfil: Haz click para ver las estadísticas completas del rival."
    },
    {
        element: '#match-list .match-item:first-child .nm-btn-secondary',
        intro: "💬 Abrir WhatsApp: Usa este botón para comunicarte directamente con el capitán del equipo rival y coordinar fecha, hora y lugar."
    },
    {
        element: '#match-list .match-item:first-child .nm-btn-primary',
        intro: "✅ Coordinar Resultado: Una vez jugado el partido, usa este botón para cargar el resultado final."
    },
    {
        element: '#match-list .match-item:first-child .btn-cancelar',
        intro: "❌ Cancelar: Si por algún motivo de fuerza mayor deben suspender, usa este botón para cancelar el partido."
    },
    {
        intro: "¡Listo! Ya conoces todos los detalles para jugar y reportar tus partidos próximos. ¡A la cancha! ⚽"
    }
];

export default steps;