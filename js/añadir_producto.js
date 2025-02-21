// Función para manejar el envío del formulario
document.getElementById('add-product-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir que se recargue la página

    // Obtener los datos del formulario
    const nombre = document.getElementById('nombre').value;
    const descripcion = document.getElementById('descripcion').value;
    const precio = document.getElementById('precio').value;
    const stock = document.getElementById('stock').value;

    // Enviar los datos al servidor
    fetch('../backend/añadir_producto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `nombre=${nombre}&descripcion=${descripcion}&precio=${precio}&stock=${stock}`
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Mostrar la respuesta del servidor
        if (data === 'Producto añadido correctamente') {
            window.location.href = 'inventario.html'; // Redirigir al inventario
        }
    })
    .catch(error => console.error('Error al añadir producto:', error));
});
