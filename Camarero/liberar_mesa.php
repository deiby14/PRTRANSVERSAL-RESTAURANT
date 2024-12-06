<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

if (isset($_GET['id_mesa'])) {
    $id_mesa = $_GET['id_mesa'];
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
} else {
    // Se eliminó el mensaje de error de datos no válidos
    // echo "Datos de mesa no válidos.";
}

// Cerrar la conexión
$con = null;
?>
