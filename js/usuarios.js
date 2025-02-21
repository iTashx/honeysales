function editarUsuario(usuarioID) {
    // Redirigir a la página de edición de usuario
    window.location.href = `editar_usuario.html?usuarioID=${usuarioID}`;
}

function eliminarUsuario(usuarioID) {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        // Hacer la solicitud a PHP para eliminar el usuario
        fetch(`../backend/eliminar_usuario.php?usuarioID=${usuarioID}`, {
            method: 'GET'
        }).then(response => {
            if (response.ok) {
                alert('Usuario eliminado');
                location.reload(); // Recargar la página para reflejar el cambio
            } else {
                alert('Error al eliminar el usuario');
            }
        });
    }
}

// Cargar los usuarios cuando la página cargue
window.onload = function() {
    fetchUsuarios();
};

function fetchUsuarios() {
    fetch('../backend/obtener_usuarios.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('users-table-body').innerHTML = data;
        })
        .catch(error => console.error('Error al cargar usuarios:', error));
}

function editUser(usuarioID) {
    // Redirigir a la página de edición de usuario
    window.location.href = `../backend/editar_usuario.php?usuarioID=${usuarioID}`;
}

function deleteUser(usuarioID) {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        // Hacer la solicitud a PHP para eliminar el usuario
        fetch(`../backend/eliminar_usuario.php?usuarioID=${usuarioID}`, {
            method: 'GET'
        }).then(response => {
            if (response.ok) {
                alert('Usuario eliminado');
                fetchUsuarios(); // Recargar la lista de usuarios
            } else {
                alert('Error al eliminar el usuario');
            }
        });
    }
}