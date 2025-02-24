// En el archivo login.js
function togglePassword() {
    const passwordInput = document.querySelector('.password-container input');
    const eyeIcon = document.querySelector('.eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.style.opacity = '0.7';
    } else {
        passwordInput.type = 'password';
        eyeIcon.style.opacity = '1';
    }
}
