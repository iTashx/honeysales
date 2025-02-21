<?php
// Incluir la conexión a la base de datos
include '../backend/conexion.php';

// Verificar que los datos del formulario han sido enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $usuarioID = $_POST['usuarioID'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Verificar si se ha ingresado una nueva contraseña
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Si no se ingresa una nueva contraseña, mantener la actual
        $hashed_password = null;
    }

    try {
        // Iniciar una transacción
        $pdo->beginTransaction();

        // Actualizar los datos de la tabla PERSONA
        $personaQuery = "UPDATE PERSONA SET nombre = ?, apellido = ?, email = ? WHERE ci = ?";
        $stmt = $pdo->prepare($personaQuery);
        $stmt->execute([$nombre, $apellido, $email, $ci]);

        // Actualizar los datos de la tabla USUARIO
        if ($hashed_password) {
            $usuarioQuery = "UPDATE USUARIO SET username = ?, contraseña = ? WHERE usuarioID = ?";
            $stmt = $pdo->prepare($usuarioQuery);
            $stmt->execute([$username, $hashed_password, $usuarioID]);
        } else {
            $usuarioQuery = "UPDATE USUARIO SET username = ? WHERE usuarioID = ?";
            $stmt = $pdo->prepare($usuarioQuery);
            $stmt->execute([$username, $usuarioID]);
        }

        // Actualizar el rol del usuario
        $rolQuery = "UPDATE USUARIO_ROL SET rolID = ? WHERE usuarioID = ?";
        $stmt = $pdo->prepare($rolQuery);
        $stmt->execute([$rol, $usuarioID]);

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir a la página de gestión de usuarios con un mensaje de éxito
        header("Location: ../pages/usuarios.html?success=1");
        exit();

    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $pdo->rollBack();
        die("Error al actualizar el usuario: " . $e->getMessage());
    }
}
?>
