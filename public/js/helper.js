document.addEventListener('DOMContentLoaded', () => {

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