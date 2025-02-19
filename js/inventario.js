document.addEventListener("DOMContentLoaded", function() {
    cargarProductos();
});

function cargarProductos() {
    fetch('obtener_productos.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("product-table-body");
            tbody.innerHTML = "";

            data.forEach(producto => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${producto.productoID}</td>
                    <td>${producto.nombre_prod}</td>
                    <td>${producto.descripcion}</td>
                    <td>$${producto.precio}</td>
                    <td>${producto.stock}</td>
                    <td>
                        <button onclick="editarProducto(${producto.productoID})">Editar</button>
                        <button onclick="eliminarProducto(${producto.productoID})">Eliminar</button>
                    </td>
                `;
                tbody.appendChild(fila);
            });
        })
        .catch(error => console.error("Error al obtener productos:", error));
}

function eliminarProducto(id) {
    if (confirm("¿Seguro que deseas eliminar este producto?")) {
        fetch('eliminar_producto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `productoID=${id}`
        })
        .then(response => response.json())
        .then(data => {
            alert(data.mensaje || data.error);
            cargarProductos();
        })
        .catch(error => console.error("Error al eliminar producto:", error));
    }
}

function editarProducto(id) {
    window.location.href = `editar_producto.html?productoID=${id}`;
}
