<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'honeysalesdb';
$usernameDB = 'root';
$passwordDB = '';

$conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

// Verificación de la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['productoID'])) {
    $productoID = $_GET['productoID'];

    // Consulta SQL para eliminar el producto
    $sql = "DELETE FROM PRODUCTO WHERE productoID = $productoID";

    if ($conn->query($sql) === TRUE) {
        echo "Producto eliminado";
    } else {
        echo "Error al eliminar el producto: " . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>
