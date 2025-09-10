export default class EstadisticaService {
    static async getStats() {
        console.log('Iniciando fetch de estadísticas...');
        const perfilId = document.body.dataset.profileId;
        if (!perfilId) {
            console.error('No se pudo obtener el ID del perfil.');
            return null;
        }

        try {
            const response = await fetch(`/estadisticas?id_equipo=${perfilId}`);
            if (!response.ok) {
                console.error('Error al obtener estadísticas:', response);
                throw new Error(`Error al obtener estadísticas: ${response.statusText}`);
            }
            const data = await response.json();
            console.log('Estadísticas obtenidas:', data);
            return data;
        } catch (error) {
            console.error('Error en EstadisticaService:', error);
            return null;
        }
    }
}
