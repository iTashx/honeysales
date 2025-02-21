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

// Consulta SQL para obtener los productos
$sql = "SELECT productoID, nombre_prod, descripcion, precio, stock FROM PRODUCTO";
$result = $conn->query($sql);

$productos = [];

if ($result->num_rows > 0) {
    // Recorrer los resultados y guardarlos en un array
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Cerrar la conexión
$conn->close();

// Retornar los productos en formato JSON
echo json_encode($productos);
?>
