import DesafioService from '../services/DesafioService.js';
import DesafioComponent from '../components/DesafioComponent.js';
import FilterComponent from '../components/FilterComponent.js';
import PaginationComponent from '../components/PaginationComponent.js';

export default class DesafioController {
  /**
   * @param {Object} args
   * @param {HTMLElement} args.desafioContainer - <ul> donde se inyectan los <li> de desafíos.
   * @param {HTMLSelectElement} args.filterSelect - <select> para filtrar/ordenar.
   * @param {HTMLElement} args.paginationContainer - <div> para la paginación.
   */
  constructor({ desafioContainer, filterSelect, paginationContainer }) {
    this.desafioContainer = desafioContainer;
    this.filterSelect = filterSelect;
    this.paginationContainer = paginationContainer;

    this.pageSize = 3;
    this.currentPage = 1;
    this.order = 'fecha_creacion';
    this.dir = 'DESC';

    this.desafioComponent = null;
    this.filterComponent = null;
    this.paginationComponent = null;
  }

  async init() {
    // Instancio DesafioComponent con lista vacía
    this.desafioComponent = new DesafioComponent([], this.desafioContainer);

    // Instancio FilterComponent
    this.filterComponent = new FilterComponent(
      this.filterSelect,
      ({ order: newOrder, dir: newDir }) => {
        this.order = newOrder;
        this.dir = newDir;
        this.currentPage = 1;
        this.loadDesafios();
      }
    );

    // Instancio PaginationComponent (totalItems arranca en 0)
    this.paginationComponent = new PaginationComponent(
      this.paginationContainer,
      0, // totalItems inicial
      this.pageSize, // pageSize
      (page) => {
        this.currentPage = page;
        this.loadDesafios();
      }
    );

    // Primera carga de desafíos
    return await this.loadDesafios();
  }

  async loadDesafios() {
    try {
      const { data: desafiosRaw, meta } = await DesafioService.getDesafios({
        page: this.currentPage,
        perPage: this.pageSize,
        order: this.order,
        dir: this.dir
      });

      console.log(desafiosRaw);

      // Actualizo lista en pantalla
      this.desafioComponent.updateData(desafiosRaw);

      // Actualizo paginación
      this.paginationComponent.setTotalItems(meta.totalItems);
      this.paginationComponent.setCurrentPage(meta.currentPage);

      // Devolvemos True si se cargaron desafíos para inicializar el tutorial
      return desafiosRaw.length > 0;
    } catch (err) {
      console.error('Error cargando desafíos:', err);
      return false;
    }
  }
}