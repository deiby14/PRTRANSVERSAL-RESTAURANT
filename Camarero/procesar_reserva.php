<?php
session_start(); // Asegúrate de que la sesión esté iniciada

include_once('../conexion.php');

// Obtener los datos del formulario
$id_mesa = $_POST['id_mesa'];
$nombre_cliente = $_POST['nombre_cliente'];
$cantidad_personas = $_POST['cantidad_personas']; // Este campo ahora está presente en el formulario
$hora_reserva = $_POST['hora_reserva']; // Esta es la hora seleccionada en formato HH:MM

// Verificar que la sesión esté activa y el usuario esté autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Redirigir si no hay sesión activa
    header('Location: ../login.php');
    exit();
}

$camarero_id = $_SESSION['id_usuario']; // Suponiendo que el ID del camarero está almacenado en la sesión

// Obtener la fecha actual
$fecha_actual = date('Y-m-d'); // Formato de fecha: YYYY-MM-DD

// Combinar la fecha actual con la hora proporcionada
$fecha_hora_reserva = $fecha_actual . ' ' . $hora_reserva . ':00'; // Añadimos los segundos

// Preparar la consulta para insertar la reserva
$query = "
    INSERT INTO reservas (id_mesa, nombre_cliente, cantidad_personas, hora_reserva, hora_fin, camarero_id)
    VALUES (:id_mesa, :nombre_cliente, :cantidad_personas, :hora_reserva, :hora_fin, :camarero_id)
";

// Usar una consulta preparada para evitar inyecciones SQL
$stmt = $con->prepare($query);

// Insertar la reserva en la base de datos
$stmt->bindParam(':id_mesa', $id_mesa);
$stmt->bindParam(':nombre_cliente', $nombre_cliente);
$stmt->bindParam(':cantidad_personas', $cantidad_personas);
$stmt->bindParam(':hora_reserva', $fecha_hora_reserva);
$stmt->bindParam(':hora_fin', $fecha_hora_reserva); // Asegúrate de que esta columna también esté correctamente definida
$stmt->bindParam(':camarero_id', $camarero_id);

// Ejecutar la consulta
try {
    $stmt->execute();
    echo "Reserva realizada con éxito!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
