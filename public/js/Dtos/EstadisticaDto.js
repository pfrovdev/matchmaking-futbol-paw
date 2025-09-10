export default class EstadisticaDto {
    constructor({
        jugados,
        goles,
        goles_en_contra,
        asistencias,
        tarjetas_amarillas,
        tarjetas_rojas,
        ganados,
        empatados,
        perdidos,
        promedio_goles,
        promedio_asistencias,
        promedio_amarillas,
        promedio_goles_en_contra,
        promedio_rojas,
        diferencia_gol,
        elo_mas_alto,
        ultimos_5_partidos,
        elo_historial,
        racha_actual,
        racha_mas_larga
    }) {
        this.jugados = jugados ?? 0;
        this.goles = goles ?? 0;
        this.golesEnContra = goles_en_contra ?? 0;
        this.asistencias = asistencias ?? 0;
        this.tarjetasAmarillas = tarjetas_amarillas ?? 0;
        this.tarjetasRojas = tarjetas_rojas ?? 0;
        this.ganados = ganados ?? 0;
        this.empatados = empatados ?? 0;
        this.perdidos = perdidos ?? 0;
        this.promedioGoles = promedio_goles ?? 0;
        this.promedioAsistencias = promedio_asistencias ?? 0;
        this.promedioAmarillas = promedio_amarillas ?? 0;
        this.promedioGolesEnContra = promedio_goles_en_contra ?? 0;
        this.promedioRojas = promedio_rojas ?? 0;
        this.diferenciaGol = diferencia_gol ?? 0;
        this.eloMasAlto = elo_mas_alto ?? 0;
        this.ultimos5Partidos = ultimos_5_partidos ?? [];
        this.eloHistorial = Array.isArray(elo_historial) ? elo_historial.map(e => (typeof e === 'object' ? (e.elo ?? 0) : e)) : [];
        this.rachaActual = racha_actual ?? 0;
        this.rachaMasLarga = racha_mas_larga ?? 0;

        if (this.eloHistorial.length > 0) {
            this.eloActual = this.eloHistorial[this.eloHistorial.length - 1];
        } else {
            this.eloActual = this.eloMasAlto || null;
        }
    }
}