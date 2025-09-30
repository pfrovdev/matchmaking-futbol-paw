export default class Tutorial {
    /**
     * @param {Object} options
     * @param {string} options.key - Clave única para localStorage
     * @param {Array} options.steps - Array de pasos [{ element: selector, intro: texto, position }]
     */
    constructor({ key, steps }) {
        if (!key || !steps || !Array.isArray(steps)) {
            throw new Error('Tutorial requiere key y steps válidos');
        }
        this.key = key;
        this.steps = steps;
        this.localStorageKey = `tutorial_seen_${key}`;
        this.intro = introJs.tour();
    }

    init() {
        // No iniciar tutorial si ya se vio
        if (localStorage.getItem(this.localStorageKey)) return;

        this.intro.setOptions({
            steps: this.steps,
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            skipLabel: 'Saltar',
            doneLabel: 'Entendido',
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            disableInteraction: false
        });

        // Guarda en localStorage cuando se termina el tutorial
        this.intro.oncomplete(() => {
            localStorage.setItem(this.localStorageKey, 'true');
        });

        this.intro.onexit(() => {
            localStorage.setItem(this.localStorageKey, 'true');
        });

        this.intro.start();
    }

    reset() {
        localStorage.removeItem(this.localStorageKey);
    }
}