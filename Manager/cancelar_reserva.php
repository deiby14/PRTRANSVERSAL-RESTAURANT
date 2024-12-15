<?php
session_start();
include('../conexion.php'); 

if (isset($_GET['id_reserva'])) {
    $idReserva = $_GET['id_reserva'];

    try {
        // Eliminar la reserva de la base de datos
        $stmt = $con->prepare("DELETE FROM reservas WHERE id_reserva = :id_reserva");
        $stmt->bindParam(':id_reserva', $idReserva, PDO::PARAM_INT);
        $stmt->execute();

    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al cancelar la reserva: " . $e->getMessage();
    }
}

header("Location: reservar.php?id_mesa=" . $_GET['id_mesa']);
exit();
?> 