<?php
include_once('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mesa = $_POST['id_mesa'];
    $nuevo_estado = $_POST['estado'];

    // Si la mesa se libera, obtenemos la hora actual para registrar como hora de fin
    $hora_fin = null;
    if ($nuevo_estado === 'libre') {
        $hora_fin = date('Y-m-d H:i:s');  // Obtiene la fecha y hora actual
    }

    // Actualizar el estado de la mesa
    $query = "UPDATE mesas SET estado = :nuevo_estado WHERE id_mesa = :id_mesa";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':nuevo_estado', $nuevo_estado);
    $stmt->bindParam(':id_mesa', $id_mesa);

    if ($stmt->execute()) {
        // Si la mesa se libera, actualizamos la hora_fin en la reserva
        if ($nuevo_estado === 'libre') {
            $update_reserva_query = "UPDATE reservas SET hora_fin = :hora_fin WHERE id_mesa = :id_mesa AND estado = 'ocupada'";
            $update_reserva_stmt = $con->prepare($update_reserva_query);
            $update_reserva_stmt->bindParam(':hora_fin', $hora_fin);
            $update_reserva_stmt->bindParam(':id_mesa', $id_mesa);
            $update_reserva_stmt->execute();
        }

        echo "Estado de la mesa actualizado correctamente.";
    } else {
        echo "Error al actualizar el estado de la mesa.";
    }
}
?>
