export default class ModalComponent {
    constructor({ triggerSelector, modalSelector }) {
        this.trigger = document.querySelector(triggerSelector);
        this.modal = document.querySelector(modalSelector);
        this.closeBtn = this.modal?.querySelector('.close-button');
        if (this.trigger && this.modal) this.init();
    }

    init() {
        this.modal.style.display = 'none';
        this.trigger.addEventListener('click', () => this.open());
        this.closeBtn.addEventListener('click', () => this.close());
        window.addEventListener('click', e => e.target === this.modal && this.close());
    }

    open() {
        this.modal.style.display = 'flex';
    }

    close() {
        this.modal.style.display = 'none';
    }

}