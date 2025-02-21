// Función para cargar los productos desde el servidor
function cargarProductos() {
    fetch('../backend/obtener_productos_inv.php')
        .then(response => response.json())
        .then(data => {
            const tabla = document.getElementById('product-table-body');
            tabla.innerHTML = ''; // Limpiar la tabla antes de cargar los nuevos datos

            // Agregar filas de productos a la tabla
            data.forEach(producto => {
                const fila = document.createElement('tr');
                
                // Crear celdas para cada producto
                fila.innerHTML = `
                    <td>${producto.productoID}</td>
                    <td>${producto.nombre_prod}</td>
                    <td>${producto.descripcion}</td>
                    <td>${producto.precio}</td>
                    <td>${producto.stock}</td>
                    <td>
                        <button class="edit-btn" data-id="${producto.productoID}">Editar</button>
                        <button class="delete-btn" data-id="${producto.productoID}">Eliminar</button>
                    </td>
                `;
                tabla.appendChild(fila);
            });
        })
        .catch(error => console.error('Error al cargar los productos:', error));
}

let productos = []; // Declarar la variable antes de usarla
document.addEventListener('DOMContentLoaded', function() {

    // Obtener el cuerpo de la tabla donde se agregarán los productos
    const tableBody = document.getElementById('product-table-body');
    tableBody.innerHTML = ''; // Limpiar tabla antes de agregar productos

    // Iterar sobre los productos y agregar las filas correspondientes
    productos.forEach(producto => {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${producto.id}</td>
            <td>${producto.nombre}</td>
            <td>${producto.descripcion}</td>
            <td>${producto.precio}</td>
            <td>${producto.stock}</td>
            <td>
                <button class="edit-btn" data-id="${producto.id}">Editar</button>
                <button class="delete-btn" data-id="${producto.id}">Eliminar</button>
            </td>
        `;

        // Agregar la fila al cuerpo de la tabla
        tableBody.appendChild(row);
    });
});

// Función para manejar el clic en "Editar"
document.addEventListener('click', function(event) {
    if (event.target && event.target.classList.contains('edit-btn')) {
        const productoID = event.target.getAttribute('data-id');
        // Redirigir a la página de edición con el productoID
        window.location.href = `editar_producto.html?id=${productoID}`;
    }
});

// Función para redirigir a la página de editar producto
function editarProducto(productoID) {
    window.location.href = `editar_producto.html?productoID=${productoID}`;
}

// Función para eliminar un producto
function eliminarProducto(productoID) {
    const confirmar = confirm('¿Estás seguro de eliminar este producto?');
    if (confirmar) {
        fetch(`../backend/eliminar_producto.php?productoID=${productoID}`, {
            method: 'GET',
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'Producto eliminado') {
                alert('Producto eliminado correctamente');
                cargarProductos(); // Recargar la lista de productos
            } else {
                alert('Error al eliminar el producto');
            }
        })
        .catch(error => console.error('Error al eliminar el producto:', error));
    }
}

// Cargar los productos al iniciar la página
window.onload = cargarProductos;
