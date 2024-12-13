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
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $capacidad = $_POST['capacidad'] ?? '';
    $imagen = $_FILES['imagen'] ?? null;

    // Validación de campos vacíos
    if (empty($nombre) || empty($capacidad) || !$imagen || $imagen['error'] !== UPLOAD_ERR_OK) {
        $error = "Debes rellenar todos los campos y seleccionar una imagen.";
    } elseif ($capacidad < 6 || $capacidad > 30) {
        $error = "La capacidad debe estar entre 6 y 30.";
    } else {
        // Validar si el nombre de la sala ya existe
        $stmt = $con->prepare("SELECT COUNT(*) FROM salas WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "El nombre de la sala ya existe. Por favor, elige otro nombre.";
        } else {
            // Procesar la imagen
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true); // Crear el directorio si no existe
            }

            $targetFile = $targetDir . basename($imagen["name"]);
            if (move_uploaded_file($imagen["tmp_name"], $targetFile)) {
                try {
                    $con->beginTransaction(); // Iniciar la transacción

                    // Insertar la nueva sala
                    $stmt = $con->prepare("INSERT INTO salas (nombre, capacidad, imagen) VALUES (?, ?, ?)");
                    $stmt->execute([$nombre, $capacidad, $targetFile]);

                    $con->commit(); // Confirmar transacción
                    $mensaje = 'Sala añadida correctamente.';
                } catch (PDOException $e) {
                    $con->rollBack(); // Deshacer cambios si ocurre un error
                    $error = 'Error al añadir la sala: ' . $e->getMessage();
                }
            } else {
                $error = "Error al subir la imagen.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Sala</title>
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
    <h1>Añadir Sala</h1>

    <!-- Mostrar los mensajes de error o éxito -->
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre de la Sala:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>"><br><br>
        <span id="error-nombre" class="error"></span>


        <label for="capacidad">Capacidad:</label>
        <input type="number" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($capacidad ?? ''); ?>"><br><br>
        <span id="error-capacidad" class="error"></span>


        <label for="imagen">Imagen de la Sala:</label>
        <input type="file" id="imagen"  name="imagen" accept="image/*"><br><br>

        <button type="submit">Añadir Sala</button>
    </form>

    <br>
    <a href="administrar.php" class="btn-volver">Volver</a>
</body>
<script src="../Js/validañadirsalas.js"></script>
</html>
