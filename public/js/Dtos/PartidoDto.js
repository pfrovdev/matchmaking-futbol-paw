import EquipoBannerDto from './EquipoBannerDto.js';

export default class PartidoDto {

  constructor({ id_partido, equipo, finalizado, fecha_creacion }) {
    this.idPartido = id_partido;
    this.equipo = new EquipoBannerDto(equipo);
    this.finalizado = finalizado;
    this.fechaCreacion= new Date(fecha_creacion);
  }

  getFechaFormateada() {
    return this.fechaCreacion.toLocaleString('es-AR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
}