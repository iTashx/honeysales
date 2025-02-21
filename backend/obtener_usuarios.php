<?php
// Incluir la conexión a la base de datos
include '../backend/conexion.php'; 

// Consulta para obtener los usuarios con su rol
$query = "
    SELECT u.usuarioID, p.nombre, p.apellido, p.ci, u.username, p.email, r.nombre_rol 
    FROM USUARIO u
    JOIN PERSONA p ON u.ci = p.ci
    JOIN USUARIO_ROL ur ON u.usuarioID = ur.usuarioID
    JOIN ROL r ON ur.rolID = r.rolID
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Generar el HTML para cada fila de la tabla
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['usuarioID']}</td>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>{$row['apellido']}</td>";
        echo "<td>{$row['ci']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['nombre_rol']}</td>";
        echo "<td>
                <button onclick='editUser({$row['usuarioID']})'>Editar</button>
                <button onclick='deleteUser({$row['usuarioID']})'>Eliminar</button>
              </td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

?>
