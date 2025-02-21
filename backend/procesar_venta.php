<?php

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Obtener los datos JSON enviados desde el cliente
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los datos se recibieron correctamente
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos']);
    exit;
}

// Extraer valores del objeto JSON
$clienteCI = $data['clienteCI'] ?? null;
$vendedorID = $data['vendedorID'] ?? null;
$tipoPago = $data['tipoPago'] ?? null;
$productos = $data['productos'] ?? [];

if (!$clienteCI || !$vendedorID || !$tipoPago || empty($productos)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$fechaVenta = date('Y-m-d H:i:s'); // Fecha y hora actuales

// Conexión a la base de datos
$mysqli = new mysqli('localhost', 'root', '', 'honeysalesdb');

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Iniciar transacción
$mysqli->begin_transaction();

try {
    // Verificar si el cliente ya existe en la base de datos
    $stmt = $mysqli->prepare("SELECT nombre, apellido FROM PERSONA WHERE ci = ?");
    $stmt->bind_param('s', $clienteCI);
    $stmt->execute();
    $stmt->bind_result($nombreCliente, $apellidoCliente);
    
    // Si el cliente no existe, insertar los datos del cliente
    if (!$stmt->fetch()) {
        // Datos del cliente enviados en la solicitud (ahora usamos clienteNombre y clienteApellido)
        $nombreCliente = $data['clienteNombre'] ?? null;
        $apellidoCliente = $data['clienteApellido'] ?? null;

        if (!$nombreCliente || !$apellidoCliente) {
            throw new Exception('Nombre o apellido del cliente faltantes');
        }

        // Insertar el cliente en la tabla PERSONA
        $stmt->close(); // Cerrar la consulta anterior
        $stmt = $mysqli->prepare("INSERT INTO PERSONA (ci, nombre, apellido) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $clienteCI, $nombreCliente, $apellidoCliente);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al insertar la persona en la base de datos');
        }

        // Insertar el cliente en la tabla CLIENTE
        $stmt->close(); // Cerrar la consulta anterior
        $stmt = $mysqli->prepare("INSERT INTO CLIENTE (ci) VALUES (?)");
        $stmt->bind_param('s', $clienteCI);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al insertar el cliente en la base de datos');
        }
    }

    $stmt->close(); // Cerrar la consulta de verificación

    // Insertar la venta en la tabla VENTA
    $stmt = $mysqli->prepare("INSERT INTO VENTA (fecha_venta, usuarioID, ci, nombre, apellido) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sisss', $fechaVenta, $vendedorID, $clienteCI, $nombreCliente, $apellidoCliente);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al registrar la venta');
    }
    
    $ventaID = $stmt->insert_id;
    $stmt->close(); // Cerrar la consulta

    // Insertar los productos de la venta en la tabla VENTA_PRODUCTO
    $stmt = $mysqli->prepare("INSERT INTO VENTA_PRODUCTO (ventaID, productoID, cantidad_producto, precio_unitario) VALUES (?, ?, ?, ?)");

    foreach ($productos as $producto) {
        if (!isset($producto['productoID'], $producto['cantidad'], $producto['precio_unitario'])) {
            throw new Exception('Error en los datos del producto');
        }

        $stmt->bind_param('iiid', $ventaID, $producto['productoID'], $producto['cantidad'], $producto['precio_unitario']);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al insertar un producto en la venta');
        }

        // Restar la cantidad comprada del stock
        $stmtActualizarInventario = $mysqli->prepare("UPDATE PRODUCTO SET stock = stock - ? WHERE productoID = ?");
        $stmtActualizarInventario->bind_param('ii', $producto['cantidad'], $producto['productoID']);
        
        if (!$stmtActualizarInventario->execute()) {
            throw new Exception('Error al actualizar el stock del producto');
        }

        $stmtActualizarInventario->close(); // Cerrar la consulta de actualización
    }

    $stmt->close(); // Cerrar la consulta de inserción

    // Insertar el recibo en la tabla RECIBO
    $stmt = $mysqli->prepare("INSERT INTO RECIBO (ventaID, tipo_pago) VALUES (?, ?)");
    $stmt->bind_param('is', $ventaID, $tipoPago);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al generar el recibo');
    }

    $reciboID = $stmt->insert_id;
    $stmt->close();

    // Confirmar la transacción
    $mysqli->commit();

    // Devolver respuesta al frontend
    echo json_encode(['success' => true, 'reciboID' => $reciboID]);

} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $mysqli->close();
}
