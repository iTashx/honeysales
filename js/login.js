function togglePassword() {
    const passwordInput = document.getElementById("contraseña");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
}
