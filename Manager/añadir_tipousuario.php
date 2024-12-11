<?php
session_start();

// Inicializar el array de tipos de usuario si no existe
if (!isset($_SESSION['tipos_usuario'])) {
    $_SESSION['tipos_usuario'] = [
        'Administrador',
        'Camarero',
        'Cocinero',
        // Puedes añadir más tipos de usuario aquí
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevo_tipo = $_POST['nuevo_tipo'];

    // Añadir el nuevo tipo de usuario al array de sesión
    if (!in_array($nuevo_tipo, $_SESSION['tipos_usuario'])) {
        $_SESSION['tipos_usuario'][] = $nuevo_tipo;
        echo "Nuevo tipo de usuario añadido con éxito.";
    } else {
        echo "El tipo de usuario ya existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Tipo de Usuario</title>
    <link rel="stylesheet" href="../CSS/crud.css">
</head>
<body>
    <h1>Añadir Tipo de Usuario</h1>
    <form method="POST" action="">
        <label for="nuevo_tipo">Nuevo Tipo de Usuario:</label>
        <input type="text" id="nuevo_tipo" name="nuevo_tipo" required>
        <button type="submit">Añadir</button>
    </form>
    <a href="trabajadores.php">Volver a Gestión de Trabajadores</a>

    <h2>Tipos de Usuario Existentes</h2>
    <ul>
        <?php foreach ($_SESSION['tipos_usuario'] as $tipo): ?>
            <li><?= htmlspecialchars($tipo) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html> 