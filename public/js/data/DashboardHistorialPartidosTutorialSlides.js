const steps = [
    {
        intro: "🏆 ¡Coordinaste tu primer partido! Acá podes revisar los detalles de tus encuentros finalizados."
    },
    {
        element: '#history-list .history-card-base:first-child',
        intro: "Resultado Rápido: El color de fondo indica el resultado del partido para tu equipo: Verde para Victoria, Rojo para Derrota o Gris para Empate."
    },
    {
        element: '#history-list .history-card-base:first-child .elo-change',
        intro: "Cambio de ELO: Este valor muestra cuántos puntos de ELO ganaste o perdiste con este partido."
    },
    {
        element: '#history-list .history-card-base:first-child .match-date',
        intro: "Fecha: Muestra cuándo se finalizó y registró este partido en el sistema."
    },
    {
        element: '#history-list .history-card-base:first-child .hc-score',
        intro: "Marcador: El resultado final del partido (Tus Goles - Goles del Rival)."
    },
    {
        element: '#history-list .history-card-base:first-child .team-block.home .tarjetas',
        intro: "Tarjetas: Muestra las tarjetas amarillas y rojas que recibió tu equipo en este encuentro."
    },
    {
        element: '#history-list .history-card-base:first-child .team-block.away',
        intro: "Rival: Datos del equipo contra el que jugaste. Puedes hacer clic en el escudo para ver su perfil."
    },
    {
        element: '.stats-card',
        intro: "El resultado se verá reflejado en tu ELO y en las estadísticas generales de tu equipo."
    },
    {
        intro: "¡Eso es todo! Ahora puedes analizar tu rendimiento histórico. ¡A seguir escalando en el ranking! 📈"
    }
];

export default steps;