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
    $stmt = $con->prepare("SELECT id_mesa, capacidad, estado, id_sala FROM mesas WHERE id_mesa = :id_mesa");
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
    $estado = $_POST['estado'];
    $id_sala = $_POST['id_sala'];

    $stmt = $con->prepare("UPDATE mesas SET capacidad = :capacidad, estado = :estado, id_sala = :id_sala WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Mesa actualizada correctamente.";
        header("Location: administrar.php");
        exit();
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la mesa.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mesa</title>
</head>
<body>
    <h1>Editar Mesa</h1>

    <form method="POST">
        <label for="capacidad">Capacidad:</label>
        <input type="number" id="capacidad" name="capacidad" value="<?= htmlspecialchars($mesa['capacidad']) ?>" required>

        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <option value="libre" <?= $mesa['estado'] === 'libre' ? 'selected' : '' ?>>Libre</option>
            <option value="ocupada" <?= $mesa['estado'] === 'ocupada' ? 'selected' : '' ?>>Ocupada</option>
        </select>

        <label for="id_sala">Sala:</label>
        <select name="id_sala" id="id_sala" required>
            <?php foreach ($salas as $sala): ?>
                <option value="<?= $sala['id_sala'] ?>" <?= $mesa['id_sala'] == $sala['id_sala'] ? 'selected' : '' ?>><?= htmlspecialchars($sala['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Actualizar Mesa</button>
    </form>

    <br><br>
    <a href="administrar.php" class="btn btn-secondary">Volver</a>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <script>
            alert('<?= $_SESSION['mensaje'] ?>');
        </script>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
</body>
</html>
