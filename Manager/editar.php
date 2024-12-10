<?php
include_once("../conexion.php");

// Verificar si se recibió el ID del trabajador
if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Obtener los datos del trabajador para editar
    $sql = "SELECT id_usuario, nombre_completo, tipo_usuario, contraseña FROM usuarios WHERE id_usuario = :id_usuario";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el trabajador existe
    if (!$trabajador) {
        die("Trabajador no encontrado.");
    }
} else {
    die("ID de trabajador no proporcionado.");
}

// Procesar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = trim($_POST['nombre_completo']);
    $tipo_usuario = trim($_POST['tipo_usuario']);

    // Validar que los campos no estén vacíos
    if (empty($nombre_completo) || empty($tipo_usuario)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Validar si el tipo de usuario es válido
        $tipos_validos = ['camarero', 'manager', 'mantenimiento', 'administrador'];
        if (!in_array($tipo_usuario, $tipos_validos)) {
            $error = "Tipo de usuario inválido.";
        } else {
            // Actualizar los datos del trabajador (sin cambiar la contraseña)
            $sql = "UPDATE usuarios SET nombre_completo = :nombre_completo, tipo_usuario = :tipo_usuario WHERE id_usuario = :id_usuario";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':nombre_completo', $nombre_completo);
            $stmt->bindParam(':tipo_usuario', $tipo_usuario);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $mensaje = "Trabajador actualizado con éxito.";
            } else {
                $error = "Error al actualizar el trabajador.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Trabajador</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .mensaje {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error {
    color: red;
    font-weight: bold;
    font-size: 12px;
    margin-top: 5px;
    display: block; /* Asegúrate de que se muestre */
}

    </style>
</head>
<body id="bodyGen">
    <h1>Editar Trabajador</h1>
    <?php if (isset($mensaje)): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="editar.php?id_usuario=<?= $trabajador['id_usuario'] ?>" method="POST">
        <label for="nombre_completo">Nombre Completo</label>
        <input type="text" name="nombre_completo" id="nombre_completo" value="<?= htmlspecialchars($trabajador['nombre_completo']) ?>">
        <span id="error-nombre" class="error"></span>


        <label for="tipo_usuario">Tipo de Usuario</label>
        <select name="tipo_usuario" id="tipo_usuario">
            <option value="camarero" <?= $trabajador['tipo_usuario'] === 'camarero' ? 'selected' : '' ?>>Camarero</option>
            <option value="manager" <?= $trabajador['tipo_usuario'] === 'manager' ? 'selected' : '' ?>>Manager</option>
            <option value="mantenimiento" <?= $trabajador['tipo_usuario'] === 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
            <option value="administrador" <?= $trabajador['tipo_usuario'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
        </select>

        <label for="contraseña">Contraseña</label>
        <input type="password" name="contraseña" id="contraseña" value="<?= htmlspecialchars($trabajador['contraseña']) ?>" disabled>

        <button type="submit">Actualizar</button>
    </form>

    <a href="trabajadores.php">Volver</a>
</body>
<script src="../Js/validaeditar.js"></script>

</html>
