<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'nombre_base_datos';
$username = 'usuario_bd';
$password = 'contraseña_bd';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Obtener los productos
function obtenerProductos($pdo) {
    $sql = "SELECT productoID, nombre_prod, precio, stock FROM PRODUCTO";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$productos = obtenerProductos($pdo); // Llamar a la función para obtener los productos
?>
