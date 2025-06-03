import PartidoDto from '../Dtos/PartidoDto.js';

export default class PartidoService {

  static async getPartidos({ page = 1, perPage = 3, order = 'fecha_creacion', dir = 'DESC' } = {}) {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: perPage.toString(),
      order,
      dir
    });

    const response = await fetch(`/partidos?${params}`);
    if (!response.ok) {
      throw new Error(`Error al obtener partidos: ${response.statusText}`);
    }
    const { data: rawData, meta } = await response.json();
    const partidos = rawData.map(item => new PartidoDto(item));
    return { data: partidos, meta };
  }
}