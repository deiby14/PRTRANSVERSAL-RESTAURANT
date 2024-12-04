<?php
include_once("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Comprobar si el ID está vacío o no se ha proporcionado
    if (!empty($id_usuario)) {
        // Preparar la consulta para eliminar al trabajador
        $stmt = $con->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();
    }
}

// Redirigir de nuevo a la página de trabajadores
header("Location: trabajadores.php");
exit();
?>

