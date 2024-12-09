<?php
session_start();
include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar que el usuario esté logueado
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el id_mesa y el estado desde la URL
$id_mesa = isset($_GET['id_mesa']) ? (int)$_GET['id_mesa'] : 0;
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Validar los parámetros
if ($id_mesa <= 0 || !in_array($estado, ['libre', 'ocupada'])) {
    echo "Datos inválidos.";
    exit();
}

// Cambiar el estado de la mesa
$nuevo_estado = ($estado == 'libre') ? 'ocupada' : 'libre'; // Cambiar de libre a ocupada y viceversa

// Preparar la consulta para actualizar el estado de la mesa
$stmtActualizar = $con->prepare("UPDATE mesas SET estado = :estado WHERE id_mesa = :id_mesa");
$stmtActualizar->execute(['estado' => $nuevo_estado, 'id_mesa' => $id_mesa]);

// Si el nuevo estado es 'ocupada', insertar en la tabla ocupaciones
if ($nuevo_estado == 'ocupada') {
    // Obtenemos el id_usuario del usuario logueado
    $id_usuario = $_SESSION['id_usuario']; // Asumiendo que el id_usuario está en la sesión

    // Insertar la ocupación en la tabla ocupaciones
    $stmtInsertarOcupacion = $con->prepare("INSERT INTO ocupaciones (id_mesa, id_usuario, fecha_ocupacion) 
                                             VALUES (:id_mesa, :id_usuario, NOW())");
    $stmtInsertarOcupacion->execute(['id_mesa' => $id_mesa, 'id_usuario' => $id_usuario]);
} elseif ($nuevo_estado == 'libre') {
    // Si el estado cambia a libre, se actualiza la fecha de liberación en la tabla ocupaciones
    // Buscar la ocupación actual de la mesa
    $stmtBuscarOcupacion = $con->prepare("SELECT * FROM ocupaciones WHERE id_mesa = :id_mesa AND fecha_libera IS NULL ORDER BY fecha_ocupacion DESC LIMIT 1");
    $stmtBuscarOcupacion->execute(['id_mesa' => $id_mesa]);
    $ocupacion = $stmtBuscarOcupacion->fetch(PDO::FETCH_ASSOC);

    if ($ocupacion) {
        // Actualizar la fecha de liberación
        $stmtLiberarMesa = $con->prepare("UPDATE ocupaciones SET fecha_libera = NOW() WHERE id_ocupacion = :id_ocupacion");
        $stmtLiberarMesa->execute(['id_ocupacion' => $ocupacion['id_ocupacion']]);
    }
}

// Redirigir de vuelta a la página de mesas
header("Location: mostrar_mesas.php?id_sala=" . $_GET['id_sala']);
exit();
?>

<?php
$con = null; // Cerrar la conexión PDO
?>
