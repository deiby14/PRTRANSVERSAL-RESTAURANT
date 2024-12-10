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
    $capacidad = $_POST['capacidad'] ?? '';
    $id_sala = $_POST['id_sala'] ?? '';

    // Validación de campos vacíos
    if (empty($capacidad) || empty($id_sala)) {
        $error = "Debes rellenar todos los campos.";
    } else {
        try {
            $con->beginTransaction(); // Iniciar la transacción

            // Insertar la nueva mesa
            $stmt = $con->prepare("INSERT INTO mesas (capacidad, id_sala) VALUES (?, ?)");
            $stmt->execute([$capacidad, $id_sala]);

            $con->commit(); // Confirmar transacción
            $mensaje = 'Mesa añadida correctamente.';
        } catch (PDOException $e) {
            $con->rollBack(); // Deshacer cambios si ocurre un error
            $error = 'Error al añadir la mesa: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Mesa</title>
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
    <h1>Añadir Mesa</h1>

    <!-- Mostrar los mensajes de error o éxito -->
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="capacidad">Capacidad:</label>
        <input type="number" name="capacidad" id="capacidad" value="<?php echo htmlspecialchars($capacidad ?? ''); ?>" required><br><br>

        <label for="id_sala">Sala:</label>
        <select name="id_sala" required>
            <?php
            $stmt = $con->query("SELECT id_sala, nombre FROM salas");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = isset($id_sala) && $id_sala == $row['id_sala'] ? 'selected' : '';
                echo "<option value=\"{$row['id_sala']}\" $selected>{$row['nombre']}</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Añadir Mesa</button>
    </form>

    <br>
    <a href="administrar.php">Volver</a>
</body>
<script src="../Js/validañadirmesas"></script>
</html>
