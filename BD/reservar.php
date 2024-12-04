<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include_once('../conexion.php');

// Obtener los datos de la mesa desde la URL
$id_mesa = $_GET['id_mesa'];
$id_sala = $_GET['id_sala'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar los datos del formulario
    $nombre_cliente = $_POST['nombre_cliente'];
    $cantidad_personas = $_POST['cantidad_personas'];
    $hora_reserva = $_POST['hora_reserva'];
    $hora_fin = $_POST['hora_fin'];
    $camarero_id = $_SESSION['id_usuario']; // Suponiendo que el ID del camarero está en la sesión

    // Insertar la reserva en la base de datos
    $query = "INSERT INTO reservas (id_mesa, nombre_cliente, cantidad_personas, hora_reserva, hora_fin, camarero_id, estado) 
              VALUES (:id_mesa, :nombre_cliente, :cantidad_personas, :hora_reserva, :hora_fin, :camarero_id, 'ocupada')";
    
    $stmt = $con->prepare($query);
    $stmt->bindParam(':id_mesa', $id_mesa);
    $stmt->bindParam(':nombre_cliente', $nombre_cliente);
    $stmt->bindParam(':cantidad_personas', $cantidad_personas);
    $stmt->bindParam(':hora_reserva', $hora_reserva);
    $stmt->bindParam(':hora_fin', $hora_fin);
    $stmt->bindParam(':camarero_id', $camarero_id);

    if ($stmt->execute()) {
        // Actualizar el estado de la mesa a 'ocupada'
        $update_query = "UPDATE mesas SET estado = 'ocupada' WHERE id_mesa = :id_mesa";
        $update_stmt = $con->prepare($update_query);
        $update_stmt->bindParam(':id_mesa', $id_mesa);
        $update_stmt->execute();

        header("Location: comedor1.php?reserva=exito");
        exit();
    } else {
        echo "Error al realizar la reserva.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reserva</title>
</head>

<body>
    <h2>Formulario de Reserva</h2>
    <form action="reservar.php?id_mesa=<?php echo $id_mesa; ?>&id_sala=<?php echo $id_sala; ?>" method="POST">
        <label for="nombre_cliente">Nombre del Cliente:</label>
        <input type="text" name="nombre_cliente" id="nombre_cliente" required><br><br>

        <label for="cantidad_personas">Cantidad de Personas:</label>
        <input type="number" name="cantidad_personas" id="cantidad_personas" required><br><br>

        <label for="hora_reserva">Hora de la Reserva:</label>
        <input type="datetime-local" name="hora_reserva" id="hora_reserva" required><br><br>

        <label for="hora_fin">Hora de Fin de la Reserva:</label>
        <input type="datetime-local" name="hora_fin" id="hora_fin" required><br><br>

        <button type="submit">Confirmar Reserva</button>
    </form>
</body>

</html>

<?php
$con = null; // Cerramos la conexión PDO
?>
