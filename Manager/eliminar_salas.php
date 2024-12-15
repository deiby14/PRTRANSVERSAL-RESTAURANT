<?php
session_start();
include('../conexion.php');
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['tabla']) && isset($_GET['id'])) {
    $tabla = $_GET['tabla'];
    $id = $_GET['id'];

    try {
        // Comienza una transacción para asegurarse de que las eliminaciones sean atómicas
        $con->beginTransaction();

        // Verificamos la tabla y realizamos las eliminaciones correspondientes
        if ($tabla === 'salas') {
            // Eliminar las reservas relacionadas con las mesas de la sala
            $stmt = $con->prepare("DELETE FROM reservas WHERE id_mesa IN (SELECT id_mesa FROM mesas WHERE id_sala = :id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();


            // Eliminar las sillas asociadas a las mesas de la sala
            $stmt = $con->prepare("DELETE FROM sillas WHERE id_mesa IN (SELECT id_mesa FROM mesas WHERE id_sala = :id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar las mesas de la sala
            $stmt = $con->prepare("DELETE FROM mesas WHERE id_sala = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar la sala
            $stmt = $con->prepare("DELETE FROM salas WHERE id_sala = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Confirmar la transacción
        $con->commit();

    } catch (PDOException $e) {
        // Si hay algún error, revertimos la transacción
        $con->rollBack();
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
    }
} else {
    $_SESSION['mensaje'] = 'Datos insuficientes para realizar la operación.';
}

// Redirigir de vuelta a la página anterior
header('Location: administrar.php');
exit();
?>
