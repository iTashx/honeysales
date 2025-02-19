<?php
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

// Obtener todos los usuarios con contraseñas sin encriptar
$stmt = $pdo->query("SELECT usuarioID, contraseña FROM USUARIO");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    $hashedPassword = password_hash($user['contraseña'], PASSWORD_DEFAULT);

    // Actualizar la contraseña en la base de datos
    $updateStmt = $pdo->prepare("UPDATE USUARIO SET contraseña = ? WHERE usuarioID = ?");
    $updateStmt->execute([$hashedPassword, $user['usuarioID']]);
}

echo "Las contraseñas han sido encriptadas con éxito.";
?>
