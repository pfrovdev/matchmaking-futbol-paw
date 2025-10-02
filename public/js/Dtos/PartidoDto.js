import EquipoBannerDto from './EquipoBannerDto.js';

export default class PartidoDto {

  constructor({ id_partido, equipo, fecha_creacion, finalizado, finalizado_equipo_desafiante, finalizado_equipo_desafiado }) {
    this.idPartido = id_partido;
    this.equipo = new EquipoBannerDto(equipo);
    this.fechaCreacion= new Date(fecha_creacion);
    this.finalizado = finalizado;
    this.finalizadoEquipoDesafiante = finalizado_equipo_desafiante;
    this.finalizadoEquipoDesafiado = finalizado_equipo_desafiado;
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