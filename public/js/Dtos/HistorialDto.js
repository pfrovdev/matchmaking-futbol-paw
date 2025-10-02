export default class HistorialDto {
    constructor({
        fecha_finalizacion,
        resultadoGanador,
        resultadoPerdedor,
        esEmpate,
    }) {
        this.fecha = new Date(fecha_finalizacion);
        this.resultadoGanador = resultadoGanador;
        this.resultadoPerdedor = resultadoPerdedor;
        this.esEmpate = esEmpate;
    }

    getFechaFormateada() {
        return this.fecha.toLocaleString('es-AR', {
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit'
        });
    }
}