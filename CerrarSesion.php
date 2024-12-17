<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname= db_restaurante', 'db_restaurante', 'Agustin51');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE usuarios SET ultima_sesion = NOW() WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);

} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}

// Destruir la sesión
session_destroy();

// Redirigir al usuario
header("Location: index.php");
exit;
?>