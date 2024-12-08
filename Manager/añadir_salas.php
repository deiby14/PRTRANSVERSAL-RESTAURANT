<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

$error = null;
$mensaje = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con->beginTransaction(); // Iniciar la transacción

    try {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $capacidad = $_POST['capacidad'];

        // Insertar la nueva sala
        $stmt = $con->prepare("INSERT INTO salas (nombre, capacidad) VALUES (?, ?)");
        $stmt->execute([$nombre, $capacidad]);

        $con->commit(); // Confirmar transacción
        $mensaje = 'Sala añadida correctamente.';
    } catch (PDOException $e) {
        $con->rollBack(); // Deshacer cambios si ocurre un error
        $error = 'Error al añadir la sala: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Sala</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .mensaje {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Añadir Sala</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($mensaje): ?>
        <p style="color:green;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nombre">Nombre de la Sala:</label>
        <input type="text" name="nombre" required><br><br>
        <label for="capacidad">Capacidad:</label>
        <input type="number" name="capacidad" required><br><br>

        <button type="submit">Añadir Sala</button>
    </form>

    <br>
    <a href="administrar.php">Volver</a>
</body>
</html>
