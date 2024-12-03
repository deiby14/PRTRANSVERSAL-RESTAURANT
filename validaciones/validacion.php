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

// Preparar la consulta para verificar si el usuario existe
$stmt = mysqli_prepare($con, "SELECT id_usuario, contraseña, tipo_usuario FROM usuarios WHERE nombre_completo = ?");
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

// Verificar si el usuario existe en la base de datos
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);

    // Verificar la contraseña
    if (password_verify($contrasena, $fila['contraseña'])) {
        // Almacenar el id_usuario en la sesión y eliminar el nombre almacenado
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['nombre'] = $_POST['nombre'];


        // Redirigir según el tipo de usuario
        if ($fila['tipo_usuario'] === 'camarero') {
            header("Location: ../Camarero/camarero_home.php");
        } elseif ($fila['tipo_usuario'] === 'manager') {
            header("Location: ../Manager/manager_home.php");
        }
        exit();
    } else {
        // Si la contraseña no es correcta, redirigir con error y almacenar el nombre en la sesión
        $_SESSION['nombre'] = htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8');
        header("Location: ../index.php?error=incorrecto");
        exit();
    }
} else {
    // Si el usuario no existe, redirigir con error y almacenar el nombre en la sesión
    $_SESSION['nombre'] = htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8');
    header("Location: ../index.php?error=incorrecto");
    exit();
}

// Cerrar la consulta y la conexión
mysqli_stmt_close($stmt);
mysqli_close($con);
?>
