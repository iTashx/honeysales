// Función para obtener parámetros de la URL
function getURLParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Cargar el producto que se va a editar
const productoID = getURLParameter('id');

if (productoID) {
    fetch(`../backend/obtener_producto_edit.php?id=${productoID}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById('productoID').value = productoID; // Guardar el ID en el campo oculto
                document.getElementById('nombre').value = data.nombre_prod;
                document.getElementById('descripcion').value = data.descripcion;
                document.getElementById('precio').value = data.precio;
                document.getElementById('stock').value = data.stock;
            } else {
                alert('Producto no encontrado');
            }
        })
        .catch(error => console.error('Error al obtener el producto:', error));
}

// Enviar los cambios al servidor
document.getElementById('edit-product-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir que se recargue la página

    const productoID = document.getElementById("productoID").value; // Obtener el ID correctamente
    const nombre = document.getElementById('nombre').value;
    const descripcion = document.getElementById('descripcion').value;
    const precio = document.getElementById('precio').value;
    const stock = document.getElementById('stock').value;

    console.log("Datos enviados:", { productoID, nombre, descripcion, precio, stock }); // Verifica en consola

    fetch('../backend/actualizar_producto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${productoID}&nombre=${nombre}&descripcion=${descripcion}&precio=${precio}&stock=${stock}`
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert(data); // Mostrar la respuesta del servidor
        if (data === 'Producto actualizado correctamente') {
            window.location.href = 'inventario.html'; // Redirigir al inventario
        }
    })
    .catch(error => console.error('Error al actualizar producto:', error));
});
