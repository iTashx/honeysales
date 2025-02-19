<?php
session_start();
$host = 'localhost';
$dbname = 'honeysalesdb';
$usernameDB = 'root';
$passwordDB = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usernameDB, $passwordDB, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["contraseña"]);

    if (!empty($username) && !empty($password)) {
        // Verificar si el usuario existe
        $stmt = $pdo->prepare("SELECT usuarioID, username, contraseña FROM USUARIO WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["contraseña"])) {
            $_SESSION["usuarioID"] = $user["usuarioID"];
            $_SESSION["username"] = $user["username"];
            
            // Redirigir al menú principal
            header("Location: ../pages/menu.html");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de inicio de sesión</title>
</head>
<body>
    <p><?php echo isset($error) ? $error : ''; ?></p>
    <a href="../index.html">Volver al login</a>
</body>
</html>
