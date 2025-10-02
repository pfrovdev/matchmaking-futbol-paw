import HistorialService from '../services/HistorialService.js';
import HistoryComponent from '../components/HistorialComponent.js';
import FilterComponent from '../components/FilterComponent.js';
import PaginationComponent from '../components/PaginationComponent.js';

export default class HistoryController {
  constructor({ historyContainer, filterSelect, paginationContainer, equipoId }) {
    this.container = historyContainer;
    this.filterSelect = filterSelect;
    this.paginationContainer = paginationContainer;
    this.equipoId = equipoId;

    this.pageSize = 3;
    this.currentPage = 1;
    this.order = 'fecha_finalizacion';
    this.dir = 'DESC';

    this.historyComponent = null;
    this.filterComponent = null;
    this.paginationComponent = null;
  }

  async init() {
    this.historyComponent = new HistoryComponent([], this.container, this.equipoId);
    this.filterComponent = new FilterComponent(
      this.filterSelect,
      ({ order, dir }) => {
        this.order = order;
        this.dir = dir;
        this.currentPage = 1;
        this.load();
      }
    );
    this.paginationComponent = new PaginationComponent(
      this.paginationContainer,
      0,
      this.pageSize,
      page => {
        this.currentPage = page;
        this.load();
      }
    );
    return await this.load();
  }

  async load() {
    try {
      const { data, meta } = await HistorialService.getHistorial({
        equipoId: this.equipoId,
        page: this.currentPage,
        perPage: this.pageSize,
        order: this.order,
        dir: this.dir
      });
      this.historyComponent.updateData(data);
      this.paginationComponent.setTotalItems(meta.totalItems);
      this.paginationComponent.setCurrentPage(meta.currentPage);

      return data.length > 0;
    } catch (e) {
      console.error('Error cargando historial:', e);
      return false;
    }
  }
}
