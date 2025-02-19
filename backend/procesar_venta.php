<?php

// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Obtener los datos JSON enviados desde el cliente
$ventaData = json_decode(file_get_contents('php://input'), true);

// Verificar que los datos se recibieron correctamente
if (!$ventaData) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos']);
    exit;
}

// Procesar la venta y realizar las operaciones necesarias...

// Responder con éxito si todo está bien
echo json_encode(['success' => true, 'reciboID' => 123]);  // Asegúrate de devolver el ID del recibo si es exitoso


// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos']);
    exit;
}

// Obtener los valores del objeto JSON
$clienteCI = $data['clienteCI'];
$vendedorID = $data['vendedorID'];
$tipoPago = $data['tipoPago'];
$productos = $data['productos'];

$fechaVenta = date('Y-m-d H:i:s'); // Fecha y hora actuales

// Conexión a la base de datos (ajusta según tu configuración)
$mysqli = new mysqli('localhost', 'root', '', 'nombre_de_base_de_datos');

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Iniciar transacción
$mysqli->begin_transaction();

try {
    // Insertar la venta en la tabla VENTA
    $stmt = $mysqli->prepare("INSERT INTO VENTA (fecha_venta, usuarioID, ci) VALUES (?, ?, ?)");
    $stmt->bind_param('sis', $fechaVenta, $vendedorID, $clienteCI);
    $stmt->execute();
    $ventaID = $stmt->insert_id;  // Obtener el ID de la venta insertada

    // Insertar los productos de la venta en la tabla VENTA_PRODUCTO
    $stmt = $mysqli->prepare("INSERT INTO VENTA_PRODUCTO (ventaID, productoID, cantidad_producto, precio_unitario) VALUES (?, ?, ?, ?)");
    foreach ($productos as $producto) {
        $stmt->bind_param('iiid', $ventaID, $producto['productoID'], $producto['cantidad'], $producto['precio_unitario']);
        $stmt->execute();
    }

    // Insertar el recibo en la tabla RECIBO
    $stmt = $mysqli->prepare("INSERT INTO RECIBO (ventaID, tipo_pago) VALUES (?, ?)");
    $stmt->bind_param('is', $ventaID, $tipoPago);
    $stmt->execute();
    $reciboID = $stmt->insert_id;

    // Confirmar la transacción
    $mysqli->commit();

    // Devolver respuesta al frontend
    echo json_encode(['success' => true, 'reciboID' => $reciboID]);
} catch (Exception $e) {
    // Si hay un error, revertir la transacción
    $mysqli->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $mysqli->close();
}
?>
