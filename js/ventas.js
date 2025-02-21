// Variables para almacenar el carrito y el total
let carrito = [];
let subtotal = 0;

// Función para obtener los productos y mostrarlos en el HTML
function cargarProductos() {
    fetch('../backend/obtener_productos.php')
        .then(response => response.json())
        .then(productos => {
            const productosLista = document.getElementById('productos-lista');
            if (!productosLista) {
                console.error("El elemento 'productos-lista' no se encontró en el DOM");
                return;
            }
            productosLista.innerHTML = ''; // Limpiar la lista actual

            productos.forEach(producto => {
                const productoDiv = document.createElement('div');
                productoDiv.classList.add('producto-item');
                productoDiv.setAttribute('data-id', producto.productoID);
                productoDiv.innerHTML = `
                    <h3>${producto.nombre_prod}</h3>
                    <p>Precio: \$${producto.precio}</p>
                    <p>Stock: ${producto.stock}</p>
                    <button class='agregar-carrito' data-id='${producto.productoID}' data-precio='${producto.precio}' data-nombre='${producto.nombre_prod}'>Agregar al carrito</button>
                `;
                productosLista.appendChild(productoDiv);
            });

            // Agregar evento para los botones de "Agregar al carrito"
            const botonesAgregar = document.querySelectorAll('.agregar-carrito');
            botonesAgregar.forEach(button => {
                button.addEventListener('click', agregarAlCarrito);
            });
        })
        .catch(error => console.error('Error al cargar los productos:', error));
}

// Función para agregar productos al carrito
function agregarAlCarrito(event) {
    const button = event.target;
    const productoID = button.getAttribute('data-id');
    const nombre = button.getAttribute('data-nombre');
    const precio = parseFloat(button.getAttribute('data-precio'));
    const stock = parseInt(button.parentElement.querySelector('p:nth-of-type(2)').textContent.split(': ')[1]); // Obtener stock desde el DOM

    const productoExistente = carrito.find(item => item.productoID == productoID);

    if (productoExistente) {
        if (productoExistente.cantidad < stock) {
            productoExistente.cantidad++;
        } else {
            alert('No hay suficiente stock disponible para agregar más de este producto.');
        }
    } else {
        if (stock > 0) {
            carrito.push({
                productoID: productoID,
                nombre: nombre,
                precio: precio,
                cantidad: 1
            });
        } else {
            alert('Este producto está agotado.');
        }
    }

    actualizarCarrito();
}



// Función para actualizar la vista del carrito
function actualizarCarrito() {
    const carritoLista = document.getElementById('carrito-lista');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');

    carritoLista.innerHTML = ''; // Limpiar el carrito actual
    subtotal = 0; // Reiniciar el subtotal

    // Agregar los productos del carrito a la lista
    carrito.forEach(item => {
        const itemLista = document.createElement('li');
        itemLista.innerHTML = `
            ${item.nombre} - Cantidad: ${item.cantidad} - \$${item.precio * item.cantidad}
            <button class="eliminar-producto" data-id="${item.productoID}">Eliminar</button>
        `;
        carritoLista.appendChild(itemLista);

        // Actualizar el subtotal
        subtotal += item.precio * item.cantidad;
    });

    // Actualizar el subtotal y el total
    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    totalElement.textContent = `$${(subtotal).toFixed(2)}`;
}

// Función para eliminar productos del carrito
function eliminarProducto(event) {
    const button = event.target;
    const productoID = button.getAttribute('data-id');

    // Eliminar el producto del carrito
    carrito = carrito.filter(item => item.productoID != productoID);

    // Actualizar la vista del carrito
    actualizarCarrito();
}

// Generar recibo PDF
function generarReciboPDF(reciboID, productos, tipoPago) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Título del recibo
    doc.setFontSize(16);
    doc.text('Recibo de Venta', 105, 20, { align: 'center' });

    // Fecha, tipo de pago y detalles de la venta
    const fecha = new Date().toLocaleString();
    doc.setFontSize(12);
    doc.text(`Fecha de Venta: ${fecha}`, 20, 40);
    doc.text(`Tipo de Pago: ${tipoPago}`, 20, 50);
    doc.text(`ID del recibo: ${reciboID}`, 20, 100);

    // Tabla de productos
    doc.text('Productos Comprados:', 20, 60);
    doc.text('Producto', 20, 70);
    doc.text('Cantidad', 100, 70);
    doc.text('Precio Unitario', 140, 70);
    doc.setFontSize(10);

    let y = 80;
    productos.forEach(producto => {
        const precio = producto.precio_unitario ? producto.precio_unitario.toFixed(2) : "0.00";
        doc.text(producto.nombre, 20, y);
        doc.text(producto.cantidad.toString(), 100, y);
        doc.text('$' + precio, 140, y);
        y += 10;
    });

    // Mostrar PDF en una nueva ventana
    window.open(doc.output('bloburl'), '_blank');

    // Generar el PDF y abrirlo doc.save(`recibo_${reciboID}.pdf`);
}

// Checkout
document.getElementById('checkout').addEventListener('click', function() {
    if (carrito.length === 0) {
        alert('El carrito está vacío. No se puede realizar la venta.');
        return;
    }

    // Obtener datos del cliente desde los campos de entrada
    const clienteNombre = document.getElementById('cliente-nombre').value;
    const clienteApellido = document.getElementById('cliente-apellido').value;
    const clienteCI = document.getElementById('cliente-ci').value;
    const tipoPago = document.getElementById('tipo-pago').value;

    if (!clienteNombre || !clienteApellido || !clienteCI || !tipoPago) {
        alert('Por favor, complete todos los campos del cliente y seleccione el tipo de pago.');
        return;
    }

    // Recolectar los datos del carrito
    const productosCarrito = carrito.map(item => ({
        productoID: item.productoID,
        cantidad: item.cantidad,
        precio_unitario: item.precio
    }));

    // Obtener datos del cliente y del vendedor (esto puede venir de campos ocultos o de una sesión)
    // const clienteCI = '123456789';  // Debes obtener el CI del cliente
    const vendedorID = 1;           // Debes obtener el ID del vendedor
    // const tipoPago = 'Efectivo';    // O 'Tarjeta' o 'Transferencia', dependiendo de la opción seleccionada

    // Crear objeto con los datos para enviar al servidor
    const ventaData = {
        clienteNombre: clienteNombre,
        clienteApellido: clienteApellido,
        clienteCI: clienteCI,
        vendedorID: vendedorID,
        tipoPago: tipoPago,
        productos: productosCarrito
    };

    // Enviar los datos al servidor mediante Fetch API
    console.log('Datos a enviar:', ventaData);


    fetch('../backend/procesar_venta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(ventaData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Venta realizada con éxito');
            generarReciboPDF(data.reciboID, carrito.map(item => ({
                nombre: item.nombre,
                cantidad: item.cantidad,
                precio_unitario: item.precio
            })), tipoPago);
            
            // Vaciar el carrito y actualizar la vista
            carrito = [];
            actualizarCarrito();
    
            // Recargar los productos para reflejar el stock actualizado
            cargarProductos();
        } else {
            alert('Error al procesar la venta: ' + data.error);
        }
    })
    
    .catch(error => {
        console.error('Error en el proceso de venta:', error);
        alert('Hubo un error al procesar la venta.');
    });
});

// Asegurarnos de que el DOM esté completamente cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', function () {
    cargarProductos();
});
