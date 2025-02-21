<!-- recuperar_contraseña_token.php -->
<?php
require_once 'conexion.php'; // Conexión a la base de datos

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar si el token es válido
    $sql = "SELECT * FROM REC_CONTRASEÑA WHERE token = ? AND usado = 0 AND fecha_expiracion > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, date("Y-m-d H:i:s"));
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recuperacion = $result->fetch_assoc();
        $usuarioID = $recuperacion['usuarioID'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nueva_contraseña = password_hash($_POST['nueva_contraseña'], PASSWORD_BCRYPT);

            // Actualizar la contraseña del usuario
            $sql = "UPDATE USUARIO SET contraseña = ? WHERE usuarioID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nueva_contraseña, $usuarioID);
            $stmt->execute();

            // Marcar el token como usado
            $sql = "UPDATE REC_CONTRASEÑA SET usado = 1 WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();

            echo "Su contraseña ha sido actualizada con éxito.";
        }
    } else {
        echo "El enlace de recuperación ha expirado o ya ha sido usado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h2>Restablecer Contraseña</h2>
    <form method="POST">
        <label for="nueva_contraseña">Nueva Contraseña</label>
        <input type="password" id="nueva_contraseña" name="nueva_contraseña" placeholder="Ingrese su nueva contraseña" required>
        <button type="submit">Restablecer Contraseña</button>
    </form>
</body>
</html>
