<?php 
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); 

if (isset($_GET['id'])) {
    $id_sala = $_GET['id'];
    
    // Obtener los datos de la sala
    $stmt = $con->prepare("SELECT id_sala, nombre, capacidad, imagen FROM salas WHERE id_sala = :id_sala");
    $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
    $stmt->execute();
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sala) {
        $_SESSION['mensaje'] = "Sala no encontrada.";
        header("Location: administrar.php");
        exit();
    }
}

// Actualizar sala
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $capacidad = $_POST['capacidad'];
    $imagen = $_FILES['imagen'] ?? null;

    // Validación de campos vacíos
    if (empty($nombre) || empty($capacidad)) {
        $_SESSION['mensaje'] = "Los campos no pueden estar vacíos.";
        header("Location: editar_salas.php?id=$id_sala");
        exit();
    }

    // Procesar la imagen si se ha subido una nueva
    $imagenRuta = $sala['imagen'];
    if ($imagen && $imagen['tmp_name']) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); 
        }

        $targetFile = $targetDir . basename($imagen["name"]);
        if (move_uploaded_file($imagen["tmp_name"], $targetFile)) {
            $imagenRuta = $targetFile;
        } else {
            $_SESSION['mensaje'] = "Error al subir la imagen.";
            header("Location: editar_salas.php?id=$id_sala");
            exit();
        }
    }

    // Actualizar la sala
    try {
        $stmt = $con->prepare("UPDATE salas SET nombre = :nombre, capacidad = :capacidad, imagen = :imagen WHERE id_sala = :id_sala");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $imagenRuta);
        $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header("Location: administrar.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al actualizar la sala.";
            header("Location: editar_salas.php?id=$id_sala");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al actualizar la sala: " . $e->getMessage();
        header("Location: editar_salas.php?id=$id_sala");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sala</title>
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
    <h1>Editar Sala</h1>

    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre de la sala:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($sala['nombre']) ?>" >
        <span id="error-nombre" class="error"></span>


        <label for="capacidad">Capacidad:</label>
        <input type="number" id="capacidad" name="capacidad" value="<?= htmlspecialchars($sala['capacidad']) ?>">
        <span id="error-capacidad" class="error"></span>


        <label for="imagen">Imagen de la Sala:</label>
        <input type="file" id="imagen" name="imagen" accept="image/*"><br>
        <img src="<?= htmlspecialchars($sala['imagen'] ?? '') ?>" alt="Imagen de la Sala" style="max-width: 100px;">

        <button type="submit" class="btn btn-primary">Actualizar Sala</button>
    </form>

    <br><br>
    <a href="administrar.php" class="btn-volver">Volver</a>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <p class="error"><?= $_SESSION['mensaje']; ?></p>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
</body>
<script src="../Js/validaeditarsalas.js"></script>

</html>
