export default class TabNavComponent {
  constructor({ tabButtonsSelector, sectionsSelector, activeClass = 'active' }) {
    this.buttons = document.querySelectorAll(tabButtonsSelector);
    this.sections = document.querySelectorAll(sectionsSelector);
    this.activeClass = activeClass;
    if (this.buttons.length) this.init();
  }

  init() {
    this.buttons.forEach(btn => btn.addEventListener('click', () => this.switchTab(btn.dataset.tab)));
    this.switchTab(this.buttons[0].dataset.tab);
  }

  switchTab(tabId) {
    this.buttons.forEach(b => b.classList.toggle(this.activeClass, b.dataset.tab === tabId));
    this.sections.forEach(sec => sec.id === tabId
      ? sec.classList.add(this.activeClass)
      : sec.classList.remove(this.activeClass)
    );
  }
}