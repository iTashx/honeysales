<?php
// Incluir la conexión a la base de datos
include '../backend/conexion.php';

// Verificar que se haya recibido un usuarioID
if (!isset($_GET['usuarioID'])) {
    die("Error: No se ha proporcionado el ID del usuario.");
}

// Recoger el ID del usuario desde la URL
$usuarioID = $_GET['usuarioID'];

// Consulta para obtener los datos del usuario
$query = "
    SELECT u.usuarioID, p.nombre, p.apellido, p.ci, u.username, p.email, r.nombre_rol
    FROM USUARIO u
    JOIN PERSONA p ON u.ci = p.ci
    JOIN USUARIO_ROL ur ON u.usuarioID = ur.usuarioID
    JOIN ROL r ON ur.rolID = r.rolID
    WHERE u.usuarioID = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$usuarioID]);

// Verificar si se encontró el usuario
$user = $stmt->fetch();

if (!$user) {
    die("Error: Usuario no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/usuarios.css">
    <script src="../js/usuarios.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="nav-logo-container">
            <img src="../assets/images/logo.png" alt="nav-logo" class="nav-logo">
        </div>
        <nav>
            <ul>
                <li><a href="../pages/menu.html">Menú</a></li>
                <li><a href="../pages/ventas.html">Ventas</a></li>
                <li><a href="../pages/inventario.html">Inventario</a></li>
                <li><a href="../pages/usuarios.html">Usuarios</a></li>
            </ul>
        </nav>
        <button class="logout">Cerrar sesión</button>
    </div>
    
    <div class="content">
        <h1>Editar Usuario</h1>
        <form action="procesar_editar_usuario.php" method="POST">
            <input type="hidden" name="usuarioID" value="<?php echo $user['usuarioID']; ?>">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $user['nombre']; ?>" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?php echo $user['apellido']; ?>" required>

            <label for="ci">Cédula:</label>
            <input type="text" id="ci" name="ci" value="<?php echo $user['ci']; ?>" required readonly>

            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password">

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="1" <?php echo ($user['nombre_rol'] == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                <option value="2" <?php echo ($user['nombre_rol'] == 'Vendedor') ? 'selected' : ''; ?>>Vendedor</option>
            </select>

            <button type="submit">Actualizar Usuario</button>
        </form>
    </div>
</body>
</html>
