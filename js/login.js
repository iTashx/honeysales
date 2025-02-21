// En el archivo login.js
function togglePassword() {
    var passwordField = document.getElementById("contraseña");
    var type = passwordField.type === "password" ? "text" : "password";
    passwordField.type = type;
}
