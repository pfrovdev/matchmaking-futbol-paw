import DesafioDto from '../Dtos/DesafioDto.js';

export default class DesafioService {
  
  static async getDesafios({ page = 1, perPage = 3, order = 'fecha_creacion', dir = 'DESC' } = {}) {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: perPage.toString(),
      order,
      dir
    });

    const response = await fetch(`/desafios?${params}`);
    if (!response.ok) {
      throw new Error(`Error al obtener desafíos: ${response.statusText}`);
    }

    const { data: rawData, meta } = await response.json();
    const desafios = rawData.map(obj => new DesafioDto(obj));
    return { data: desafios, meta };
  }
}