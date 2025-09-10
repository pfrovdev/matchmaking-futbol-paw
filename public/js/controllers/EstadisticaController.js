import EstadisticaService from '../services/EstadisticaService.js';
import EstadisticaDto from '../Dtos/EstadisticaDto.js';

export default class EstadisticasController {
    constructor() {
        this.graficos = {};
        this.estadisticas = null;
    }

    async inicializar() {
        try {
            const estadisticasRaw = await EstadisticaService.getStats();

            this.estadisticas = new EstadisticaDto(estadisticasRaw || {});
            if (this.estadisticas) {
                this.actualizarDatosResumen(this.estadisticas);
                this.actualizarMetricas(this.estadisticas);
                this.configurarPestanas(this.estadisticas);
            }
        } catch (error) {
            console.error("error al cargar las estadísticas:", error);
        }
    }

    actualizarDatosResumen(stats) {
        const elementoTasaVictoria = document.getElementById('tasa-victoria-destacada');
        const elementoPartidos = document.getElementById('partidos-destacados');
        const elementoGoles = document.getElementById('goles-destacados');
        const elementoElo = document.getElementById('elo-destacado');

        const tasaVictoria = stats.jugados > 0 ? (stats.ganados / stats.jugados) * 100 : 0;
        if (elementoTasaVictoria) elementoTasaVictoria.textContent = `${tasaVictoria.toFixed(0)}%`;
        if (elementoPartidos) elementoPartidos.textContent = stats.jugados ?? 0;
        if (elementoGoles) elementoGoles.textContent = stats.goles ?? 0;
        if (elementoElo) elementoElo.textContent = stats.eloActual ?? 'N/A';
    }

    actualizarMetricas(stats) {
        const elementoMetricaGolesFavor = document.getElementById('metrica-goles-favor');
        const elementoMetricaGolesContra = document.getElementById('metrica-goles-contra');
        const elementoMetricaDiferenciaGol = document.getElementById('metrica-diferencia-gol');
        const elementoMetricaAsistencias = document.getElementById('metrica-asistencias');
        const elementoMetricaAmarillas = document.getElementById('metrica-amarillas');
        const elementoMetricaRojas = document.getElementById('metrica-rojas');
        const elementoMetricaRachaActual = document.getElementById('metrica-racha-actual');
        const elementoMetricaRachaMasLarga = document.getElementById('metrica-racha-maslarga');

        if (elementoMetricaGolesFavor) elementoMetricaGolesFavor.textContent = stats.goles ?? 0;
        if (elementoMetricaGolesContra) elementoMetricaGolesContra.textContent = stats.golesEnContra ?? 0;
        if (elementoMetricaAsistencias) elementoMetricaAsistencias.textContent = stats.asistencias ?? 0;
        if (elementoMetricaAmarillas) elementoMetricaAmarillas.textContent = stats.tarjetasAmarillas ?? 0;
        if (elementoMetricaRojas) elementoMetricaRojas.textContent = stats.tarjetasRojas ?? 0;
        if (elementoMetricaRachaActual) elementoMetricaRachaActual.textContent = stats.rachaActual ?? 0;
        if (elementoMetricaRachaMasLarga) elementoMetricaRachaMasLarga.textContent = stats.rachaMasLarga ?? 0;

        if (elementoMetricaDiferenciaGol) {
            if (stats.diferenciaGol > 0) {
                elementoMetricaDiferenciaGol.className = 'valor-metrica positivo';
                elementoMetricaDiferenciaGol.textContent = `+${stats.diferenciaGol}`;
            } else if (stats.diferenciaGol < 0) {
                elementoMetricaDiferenciaGol.className = 'valor-metrica negativo';
                elementoMetricaDiferenciaGol.textContent = stats.diferenciaGol;
            } else {
                elementoMetricaDiferenciaGol.className = 'valor-metrica neutro';
                elementoMetricaDiferenciaGol.textContent = '0';
            }
        }

        this.actualizarInsights(stats);
        this.actualizarUltimosPartidos(stats);
        this.actualizarPanelPromedios(stats);
    }

    actualizarInsights(stats) {
        const elementoInsightGanados = document.getElementById('insight-ganados');
        const elementoInsightEmpatados = document.getElementById('insight-empatados');
        const elementoInsightPerdidos = document.getElementById('insight-perdidos');

        if (elementoInsightGanados) elementoInsightGanados.textContent = stats.ganados ?? 0;
        if (elementoInsightEmpatados) elementoInsightEmpatados.textContent = stats.empatados ?? 0;
        if (elementoInsightPerdidos) elementoInsightPerdidos.textContent = stats.perdidos ?? 0;

        const elementoInsightEloMaximo = document.getElementById('insight-elo-maximo');
        const elementoInsightEloActual = document.getElementById('insight-elo-actual');
        const elementoInsightEloProgreso = document.getElementById('insight-elo-progreso');

        if (elementoInsightEloMaximo) elementoInsightEloMaximo.textContent = stats.eloMasAlto ?? '0';

        const eloActual = (Array.isArray(stats.eloHistorial) && stats.eloHistorial.length > 0) ? stats.eloHistorial[stats.eloHistorial.length - 1] : (stats.eloActual ?? 0);
        if (elementoInsightEloActual) elementoInsightEloActual.textContent = eloActual ?? 0;

        const eloInicial = (Array.isArray(stats.eloHistorial) && stats.eloHistorial.length > 0) ? stats.eloHistorial[0] : eloActual;
        const progreso = (eloActual || 0) - (eloInicial || 0);
        if (elementoInsightEloProgreso) {
            elementoInsightEloProgreso.textContent = progreso > 0 ? `+${progreso}` : progreso;
            elementoInsightEloProgreso.style.color = progreso > 0 ? '#64c67c' : progreso < 0 ? '#df2727' : '#666';
        }
    }

    actualizarUltimosPartidos(stats) {
        const contenedor = document.getElementById('resultados-recientes');
        if (!contenedor) return;
        contenedor.innerHTML = '';

        const arr = stats.ultimos5Partidos;
        if (Array.isArray(arr) && arr.length > 0) {
            arr.slice(0, 5).forEach(item => {
                let resultado = null;
                let title = '';
                if (item && typeof item === 'object' && item.resultado) {
                    resultado = item.resultado;
                    title = `vs ${item.nombre_rival || 'Rival'} - ${resultado}`;
                } else if (typeof item === 'string') {
                    const s = item.trim();
                    if (s === '✅' || s.toLowerCase().includes('g')) resultado = 'ganado';
                    else if (s === '❌' || s.toLowerCase().includes('p')) resultado = 'perdido';
                    else if (s === '➖' || s === '-' || s === 'E' || s.toLowerCase().includes('e')) resultado = 'empatado';
                    else {
                        const low = s.toLowerCase();
                        if (low.includes('gan')) resultado = 'ganado';
                        else if (low.includes('emp')) resultado = 'empatado';
                        else if (low.includes('per')) resultado = 'perdido';
                        else resultado = 'desconocido';
                    }
                    title = `Resultado: ${item}`;
                } else {
                    resultado = 'desconocido';
                    title = 'Resultado desconocido';
                }

                const elementoResultado = document.createElement('div');
                elementoResultado.className = `insignia-resultado ${resultado === 'ganado' ? 'ganado' : resultado === 'empatado' ? 'empatado' : resultado === 'perdido' ? 'perdido' : ''}`;
                elementoResultado.textContent = resultado === 'ganado' ? 'G' : resultado === 'empatado' ? 'E' : resultado === 'perdido' ? 'P' : '?';
                elementoResultado.title = title;
                contenedor.appendChild(elementoResultado);
            });
        } else {
            contenedor.innerHTML = '<span class="sin-datos">Sin partidos</span>';
        }
    }

    configurarPestanas(stats) {
        const botonesPestana = document.querySelectorAll('.pestana-boton');
        const panelesPestana = document.querySelectorAll('.panel-pestana');

        const mostrarPestana = (id) => {
            panelesPestana.forEach(panel => panel.classList.remove('activo'));
            botonesPestana.forEach(boton => boton.classList.remove('activo'));

            const panelObjetivo = document.getElementById(id);
            const botonObjetivo = document.querySelector(`.pestana-boton[data-tab="${id}"]`);

            if (panelObjetivo && botonObjetivo) {
                panelObjetivo.classList.add('activo');
                botonObjetivo.classList.add('activo');
                this.inicializarGrafico(id, stats);
            }
        };

        botonesPestana.forEach(boton => {
            boton.addEventListener('click', (evento) => {
                const id = evento.currentTarget.dataset.tab;
                mostrarPestana(id);
            });
        });

        if (botonesPestana.length > 0) {
            const idPredeterminado = botonesPestana[0].dataset.tab;
            mostrarPestana(idPredeterminado);
        }
    }

    inicializarGrafico(tabId, stats) {
        if (typeof Chart === 'undefined') {
            console.error('EstadisticaController.js: Chart.js fallo al cargar');
            return;
        }

        // Destruye el gráfico anterior si existe para evitar superposiciones
        if (this.graficos[tabId]) {
            try { this.graficos[tabId].destroy(); } catch (e) {}
            this.graficos[tabId] = null;
        }

        switch (tabId) {
            case 'resumen': {
                const canvas = document.getElementById('grafico-resumen');
                if (canvas) {
                    this.graficos['resumen'] = new Chart(canvas, {
                        type: "doughnut",
                        data: {
                            labels: ["Ganados", "Empatados", "Perdidos"],
                            datasets: [{
                                data: [stats.ganados ?? 0, stats.empatados ?? 0, stats.perdidos ?? 0],
                                backgroundColor: ["#64c67c", "#f2c94c", "#df2727"],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            plugins: { legend: { position: "bottom", labels: { font: { size: 11 }, padding: 8 } } },
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%'
                        }
                    });
                }
                break;
            }
            case 'rendimiento': {
                const canvas = document.getElementById('grafico-rendimiento');
                if (!canvas) return;

                if (!Array.isArray(stats.eloHistorial) || stats.eloHistorial.length === 0) {
                    const contenedorGrafico = canvas.parentElement;
                    if (contenedorGrafico) contenedorGrafico.innerHTML = '<div style="text-align:center;padding:1rem;color:#666">No hay datos de ELO disponibles</div>';
                    return;
                }

                const maxElo = Math.max(...stats.eloHistorial);
                const minElo = Math.min(...stats.eloHistorial);

                this.graficos['rendimiento'] = new Chart(canvas, {
                    type: "line",
                    data: {
                        labels: stats.eloHistorial.map((_, i) => i + 1),
                        datasets: [{
                            label: "ELO",
                            data: stats.eloHistorial,
                            fill: true,
                            borderColor: "#092469",
                            backgroundColor: "rgba(9,36,105,0.08)",
                            tension: 0.35,
                            pointRadius: 2,
                            pointHoverRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { min: Math.max(0, minElo - 50), max: maxElo + 50, ticks: { font: { size: 10 } } },
                            x: { ticks: { font: { size: 10 } } }
                        }
                    }
                });
                break;
            }
            case 'promedios': {
                const canvas = document.getElementById('grafico-promedios');
                if (!canvas) return;

                const valores = [
                    stats.promedioGoles ?? 0,
                    stats.promedioAsistencias ?? 0,
                    stats.promedioAmarillas ?? 0,
                    stats.promedioGolesEnContra ?? 0,
                    stats.promedioRojas ?? 0
                ];

                const elG = document.getElementById('promedio-goles-valor');
                const elA = document.getElementById('promedio-asistencias-valor');
                const elAm = document.getElementById('promedio-amarillas-valor');
                const elGc = document.getElementById('promedio-golescontra-valor');
                const elR = document.getElementById('promedio-rojas-valor');
                if (elG) elG.textContent = (stats.promedioGoles ?? 0).toFixed(2);
                if (elA) elA.textContent = (stats.promedioAsistencias ?? 0).toFixed(2);
                if (elAm) elAm.textContent = (stats.promedioAmarillas ?? 0).toFixed(2);
                if (elGc) elGc.textContent = (stats.promedioGolesEnContra ?? 0).toFixed(2);
                if (elR) elR.textContent = (stats.promedioRojas ?? 0).toFixed(2);

                this.graficos['promedios'] = new Chart(canvas, {
                    type: 'radar',
                    data: {
                        labels: ['Goles', 'Asistencias', 'Amarillas', 'Goles En Contra', 'Rojas'],
                        datasets: [{
                            label: 'Promedios',
                            data: valores,
                            fill: true,
                            borderColor: '#092469',
                            backgroundColor: 'rgba(9,36,105,0.12)',
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    backdropColor: 'rgba(255,255,255,0.0)'
                                },
                                pointLabels: { font: { size: 11 } }
                            }
                        },
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
                break;
            }
        }
    }

    actualizarPanelPromedios(stats) {
        const panel = document.getElementById('promedios');
        if (panel && panel.classList.contains('activo')) {
            this.inicializarGrafico('promedios', stats);
        }
    }
}