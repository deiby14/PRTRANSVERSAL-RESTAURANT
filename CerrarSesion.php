<?php
session_start();

// Conectar a la base de datos usando PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=tu_base_de_datos', 'tu_usuario', 'tu_contraseña');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supongamos que quieres registrar la hora de cierre de sesión
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