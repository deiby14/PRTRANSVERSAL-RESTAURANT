<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

if (isset($_GET['id_mesa']) && isset($_GET['estado'])) {
    $id_mesa = $_GET['id_mesa'];
    $estado_actual = $_GET['estado'];

    if ($estado_actual === 'libre') {
        // Lógica para ocupar la mesa
        $fecha_ocupacion = date('Y-m-d H:i:s');
        try {
            // Insertar en la tabla ocupaciones
            $stmt = $con->prepare("INSERT INTO ocupaciones (id_mesa, id_usuario, sillas, fecha_ocupacion) VALUES (:id_mesa, :id_usuario, 1, :fecha_ocupacion)");
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
            $stmt->bindParam(':fecha_ocupacion', $fecha_ocupacion);
            $stmt->execute();

            // Actualizar el estado de la mesa a 'ocupada'
            $stmt = $con->prepare("UPDATE mesas SET estado = 'ocupada' WHERE id_mesa = :id_mesa");
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->execute();

        } catch (PDOException $e) {
            // Se eliminó el mensaje de error para evitar la salida en la página
            // echo "Error: " . $e->getMessage();
        }
    } elseif ($estado_actual === 'ocupada') {
        // Liberar la mesa directamente sin confirmación
        $fecha_libera = date('Y-m-d H:i:s');
        try {
            // Actualizar la fecha de liberación en la tabla ocupaciones
            $stmt = $con->prepare("UPDATE ocupaciones SET fecha_libera = :fecha_libera WHERE id_mesa = :id_mesa AND fecha_libera IS NULL");
            $stmt->bindParam(':fecha_libera', $fecha_libera);
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->execute();

            // Actualizar el estado de la mesa a 'libre'
            $stmt = $con->prepare("UPDATE mesas SET estado = 'libre' WHERE id_mesa = :id_mesa");
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->execute();

            // Redirigir sin mensaje
            echo "<script>window.location.href='comedor1.php';</script>";
        } catch (PDOException $e) {
            // Se eliminó el mensaje de error para evitar la salida en la página
            // echo "Error: " . $e->getMessage();
        }
    }
} else {
    // Se eliminó el mensaje de error de datos no válidos
    // echo "Datos de mesa no válidos.";
}

// Cerrar la conexión
$con = null;
?>
