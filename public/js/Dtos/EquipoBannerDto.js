export default class EquipoBannerDto {
  constructor({id_equipo, nombre_equipo, acronimo, url_foto_perfil, lema, elo_actual, descripcion_elo, deportividad, tipoEquipo, resultadosEquipo}) {
    this.idEquipo = id_equipo;
    this.nombreEquipo = nombre_equipo;
    this.acronimo = acronimo;
    this.urlFotoPerfil = url_foto_perfil;
    this.lema = lema;
    this.eloActual = elo_actual;
    this.descripcionElo = descripcion_elo;
    this.deportividad = deportividad;
    this.tipoEquipo = tipoEquipo;
    this.resultadosEquipo = resultadosEquipo;
  }

  getNombreFormateado() {
    return `${this.acronimo} — ${this.nombreEquipo}`;
  }
}