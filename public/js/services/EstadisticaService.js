export default class EstadisticaService {
    static async getStats() {
        const perfilId = document.body.dataset.profileId;
        if (!perfilId) {
            console.error('No se pudo obtener el ID del perfil.');
            return null;
        }
        try {
            const response = await fetch(`/estadisticas?id_equipo=${perfilId}`);

            if (response.status === 204) {
                console.warn('El equipo no tiene estadísticas');
                return null;
            }

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                console.error('Error al obtener estadísticas:', errData);
                return null;
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
