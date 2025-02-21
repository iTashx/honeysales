<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'honeysalesdb';
$usernameDB = 'root';
$passwordDB = '';

$conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $productoID = $_GET['id'];

    $sql = "SELECT * FROM PRODUCTO WHERE productoID = $productoID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        echo json_encode($producto);
    } else {
        echo json_encode(null);
    }
}

$conn->close();
?>
