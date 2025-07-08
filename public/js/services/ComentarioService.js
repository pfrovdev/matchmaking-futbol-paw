import ComentarioEquipoDto from "../Dtos/ComentarioEquipoDto.js";

export default class ComentarioService {
  static async getComentarios({ perfilId = null, page = 1, perPage = 3, order = 'fecha_creacion', dir = 'DESC' } = {}) {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: perPage.toString(),
      order,
      dir
    });
    if (perfilId) {
      params.set('equipo_id', perfilId);
    }
    const response = await fetch(`/comentarios?${params}`);
    if (!response.ok) throw new Error(`Error al obtener comentarios: ${response.statusText}`);
    const { data: comentariosRaw, meta } = await response.json();
    const comentarios = comentariosRaw.map(obj => new ComentarioEquipoDto(obj));
    return { data: comentarios, meta };
  }

}