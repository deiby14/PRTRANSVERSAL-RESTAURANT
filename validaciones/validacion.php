<?php
session_start();
include('../conexion.php');

// Verificar si se ha enviado el formulario de login
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['login'])) {
    // Redirigir al login si no es una solicitud POST o el formulario no está enviado
    header("Location: ../index.php");
    exit();
}

// Recoger y sanitizar los datos del formulario
$usuario = $_POST['nombre'];
$contrasena = $_POST['contrasena'];

try {
    // Preparar la consulta para verificar si el usuario existe
    $stmt = $con->prepare("SELECT id_usuario, contraseña, tipo_usuario FROM usuarios WHERE nombre_completo = :nombre");
    $stmt->execute(['nombre' => $usuario]);

    // Verificar si el usuario existe en la base de datos
    if ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Verificar la contraseña
        if (password_verify($contrasena, $fila['contraseña'])) {
            // Verificar el tipo de usuario
            if ($fila['tipo_usuario'] === 'camarero') {
                $_SESSION['id_usuario'] = $fila['id_usuario'];
                $_SESSION['nombre'] = $usuario; // Almacenar el nombre correcto
                header("Location: ../Camarero/camarero_home.php");
                exit();
            } elseif ($fila['tipo_usuario'] === 'manager') {
                $_SESSION['id_usuario'] = $fila['id_usuario'];
                $_SESSION['nombre'] = $usuario; // Almacenar el nombre correcto
                header("Location: ../Manager/manager_home.php");
                exit();
            } else {
                // Usuario no autorizado
                unset($_SESSION['nombre']); // Eliminar el nombre de la sesión
                header("Location: ../index.php?error=no_autorizado");
                exit();
            }
        } else {
            // Contraseña incorrecta
            unset($_SESSION['nombre']); // Eliminar el nombre de la sesión
            header("Location: ../index.php?error=incorrecto");
            exit();
        }
    } else {
        // Usuario no encontrado
        unset($_SESSION['nombre']); // Eliminar el nombre de la sesión
        header("Location: ../index.php?error=incorrecto");
        exit();
    }
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
?>
