<?php 
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar si se pasó el ID de la sala a editar
if (isset($_GET['id'])) {
    $id_sala = $_GET['id'];
    
    // Obtener los datos de la sala
    $stmt = $con->prepare("SELECT id_sala, nombre, capacidad FROM salas WHERE id_sala = :id_sala");
    $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
    $stmt->execute();
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra la sala, redirigir
    if (!$sala) {
        $_SESSION['mensaje'] = "Sala no encontrada.";
        header("Location: administrar.php");
        exit();
    }
}

// Actualizar sala
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $capacidad = $_POST['capacidad'];

    $stmt = $con->prepare("UPDATE salas SET nombre = :nombre, capacidad = :capacidad WHERE id_sala = :id_sala");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
    $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Sala actualizada correctamente.";
        header("Location: administrar.php");
        exit();
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la sala.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sala</title>
</head>
<body>
    <h1>Editar Sala</h1>

    <form method="POST">
        <label for="nombre">Nombre de la sala:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($sala['nombre']) ?>" required>

        <label for="capacidad">Capacidad:</label>
        <input type="number" id="capacidad" name="capacidad" value="<?= htmlspecialchars($sala['capacidad']) ?>" required>

        <button type="submit" class="btn btn-primary">Actualizar Sala</button>
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
