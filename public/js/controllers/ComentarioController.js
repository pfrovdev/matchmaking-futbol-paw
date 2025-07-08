import ComentarioService from '../services/ComentarioService.js';
import ComentarioComponent from '../components/ComentarioComponent.js';
import FilterComponent from '../components/FilterComponent.js';
import PaginationComponent from '../components/PaginationComponent.js';

export default class ComentarioController {

  /**
   * @param {Object} args
   * @param {HTMLElement} args.comentarioContainer - <ul> donde van los comentarios.
   * @param {HTMLSelectElement} args.filterSelect - <select> para filtrar/ordenar.
   * @param {HTMLElement} args.paginationContainer - Contenedor <div> para la paginación.
   */
  constructor({ profileId, comentarioContainer, filterSelect, paginationContainer }) {
    this.comentarioContainer = comentarioContainer;
    this.filterSelect = filterSelect;
    this.paginationContainer = paginationContainer;
    this.profileId = profileId;

    this.pageSize = 3; 
    this.currentPage = 1;
    this.order = 'fecha_creacion';
    this.dir = 'DESC';

    this.comentarioComponent = null;
    this.paginationComponent = null;
    this.filterComponent = null;
  }

  async init() {
    // 1) Instanciar ComentarioComponent con lista vacía
    this.comentarioComponent = new ComentarioComponent([], this.comentarioContainer);

    // 2) Instanciar FilterComponent
    this.filterComponent = new FilterComponent(
      this.filterSelect,
      ({ order: newOrder, dir: newDir }) => {
        // Al cambiar el filtro:
        this.order = newOrder;
        this.dir = newDir;
        this.currentPage = 1;
        this.loadComentarios();
      }
    );

    // 3) Instanciar PaginationComponent
    //    Pass totalItems=0 (hasta que traigamos datos), pageSize=this.pageSize, onPageChange=callback
    this.paginationComponent = new PaginationComponent(
      this.paginationContainer,
      0, // totalItems inicial (se actualizará cuando carguemos datos)
      this.pageSize,
      (page) => {
        this.currentPage = page;
        this.loadComentarios();
      }
    );

    // 4) Carga inicial de comentarios
    await this.loadComentarios();
  }

  async loadComentarios() {
    try {
      const { data: comentariosRaw, meta } = await ComentarioService.getComentarios({
        page: this.currentPage,
        perPage: this.pageSize,
        order: this.order,
        dir: this.dir
      });

      // 5) Actualizar la lista en pantalla
      this.comentarioComponent.updateData(comentariosRaw);

      // 6) Actualizar paginación (totalItems + página actual)
      this.paginationComponent.setTotalItems(meta.totalItems);
      this.paginationComponent.setCurrentPage(meta.currentPage);
    } catch (err) {
      console.error('Error cargando comentarios:', err);
    }
  }
}