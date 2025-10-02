import HistorialDto from '../Dtos/HistorialDto.js';

export default class HistorialService {
    static async getHistorial({ equipoId, page = 1, perPage = 3, order = 'fecha_finalizacion', dir = 'DESC' }) {
        const params = new URLSearchParams({
            equipo_id: equipoId,
            page: page.toString(),
            per_page: perPage.toString(),
            order,
            dir
        });
        const res = await fetch(`/historial-partidos?${params}`);
        if (!res.ok) throw new Error(`Error al obtener historial: ${res.statusText}`);
        const { data: raw, meta } = await res.json();
        const data = raw.map(o => new HistorialDto(o));
        return { data, meta };
    }
}