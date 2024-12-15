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
        // Dependiendo de la tabla, realizar la eliminación
        if ($tabla === 'salas') {
            // Eliminar una sala
            $stmt = $con->prepare("DELETE FROM salas WHERE id_sala = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($tabla === 'mesas') {
            // Primero, eliminar las sillas que hacen referencia a la mesa
            $stmt = $con->prepare("DELETE FROM sillas WHERE id_mesa = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Luego, eliminar las reservas que hacen referencia a la mesa
            $stmt = $con->prepare("DELETE FROM reservas WHERE id_mesa = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Finalmente, eliminar la mesa
            $stmt = $con->prepare("DELETE FROM mesas WHERE id_mesa = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($tabla === 'sillas') {
            // Eliminar una silla
            $stmt = $con->prepare("DELETE FROM sillas WHERE id_silla = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Establecer mensaje de éxito
    } catch (PDOException $e) {
        // En caso de error, mostrar el mensaje
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
    }
} else {
    $_SESSION['mensaje'] = 'Datos insuficientes para realizar la operación.';
}

// Redirigir de vuelta a la página anterior
header('Location: administrar.php'); // Asegúrate de que este archivo se llame correctamente
exit();
?>
