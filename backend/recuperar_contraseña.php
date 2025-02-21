<!-- recuperar_contraseña.php -->
<?php
require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM USUARIO WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $usuarioID = $user['usuarioID'];

        // Generar un token único
        $token = bin2hex(random_bytes(32));
        $fecha_solicitud = date("Y-m-d H:i:s");
        $fecha_expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Insertar en la tabla de recuperación
        $sql = "INSERT INTO REC_CONTRASEÑA (usuarioID, token, fecha_solicitud, fecha_expiracion, usado) 
                VALUES (?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $usuarioID, $token, $fecha_solicitud, $fecha_expiracion);
        $stmt->execute();

        // Enviar correo electrónico con el enlace de recuperación
        $url_recuperacion = "http://localhost/recuperar_contraseña_token.php?token=" . $token;
        $subject = "Recuperación de Contraseña";
        $message = "Haga clic en el siguiente enlace para recuperar su contraseña: " . $url_recuperacion;
        mail($email, $subject, $message);

        echo "Se ha enviado un enlace a su correo electrónico.";
    } else {
        echo "El correo electrónico no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
</head>
<body>
    <h2>Recuperar Contraseña</h2>
    <form method="POST">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" placeholder="Ingrese su correo electrónico" required>
        <button type="submit">Enviar Enlace</button>
    </form>
</body>
</html>
