<?php
include_once("../conexion.php");

// Obtener la lista de trabajadores
$sql = "SELECT id_usuario, nombre_completo, tipo_usuario, contraseña FROM usuarios";
$stmt = $con->query($sql);
$trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Trabajadores</title>
    <link rel="stylesheet" href="../CSS/crud.css">
    <!-- Incluye SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .add-user-type {
            margin-bottom: 20px;
        }
        .add-user-type a {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .add-user-type a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body id="bodyGen">
    <h1>Gestión de Trabajadores</h1>
    
    <!-- Botones de acciones -->
    <div style="margin-bottom: 20px;">
        <a href="añadir.php" class="btn btn-primary">Añadir trabajador</a>
        <a href="manager_home.php" class="btn btn-success">Volver</a>
    </div>



    <!-- Tabla de trabajadores -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Tipo de Usuario</th>
                <th>Contraseña</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trabajadores as $trabajador): ?>
                <tr>
                    <td><?= htmlspecialchars($trabajador['id_usuario']) ?></td>
                    <td><?= htmlspecialchars($trabajador['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($trabajador['tipo_usuario']) ?></td>
                    <td><?= htmlspecialchars($trabajador['contraseña']) ?></td>
                    <td>
                        <a href="editar.php?id_usuario=<?= htmlspecialchars($trabajador['id_usuario']) ?>" 
                           class="btn btn-primary">Editar</a>
                        <button type="button" class="btn btn-danger" onclick="eliminarTrabajador(<?= $trabajador['id_usuario'] ?>, '<?= addslashes($trabajador['nombre_completo']) ?>')">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Incluye SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        function eliminarTrabajador(id_usuario, nombre_completo) {
            // Mostrar el SweetAlert2 de confirmación
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Seguro que deseas eliminar a ${nombre_completo}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario confirma, se envía el formulario de eliminación
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'eliminar.php';

                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id_usuario';
                    input.value = id_usuario;
                    form.appendChild(input);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
