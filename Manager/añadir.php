<?php
require '../conexion.php'; // Asegúrate de que este archivo conecta con la base de datos correctamente.

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $repetir_contrasena = trim($_POST['repetir_contrasena'] ?? '');
    $tipo_usuario = $_POST['tipo_usuario'] ?? '';

    // Validar si los campos no están vacíos
    if (empty($nombre_completo) || empty($contrasena) || empty($repetir_contrasena) || empty($tipo_usuario)) {
        $mensaje = 'Por favor, completa todos los campos.';
    } else {
        if ($contrasena !== $repetir_contrasena) {
            $mensaje = 'Las contraseñas no coinciden.';
        } else {
            // Verificar si ya existe un usuario con el mismo nombre y rol
            $stmt = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_completo = :nombre_completo AND tipo_usuario = :tipo_usuario");
            $stmt->bindParam(':nombre_completo', $nombre_completo);
            $stmt->bindParam(':tipo_usuario', $tipo_usuario);
            $stmt->execute();
            $existe = $stmt->fetchColumn();

            if ($existe > 0) {
                $mensaje = 'Ya existe un trabajador con este nombre y rol.';
            } else {
                // Hashear la contraseña
                $hash_contrasena = password_hash($contrasena, PASSWORD_BCRYPT);

                // Insertar en la base de datos
                $stmt = $con->prepare("INSERT INTO usuarios (nombre_completo, contraseña, tipo_usuario) VALUES (:nombre_completo, :contrasena, :tipo_usuario)");
                $stmt->bindParam(':nombre_completo', $nombre_completo);
                $stmt->bindParam(':contrasena', $hash_contrasena);
                $stmt->bindParam(':tipo_usuario', $tipo_usuario);

                if ($stmt->execute()) {
                    $mensaje = 'Trabajador añadido correctamente.';
                } else {
                    $mensaje = 'Error al añadir el trabajador.';
                }
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
    <title>Añadir Trabajador</title>
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
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .mensaje {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Añadir Trabajador</h1>

    <?php if ($mensaje): ?>
        <p class="<?= (strpos($mensaje, 'Error') !== false || strpos($mensaje, 'no coinciden') !== false || strpos($mensaje, 'completa todos los campos') !== false) ? 'error' : 'mensaje' ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form action="añadir.php" method="post">
        <label for="nombre_completo">Nombre Completo</label>
        <input type="text" id="nombre_completo" name="nombre_completo">

        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena">

        <label for="repetir_contrasena">Repetir Contraseña</label>
        <input type="password" id="repetir_contrasena" name="repetir_contrasena" >

        <label for="tipo_usuario">Tipo de Usuario</label>
        <select id="tipo_usuario" name="tipo_usuario" >
            <option value="camarero">Camarero</option>
            <option value="manager">Manager</option>
            <option value="mantenimiento">Mantenimiento</option>
        </select>

        <button type="submit">Añadir Trabajador</button>
    </form>

    <a href="trabajadores.php" class="btn btn-success">Volver</a>
</body>
</html>
