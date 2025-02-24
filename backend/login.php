<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'honeysalesdb';
$usernameDB = 'root';
$passwordDB = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usernameDB, $passwordDB, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
        $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

        if (!empty($username) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT usuarioID, username, contraseña FROM USUARIO WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["contraseña"])) {
                $_SESSION["usuarioID"] = $user["usuarioID"];
                $_SESSION["username"] = $user["username"];
                
                header("Location: ../pages/menu.html");
                exit();
            } else {
                header("Location: ../pages/login.html?error=invalid");
                exit();
            }
        } else {
            header("Location: ../pages/login.html?error=empty");
            exit();
        }
    }
} catch (PDOException $e) {
    header("Location: ../pages/login.html?error=database");
    exit();
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
