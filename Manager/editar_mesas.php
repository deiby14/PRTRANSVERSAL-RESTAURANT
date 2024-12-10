<?php 
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar si se pasó el ID de la mesa a editar
if (isset($_GET['id'])) {
    $id_mesa = $_GET['id'];
    
    // Obtener los datos de la mesa
    $stmt = $con->prepare("SELECT id_mesa, capacidad, id_sala FROM mesas WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();
    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra la mesa, redirigir
    if (!$mesa) {
        $_SESSION['mensaje'] = "Mesa no encontrada.";
        header("Location: index.php");
        exit();
    }

    // Obtener las salas disponibles para seleccionar
    $stmt = $con->query("SELECT id_sala, nombre FROM salas");
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Actualizar mesa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $capacidad = $_POST['capacidad'];
    $id_sala = $_POST['id_sala'];

    // Validación de campos vacíos
    if (empty($capacidad) || empty($id_sala)) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: editar_mesas.php?id=$id_mesa");
        exit();
    }

    // Actualizar la mesa
    $stmt = $con->prepare("UPDATE mesas SET capacidad = :capacidad, id_sala = :id_sala WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
    $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Mesa actualizada correctamente.";
        header("Location: administrar.php");
        exit();
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la mesa.";
        header("Location: editar_mesas.php?id=$id_mesa");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mesa</title>
    <link rel="stylesheet" href="../CSS/styles.css">
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
<body id="bodyGen">
    <h1>Editar Mesa</h1>

    <form method="POST">
        <label for="capacidad">Capacidad:</label>
        <input type="number" id="capacidad" name="capacidad" value="<?= htmlspecialchars($mesa['capacidad']) ?>">
        <span id="error-capacidad" class="error"></span>


        <label for="id_sala">Sala:</label>
        <select name="id_sala" id="id_sala">
            <?php foreach ($salas as $sala): ?>
                <option value="<?= $sala['id_sala'] ?>" <?= $mesa['id_sala'] == $sala['id_sala'] ? 'selected' : '' ?>><?= htmlspecialchars($sala['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Actualizar Mesa</button>
    </form>

    <br><br>
    <a href="administrar.php" class="btn btn-secondary">Volver</a>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <p class="error"><?= $_SESSION['mensaje']; ?></p>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
</body>
<script src="../Js/validaeditarmesas.js"></script>
</html>
