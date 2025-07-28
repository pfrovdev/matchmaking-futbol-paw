import DesafioDto from '../Dtos/DesafioDto.js';

export default class DesafioService {
  
  static async getDesafios({ page = 1, perPage = 3, order = 'fecha_creacion', dir = 'DESC' } = {}) {
    console.log("page",page);
      console.log("perPage",perPage);
      console.log("order",order);
      console.log("dir",dir);
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: perPage.toString(),
      order,
      dir
    });
    
    const response = await fetch(`/desafios?${params}`);
    console.log("response es ", response);
    if (!response.ok) {
      throw new Error(`Error al obtener desafíos: ${response.statusText}`);
    }
    
    const { data: rawData, meta } = await response.json();
    const desafios = rawData.map(obj => new DesafioDto(obj));
    console.log("desafios", desafios);
    return { data: desafios, meta };
  }
}