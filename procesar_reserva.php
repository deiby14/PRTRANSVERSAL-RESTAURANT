<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mesa = $_POST['id_mesa'];
    $nombre_cliente = $_POST['nombre_reserva'];
    $fecha_reserva = $_POST['fecha'];
    $hora = $_POST['hora'];
    $personas = $_POST['personas'];

    $fecha_actual = date('Y-m-d');

    if ($fecha_reserva === $fecha_actual) {
        $hora_reserva = $fecha_reserva . ' ' . $hora;
        $hora_fin = date('Y-m-d H:i:s', strtotime($hora_reserva . ' +1 hour'));

        try {
            // Preparar la consulta SQL para insertar la reserva
            $stmt = $con->prepare("INSERT INTO reservas (id_mesa, nombre_cliente, cantidad_personas, hora_reserva, hora_fin, camarero_id) VALUES (:id_mesa, :nombre_cliente, :cantidad_personas, :hora_reserva, :hora_fin, :camarero_id)");
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->bindParam(':nombre_cliente', $nombre_cliente);
            $stmt->bindParam(':cantidad_personas', $personas);
            $stmt->bindParam(':hora_reserva', $hora_reserva);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':camarero_id', $_SESSION['id_usuario']);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "Reserva realizada con éxito.";
            } else {
                echo "Error al realizar la reserva.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "La reserva solo puede hacerse para el día actual.";
    }

    // Cerrar la conexión
    $con = null;
} else {
    echo "Método de solicitud no permitido.";
}
?> 