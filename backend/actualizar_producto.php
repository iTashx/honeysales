<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'honeysalesdb';
$usernameDB = 'root';
$passwordDB = '';

$conn = new mysqli($host, $usernameDB, $passwordDB, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qué datos llegan
    print_r($_POST); // Agrega esto temporalmente para depuración

    if (isset($_POST["id"], $_POST["nombre"], $_POST["descripcion"], $_POST["precio"], $_POST["stock"])) {
        $productoID = $_POST["id"]; // Cambiado de productoID a id
        $nombre = $_POST["nombre"];
        $descripcion = $_POST["descripcion"];
        $precio = $_POST["precio"];
        $stock = $_POST["stock"];

        // Depuración: Verificar valores
        error_log("ID: $productoID, Nombre: $nombre, Descripción: $descripcion, Precio: $precio, Stock: $stock");

        $sql = "UPDATE PRODUCTO SET nombre_prod = ?, descripcion = ?, precio = ?, stock = ? WHERE productoID = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $productoID);

        if ($stmt->execute()) {
            echo "Producto actualizado correctamente";
        } else {
            echo "Error al actualizar el producto: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Faltan datos en la solicitud.";
    }
} else {
    echo "Método de solicitud no permitido.";
}
?>
