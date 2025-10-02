import  EquipoBannerDto from './EquipoBannerDto.js';

export default class ComentarioEquipoDto {
  constructor({idComentario, deportividad, comentario, fechaCreacion, equipoComentador}) {
    this.idComentario = idComentario;
    this.deportividad = deportividad;
    this.comentario = comentario;
    this.fechaCreacion = new Date(fechaCreacion);
    this.equipoComentador = new EquipoBannerDto(equipoComentador);
  }

  getFechaComoDate() {
    return new Date(this.fechaCreacion);
  }

  getFechaFormateada() {
    const date = this.getFechaComoDate();
    return date.toLocaleString();
  }

  getResumen(maxLen = 100) {
    if (this.texto.length <= maxLen) return this.texto;
    return this.texto.slice(0, maxLen) + '…';
  }
}