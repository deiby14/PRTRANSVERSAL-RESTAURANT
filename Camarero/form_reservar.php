<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include_once('../conexion.php'); // Incluye la conexión a la base de datos

// Verifica que se haya recibido el ID de la mesa
if (isset($_GET['id_mesa'])) {
    $id_mesa = $_GET['id_mesa'];
    $usuario = $_SESSION['nombre']; // Asumiendo que el nombre del usuario está en la sesión
    $fecha_reserva = date('Y-m-d H:i:s'); // Fecha y hora actual

    try {
        // Inserta la reserva en la tabla correspondiente
        $query = $con->prepare("INSERT INTO reservas (id_mesa, usuario, fecha_reserva) VALUES (?, ?, ?)");
        $query->execute([$id_mesa, $usuario, $fecha_reserva]);

        echo "<script>
            alert('Reserva realizada con éxito.');
            window.location.href = './manager_home.php'; // Redirige a la página principal
        </script>";
    } catch (PDOException $e) {
        echo "<script>
            alert('Error al realizar la reserva: " . $e->getMessage() . "');
            window.history.back(); // Vuelve a la página anterior
        </script>";
    }
} else {
    echo "<script>
        alert('No se recibió una mesa válida para reservar.');
        window.history.back(); // Vuelve a la página anterior
    </script>";
}
?>

