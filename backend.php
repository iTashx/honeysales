<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'honeysalesdb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}

// Funciones para interactuar con la base de datos

// Obtener todos los productos
function obtenerProductos($pdo) {
    $stmt = $pdo->query("SELECT * FROM Producto");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Registrar un nuevo producto
function registrarProducto($pdo, $nombre, $precio, $sabor, $categoria, $stock) {
    $stmt = $pdo->prepare("INSERT INTO Producto (nombreProducto, precio, sabor, categoria, stock) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$nombre, $precio, $sabor, $categoria, $stock]);
}

// Modificar un producto existente
function modificarProducto($pdo, $productoID, $nombre, $precio, $sabor, $categoria, $stock) {
    $stmt = $pdo->prepare("UPDATE Producto SET nombreProducto = ?, precio = ?, sabor = ?, categoria = ?, stock = ? WHERE productoID = ?");
    return $stmt->execute([$nombre, $precio, $sabor, $categoria, $stock, $productoID]);
}

// Eliminar un producto
function eliminarProducto($pdo, $productoID) {
    $stmt = $pdo->prepare("DELETE FROM Producto WHERE productoID = ?");
    return $stmt->execute([$productoID]);
}

// Registrar una nueva venta
function registrarVenta($pdo, $vendedorID, $clienteID, $productos) {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO Venta (vendedorID, clienteID) VALUES (?, ?)");
        $stmt->execute([$vendedorID, $clienteID]);
        $ventaID = $pdo->lastInsertId();
        
        $stmtProducto = $pdo->prepare("INSERT INTO VentaProducto (ventaID, productoID, cantidad) VALUES (?, ?, ?)");
        foreach ($productos as $producto) {
            $stmtProducto->execute([$ventaID, $producto['productoID'], $producto['cantidad']]);
            
            // Actualizar inventario
            $stmtStock = $pdo->prepare("UPDATE Producto SET stock = stock - ? WHERE productoID = ?");
            $stmtStock->execute([$producto['cantidad'], $producto['productoID']]);
        }
        
        $pdo->commit();
        return $ventaID;
    } catch (Exception $e) {
        $pdo->rollBack();
        return "Error en la transacción: " . $e->getMessage();
    }
}

// Generar reporte de inventario
function reporteInventario($pdo) {
    $stmt = $pdo->query("SELECT * FROM Producto WHERE stock <= 10");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Generar reporte de ventas
function reporteVentas($pdo, $fechaInicio, $fechaFin) {
    $stmt = $pdo->prepare("SELECT * FROM Venta WHERE fechaVenta BETWEEN ? AND ?");
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Añadir usuario
function añadirUsuario($pdo, $nombre, $apellido, $correo, $contraseña) {
    $stmt = $pdo->prepare("INSERT INTO Usuario (nombre, apellido, correo, contraseña) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$nombre, $apellido, $correo, password_hash($contraseña, PASSWORD_BCRYPT)]);
}

// Modificar usuario
function modificarUsuario($pdo, $usuarioID, $nombre, $apellido, $correo) {
    $stmt = $pdo->prepare("UPDATE Usuario SET nombre = ?, apellido = ?, correo = ? WHERE usuarioID = ?");
    return $stmt->execute([$nombre, $apellido, $correo, $usuarioID]);
}

// Eliminar usuario
function eliminarUsuario($pdo, $usuarioID) {
    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE usuarioID = ?");
    return $stmt->execute([$usuarioID]);
}

// Archivo de prueba
include 'conexion.php';

// Intenta una consulta simple
try {
    $stmt = $pdo->query("SELECT * FROM USUARIO LIMIT 1");
    $result = $stmt->fetch();
    var_dump($result);
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>
