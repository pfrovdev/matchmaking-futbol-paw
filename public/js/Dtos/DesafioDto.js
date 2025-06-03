// Dtos/DesafioDto.js

import EquipoBannerDto from './EquipoBannerDto.js';

export default class DesafioDto {
  /**
   * @param {Object} params
   * @param {number} params.id_desafio
   * @param {string} params.fecha_creacion
   * @param {Object} params.equipo_desafiante  // datos crudos para EquipoBannerDto
   */
  constructor({ id_desafio, fecha_creacion, equipo_desafiante }) {
    this.idDesafio = id_desafio;
    this.fechaCreacion = new Date(fecha_creacion);
    this.equipoDesafiante = new EquipoBannerDto(equipo_desafiante);
  }

  getFechaComoDate() {
    return new Date(this.fechaCreacion);
  }

  getFechaFormateada() {
    const d = this.getFechaComoDate();
    return d.toLocaleString('es-AR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  toPlainObject() {
    return {
      idDesafio: this.idDesafio,
      fechaCreacion: this.getFechaFormateada(),
      equipo: this.equipoDesafiante,
    };
  }
}