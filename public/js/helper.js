document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector(".form-container");
    const passwordInput = document.getElementById("password");
    const confirmInput = document.getElementById("confirm_password");
    const errorBox = document.querySelector(".error-messages");

    if (!errorBox) {
        errorBox = document.createElement("section");
        errorBox.classList.add("error-messages");
        form.prepend(errorBox);
    }

    form.addEventListener("submit", (e) => {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        const errors = validatePassword(password);

        if (password !== confirm) {
            errors.push("Las contraseñas no coinciden.");
        }

       if (errors.length > 0) {
            e.preventDefault();
            errorBox.innerHTML = errors.map(err => `<p class="error-text">${err}</p>`).join("");
        }
    });

    const togglePasswordVisibility = (inputSelector, iconSelector) => {
        const input = document.querySelector(inputSelector);
        const icon = document.querySelector(iconSelector);

        if (!input || !icon) return;

        icon.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            icon.src = isPassword ? '/icons/open-eye.png' : '/icons/close-eye.png';
            icon.alt = isPassword ? 'ocultar contraseña' : 'mostrar contraseña';
        });
    };
    // este lo usamos para el login
    togglePasswordVisibility('#password', '.icon-password');
    // y este para crear cuenta pass y confirm
    togglePasswordVisibility('#confirm_password', '.icon-confirm-password');
});

function validatePassword(password) {
    const minLength = 8;
    const regex = {
        uppercase: /[A-Z]/,
        lowercase: /[a-z]/,
        number: /[0-9]/,
        special: /[!@#$%^&*(),.?":{}|<>]/
    };

    let errors = [];

    if (!password || password.length < minLength) {
        errors.push(`La contraseña debe tener al menos ${minLength} caracteres.`);
    }
    if (!regex.uppercase.test(password)) {
        errors.push("La contraseña debe contener al menos una letra mayúscula.");
    }
    if (!regex.lowercase.test(password)) {
        errors.push("La contraseña debe contener al menos una letra minúscula.");
    }
    if (!regex.number.test(password)) {
        errors.push("La contraseña debe contener al menos un número.");
    }
    if (!regex.special.test(password)) {
        errors.push("La contraseña debe contener al menos un carácter especial.");
    }

    return errors;
}
