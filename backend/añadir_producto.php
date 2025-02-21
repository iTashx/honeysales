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

// Verificar si se han recibido los datos
if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['precio']) && isset($_POST['stock'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Consulta SQL para insertar el producto
    $sql = "INSERT INTO PRODUCTO (nombre_prod, descripcion, precio, stock) 
            VALUES ('$nombre', '$descripcion', $precio, $stock)";

    if ($conn->query($sql) === TRUE) {
        echo "Producto añadido correctamente";
    } else {
        echo "Error al añadir producto: " . $conn->error;
    }
} else {
    echo "Faltan datos";
}

// Cerrar la conexión
$conn->close();
?>
