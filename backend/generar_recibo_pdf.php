<?php
require_once('tcpdf_include.php');

// Obtener el ID del recibo
$reciboID = $_GET['reciboID'];

// Conexión a la base de datos
$mysqli = new mysqli('localhost', 'root', '', 'nombre_de_base_de_datos');

if ($mysqli->connect_error) {
    die('Error de conexión a la base de datos');
}

// Obtener los datos del recibo
$stmt = $mysqli->prepare("SELECT V.fecha_venta, V.usuarioID, V.ci, R.tipo_pago
                          FROM VENTA V
                          JOIN RECIBO R ON V.ventaID = R.ventaID
                          WHERE R.reciboID = ?");
$stmt->bind_param('i', $reciboID);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();

// Obtener los productos de la venta
$stmt = $mysqli->prepare("SELECT P.nombre_prod, VP.cantidad_producto, VP.precio_unitario
                          FROM VENTA_PRODUCTO VP
                          JOIN PRODUCTO P ON VP.productoID = P.productoID
                          WHERE VP.ventaID = ?");
$stmt->bind_param('i', $venta['ventaID']);
$stmt->execute();
$productos = $stmt->get_result();

// Crear el PDF con TCPDF
$pdf = new TCPDF();
$pdf->AddPage();

// Título del recibo
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Recibo de Venta', 0, 1, 'C');

// Detalles de la venta
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(100, 10, 'Fecha de Venta: ' . $venta['fecha_venta']);
$pdf->Ln();
$pdf->Cell(100, 10, 'Cliente CI: ' . $venta['ci']);
$pdf->Ln();
$pdf->Cell(100, 10, 'Vendedor ID: ' . $venta['usuarioID']);
$pdf->Ln();
$pdf->Cell(100, 10, 'Tipo de Pago: ' . $venta['tipo_pago']);
$pdf->Ln();

// Tabla de productos
$pdf->Cell(0, 10, 'Productos Comprados:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(60, 10, 'Producto', 1);
$pdf->Cell(30, 10, 'Cantidad', 1);
$pdf->Cell(30, 10, 'Precio Unitario', 1);
$pdf->Ln();

while ($producto = $productos->fetch_assoc()) {
    $pdf->Cell(60, 10, $producto['nombre_prod'], 1);
    $pdf->Cell(30, 10, $producto['cantidad_producto'], 1);
    $pdf->Cell(30, 10, '$' . number_format($producto['precio_unitario'], 2), 1);
    $pdf->Ln();
}

// Salvar el PDF
$pdf->Output('recibo_' . $reciboID . '.pdf', 'I');

// Cerrar la conexión
$mysqli->close();
?>
