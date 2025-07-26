export default class RatingComponent {

    constructor({ containerSelector, inputName }) {
        this.starWrapper = document.querySelector(containerSelector);
        this.inputName = inputName;
        this.rating = 0;
        this.hiddenInput = null;

        if (this.starWrapper) {
            this.init();
        } else {
            console.error(`RatingComponent: No se encontró el contenedor de estrellas con el selector: ${containerSelector}`);
        }
    }

    init() {
        this.buildInput();
        this.attachListeners();
        const initialValue = parseInt(this.hiddenInput.value, 10) || 0;
        this.updateStars(initialValue);
    }
    buildInput() {
        this.hiddenInput = document.getElementById(this.inputName + 'Input');
        if (!this.hiddenInput) {
            this.hiddenInput = document.createElement('input');
            this.hiddenInput.type = 'hidden';
            this.hiddenInput.name = this.inputName;
            this.hiddenInput.id = this.inputName + 'Input';
            this.starWrapper.parentElement.append(this.hiddenInput);
        }
    }

    attachListeners() {
        this.starWrapper.querySelectorAll('.rating-icon').forEach(star => {
            star.addEventListener('mouseover', () => this.onHover({ target: star }));
            star.addEventListener('mouseout', () => this.updateStars(this.rating));
            star.addEventListener('click', () => this.onClick({ target: star }));
        });
    }

    onHover({ target }) {
        if (!target.matches('.rating-icon')) return;
        const hoverVal = Number(target.dataset.value);
        this.updateStars(hoverVal);
    }

    onClick({ target }) {
        if (!target.matches('.rating-icon')) return;
        this.rating = Number(target.dataset.value);
        this.hiddenInput.value = this.rating;
        this.updateStars(this.rating);
    }

    updateStars(currentValue = this.rating) {
        this.starWrapper.querySelectorAll('.rating-icon').forEach(star => {
            const val = Number(star.dataset.value);
            if (val <= currentValue) {
                star.classList.remove('empty');
            } else {
                star.classList.add('empty');
            }
        });
    }
}