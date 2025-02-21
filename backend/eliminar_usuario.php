<?php
include 'conexion.php';

if (isset($_GET['usuarioID'])) {
    $usuarioID = $_GET['usuarioID'];

    // Eliminar usuario de la tabla USUARIO_ROL
    $query = "DELETE FROM USUARIO_ROL WHERE usuarioID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $usuarioID);
    $stmt->execute();

    // Eliminar usuario de la tabla USUARIO
    $query = "DELETE FROM USUARIO WHERE usuarioID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $usuarioID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Usuario eliminado";
    } else {
        echo "Error al eliminar el usuario";
    }

    $stmt->close();
}
$conn->close();
?>
