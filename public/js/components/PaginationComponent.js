export default class PaginationComponent {
  /**
   * @param {HTMLElement} container - Contenedor donde se dibujarán los enlaces de paginación.
   * @param {number} totalItems - Cantidad total de elementos (viene del backend).
   * @param {number} pageSize - Cantidad de elementos por página.
   * @param {function(number): void} onPageChange - Callback que se ejecuta cuando el usuario hace clic en una nueva página.
   */
  constructor(container, totalItems, pageSize, onPageChange) {
    this.container = container;
    this.totalItems = totalItems;
    this.pageSize = pageSize;
    this.currentPage = 1;
    this.onPageChange = onPageChange;

    this.render();
  }

  setTotalItems(totalItems) {
    this.totalItems = totalItems;
    const maxPage = this.getMaxPage();
    if (this.currentPage > maxPage) {
      this.currentPage = maxPage;
    }
    this.render();
  }

  setCurrentPage(page) {
    const maxPage = this.getMaxPage();
    if (page < 1 || page > maxPage) return;
    this.currentPage = page;
    this.render();
  }

  getMaxPage() {
    return Math.ceil(this.totalItems / this.pageSize) || 1;
  }

  render() {
    this.container.innerHTML = '';

    const maxPage = this.getMaxPage();
    const wrapper = document.createElement('div');
    wrapper.classList.add('pagination-wrapper');

    for (let i = 1; i <= maxPage; i++) {
      const a = document.createElement('a');
      a.href = '#';
      a.textContent = i;
      a.classList.add('pagination-link');

      if (i === this.currentPage) {
        a.classList.add('active');
      }

      a.addEventListener('click', (e) => {
        e.preventDefault();
        if (i === this.currentPage) return;
        this.currentPage = i;
        this.onPageChange(i);
        this.render();
      });

      wrapper.appendChild(a);
    }

    this.container.appendChild(wrapper);
  }
}