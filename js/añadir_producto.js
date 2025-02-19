document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("productoForm");

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        
        const nombre = document.getElementById("nombre").value.trim();
        const descripcion = document.getElementById("descripcion").value.trim();
        const precio = document.getElementById("precio").value.trim();
        const stock = document.getElementById("stock").value.trim();
        
        if (nombre === "" || precio === "" || stock === "") {
            alert("Por favor, complete los campos obligatorios.");
            return;
        }
        
        if (isNaN(precio) || parseFloat(precio) <= 0) {
            alert("El precio debe ser un número válido y mayor a 0.");
            return;
        }
        
        if (isNaN(stock) || parseInt(stock) < 0) {
            alert("El stock debe ser un número válido y mayor o igual a 0.");
            return;
        }
        
        const formData = new FormData(form);
        
        fetch("procesar_producto.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Producto añadido correctamente.");
                form.reset();
            } else {
                alert("Error al añadir producto: " + data.message);
            }
        })
        .catch(error => {
            alert("Ocurrió un error en la solicitud.");
            console.error(error);
        });
    });
});
