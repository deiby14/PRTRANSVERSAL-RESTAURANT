<?php
// Incluir conexión a la base de datos
include_once '../conexion.php'; // Asegúrate de tener este archivo correctamente configurado

// Verificar si los datos se reciben
if (isset($_POST['id_mesa'], $_POST['nombre_cliente'], $_POST['cantidad_personas'], $_POST['hora_reserva'], $_POST['hora_fin'], $_POST['fecha_reserva'])) {
    $id_mesa = $_POST['id_mesa'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $cantidad_personas = $_POST['cantidad_personas'];
    $hora_reserva = $_POST['hora_reserva'];
    $hora_fin = $_POST['hora_fin'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $camarero_id = 1; // Asumimos que siempre es 1, ajusta según tu lógica

    // Preparar consulta SQL para insertar la reserva
    $sql = "INSERT INTO reservas (id_mesa, nombre_cliente, cantidad_personas, hora_reserva, hora_fin, fecha_reserva, camarero_id)
            VALUES ('$id_mesa', '$nombre_cliente', '$cantidad_personas', '$hora_reserva', '$hora_fin', '$fecha_reserva', '$camarero_id')";

    // Ejecutar consulta
    if (mysqli_query($con, $sql)) {
        echo 'success'; // Respuesta esperada en caso de éxito
    } else {
        echo 'error'; // Respuesta en caso de fallo
    }
} else {
    echo 'error'; // Si no se reciben los datos correctamente
}
?>
