import PartidoService from '../services/PartidoService.js';
import PartidoComponent from '../components/PartidoComponent.js';
import FilterComponent from '../components/FilterComponent.js';
import PaginationComponent from '../components/PaginationComponent.js';

export default class PartidoController {
  /**
   * @param {Object} args
   * @param {HTMLElement} args.partidoContainer - <ul> donde van los <li> de partidos.
   * @param {HTMLSelectElement} args.filterSelect - <select> para filtrar/ordenar.
   * @param {HTMLElement} args.paginationContainer - <div> para paginación.
   */
  constructor({ partidoContainer, filterSelect, paginationContainer }) {
    this.partidoContainer = partidoContainer;
    this.filterSelect = filterSelect;
    this.paginationContainer = paginationContainer;

    this.pageSize = 2;
    this.currentPage = 1;
    this.order = 'fecha_creacion';
    this.dir = 'DESC';

    this.partidoComponent = null;
    this.filterComponent = null;
    this.paginationComponent = null;
  }

  async init() {
    // Instanciar PartidoComponent con lista vacía
    this.partidoComponent = new PartidoComponent([], this.partidoContainer);

    // Instanciar FilterComponent
    this.filterComponent = new FilterComponent(
      this.filterSelect,
      ({ order: newOrder, dir: newDir }) => {
        this.order = newOrder;
        this.dir = newDir;
        this.currentPage = 1;
        this.loadPartidos();
      }
    );

    // Instanciar PaginationComponent
    this.paginationComponent = new PaginationComponent(
      this.paginationContainer,
      0,               // totalItems inicial
      this.pageSize,
      (page) => {
        this.currentPage = page;
        this.loadPartidos();
      }
    );

    // Primera carga
    return await this.loadPartidos();
  }

  async loadPartidos() {
    try {
      const { data: partidosRaw, meta } = await PartidoService.getPartidos({
        page: this.currentPage,
        perPage: this.pageSize,
        order: this.order,
        dir: this.dir
      });

      // Actualizar UI
      this.partidoComponent.updateData(partidosRaw);
      this.paginationComponent.setTotalItems(meta.totalItems);
      this.paginationComponent.setCurrentPage(meta.currentPage);

      return partidosRaw.length > 0;
    } catch (err) {
      console.error('Error cargando partidos:', err);
      return false;
    }
  }
}