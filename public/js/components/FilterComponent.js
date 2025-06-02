export default class FilterComponent {
  /**
   * @param {HTMLSelectElement} selectElement - El <select> que contiene las opciones de ordenamiento.
   * @param {function({order: string, dir: string}): void} onFilterChange - Callback que se ejecuta cuando cambia el valor del <select>.
   */
  constructor(selectElement, onFilterChange) {
    this.selectElement = selectElement;
    this.onFilterChange = onFilterChange;

    // Vincular el evento 'change' al <select>
    this.selectElement.addEventListener('change', () => {
      const [newOrder, newDir] = this.selectElement.value.split('-');
      // Llamar al callback pasando el nuevo orden y dirección
      this.onFilterChange({ order: newOrder, dir: newDir.toUpperCase() });
    });
  }

  /**
   * (Opcional) Permite establecer programáticamente un valor en el <select> y disparar el callback.
   * @param {string} value - Algo como "fecha-desc" o "deportividad-asc".
   */
  setValue(value) {
    this.selectElement.value = value;
    const [newOrder, newDir] = value.split('-');
    this.onFilterChange({ order: newOrder, dir: newDir.toUpperCase() });
  }
}