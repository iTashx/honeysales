<?php
// Incluir la conexión a la base de datos
include '../backend/conexion.php';

// Verificar que los datos del formulario han sido enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Encriptar la contraseña antes de guardarla
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Iniciar una transacción
        $pdo->beginTransaction();

        // Insertar los datos en la tabla PERSONA
        $personaQuery = "INSERT INTO PERSONA (ci, nombre, apellido, email) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($personaQuery);
        $stmt->execute([$ci, $nombre, $apellido, $email]);

        // Insertar los datos en la tabla USUARIO
        $usuarioQuery = "INSERT INTO USUARIO (ci, username, contraseña) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($usuarioQuery);
        $stmt->execute([$ci, $username, $hashed_password]);

        // Obtener el usuarioID del último usuario insertado
        $usuarioID = $pdo->lastInsertId();

        // Insertar en la tabla USUARIO_ROL
        $rolQuery = "INSERT INTO USUARIO_ROL (usuarioID, rolID) VALUES (?, ?)";
        $stmt = $pdo->prepare($rolQuery);
        $stmt->execute([$usuarioID, $rol]);

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir a la página de gestión de usuarios con un mensaje de éxito
        header("Location: usuarios.html?success=1");
        exit();

    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $pdo->rollBack();
        die("Error al añadir el usuario: " . $e->getMessage());
    }
}
?>
