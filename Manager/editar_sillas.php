<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); 

if (isset($_GET['id'])) {
    $id_mesa = $_GET['id'];

    // Obtener el nombre de la mesa
    $stmt = $con->prepare("SELECT id_mesa, capacidad FROM mesas WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();
    $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si la mesa existe
    if ($mesa) {
        $id_mesa = $mesa['id_mesa'];
        $capacidad_mesa = $mesa['capacidad'];
    } else {
        $_SESSION['mensaje'] = 'Mesa no encontrada.';
        header("Location: administrar.php");
        exit();
    }

    // Obtener el número actual de sillas de la mesa seleccionada
    $stmt = $con->prepare("SELECT COUNT(*) as total_sillas FROM sillas WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_sillas = $resultado['total_sillas'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar si el campo de sillas no está vacío y que no sea negativo
    if (empty($_POST['total_sillas']) || $_POST['total_sillas'] < 0) {
        $_SESSION['mensaje'] = 'El campo de número de sillas no puede estar vacío ni ser negativo.';
        header("Location: editar_sillas.php?id=" . $id_mesa);
        exit();
    }

    $total_sillas_nueva = $_POST['total_sillas'];

    // Validar si el número de sillas es mayor que la capacidad de la mesa
    if ($total_sillas_nueva > $capacidad_mesa) {
        $_SESSION['mensaje'] = 'No puedes añadir más sillas que la capacidad de la mesa.';
        header("Location: editar_sillas.php?id=" . $id_mesa);
        exit();
    }

    try {
        // Eliminar las sillas existentes para esa mesa
        $stmt = $con->prepare("DELETE FROM sillas WHERE id_mesa = :id_mesa");
        $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $stmt->execute();

        // Insertar las nuevas sillas según el número que el usuario ingresa
        $stmt = $con->prepare("INSERT INTO sillas (id_mesa) VALUES (:id_mesa)");
        $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
        for ($i = 0; $i < $total_sillas_nueva; $i++) {
            $stmt->execute();
        }

        $_SESSION['mensaje'] = 'Las sillas se han actualizado correctamente.';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
    }

    // Redirigir de nuevo a esta página para evitar reenvío de formulario
    header("Location: editar_sillas.php?id=" . $id_mesa);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sillas</title>
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
        .mensaje-error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    
    </style>
</head>
<body id="bodyGen">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <p class="mensaje"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></p>
    <?php endif; ?>

    <h1>Editar Sillas de la Mesa <?php echo htmlspecialchars($mesa['id_mesa']); ?> (Capacidad: <?php echo htmlspecialchars($mesa['capacidad']); ?>)</h1>

    <form method="POST" action="">
        <label for="total_sillas">Número de Sillas:</label>
        <input type="number" id="total_sillas" name="total_sillas" value="<?php echo isset($total_sillas) ? $total_sillas : 0; ?>">
        <span id="error-sillas" class="mensaje-error"></span>


        <button type="submit">Actualizar Sillas</button>
    </form>

    <a href="administrar.php" class="btn-volver">Volver</a>


</body>
<script src="../Js/validaeditarsillas.js"></script>
</html>
