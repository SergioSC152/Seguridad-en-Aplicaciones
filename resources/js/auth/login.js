const passwordInput = document.querySelector('#password');
const toggleButton = document.querySelector('#togglePassword');
const toggleIcon = document.querySelector('#togglePasswordIcon');

if (passwordInput && toggleButton) {
  toggleButton.addEventListener('click', () => {
    const hidden = passwordInput.type === 'password';
    passwordInput.type = hidden ? 'text' : 'password';
    if (toggleIcon) {
      toggleIcon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    }
    toggleButton.setAttribute('aria-label', hidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
    toggleButton.setAttribute('aria-pressed', String(hidden));
    toggleButton.setAttribute('title', hidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
  });
}
