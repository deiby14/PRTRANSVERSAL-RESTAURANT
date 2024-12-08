<?php
session_start();
include('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar que se recibe el ID de la mesa
if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = "ID de mesa no especificado.";
    header("Location: administrar.php");
    exit();
}

$id_mesa = $_GET['id'];

// Obtener el total de sillas para la mesa especificada
try {
    $stmt = $con->prepare("SELECT COUNT(id_silla) AS total_sillas FROM sillas WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $_SESSION['mensaje'] = "No se encontró información para la mesa seleccionada.";
        header("Location: administrar.php");
        exit();
    }

    $total_sillas = $result['total_sillas'];
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error al obtener información: " . $e->getMessage();
    header("Location: administrar.php");
    exit();
}

// Inicializar el mensaje de error
$error = '';

// Procesar el formulario de eliminación de sillas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cantidad_a_eliminar = $_POST['cantidad'] ?? '';
    $id_mesa = $_POST['id_mesa'] ?? '';

    // Validación: Verificar que el campo no esté vacío
    if (empty($cantidad_a_eliminar)) {
        $error = "Debes de especificar la cantidad de sillas a eliminar.";
    } elseif ($cantidad_a_eliminar < 0) {
        // Validación: No permitir números negativos
        $error = "La cantidad de sillas a eliminar no puede ser negativa.";
    } elseif ($cantidad_a_eliminar > $total_sillas) {
        // Validación: No eliminar más sillas de las que existen
        $error = "No puedes eliminar más sillas de las que existen en la mesa.";
    } else {
        // Aquí puedes agregar la lógica para eliminar las sillas de la base de datos
        try {
            $stmt = $con->prepare("DELETE FROM sillas WHERE id_mesa = :id_mesa LIMIT :cantidad");
            $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad_a_eliminar, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: administrar.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = "Error al eliminar las sillas: " . $e->getMessage();
            header("Location: eliminar_sillas.php?id=$id_mesa");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Sillas</title>
    <link rel="stylesheet" href="../CSS/form.css"> <!-- Asegúrate de tener un estilo adecuado -->
    <style>
      
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
    </style>
</head>
<body>
    <h1>Eliminar Sillas</h1>
    
    <!-- Mostrar mensaje de error si lo hay -->
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="eliminar_sillas.php?id=<?= $id_mesa ?>" method="POST">
        <p><strong>Sillas Totales:</strong> <?= $total_sillas ?></p>
        <label for="cantidad">Cantidad a eliminar:</label>
        <input type="number" id="cantidad" name="cantidad">
        <input type="hidden" name="id_mesa" value="<?= $id_mesa ?>">
        <button type="submit" class="btn btn-danger">Eliminar</button>
        <a href="administrar.php" class="btn-success:hover">Volver</a>


    </form>
</body>
</html>
