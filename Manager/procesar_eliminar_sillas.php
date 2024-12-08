<?php
session_start();
include('../conexion.php'); // Asegúrate de que la ruta sea correcta

if (!isset($_POST['id_mesa'], $_POST['cantidad'])) {
    // No hace falta el mensaje, solo redirigir
    header("Location: administrar.php");
    exit();
}

$id_mesa = $_POST['id_mesa'];
$cantidad = (int)$_POST['cantidad'];

try {
    // Verificar cuántas sillas hay en la mesa
    $stmt = $con->prepare("SELECT COUNT(id_silla) AS total_sillas FROM sillas WHERE id_mesa = :id_mesa");
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || $cantidad > $result['total_sillas']) {
        // No hacemos nada con el mensaje, solo redirigir
        header("Location: eliminar_sillas.php?id=$id_mesa");
        exit();
    }

    // Eliminar las sillas
    $stmt = $con->prepare("DELETE FROM sillas WHERE id_mesa = :id_mesa LIMIT :cantidad");
    $stmt->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->execute();

    // Al eliminar con éxito, redirigimos a la página de administración sin mensaje
    header("Location: administrar.php");
    exit();
} catch (PDOException $e) {
    // No hay mensaje de error que se muestre en esta página, solo redirigir
    header("Location: administrar.php");
    exit();
}
?>
