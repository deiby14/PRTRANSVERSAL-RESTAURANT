<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Aquí corregimos la consulta para usar 'id_sala' en lugar de 'comedor_id'
$result = $con->query("SELECT * FROM mesas WHERE id_sala = 4"); // Comedor 1 tiene id_sala = 4
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comedor 1</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
    <!-- Bootstrap JS (y dependencias) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</head>

<body id="bodyGen">
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
        <div class="container">
            <a href="./manager_home.php" data-bs-toggle="collapse" data-bs-target="#navbarButtonsExample" aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                <img id="LogoNav" src="../img/LOGO-REST.png" alt="Logo" />
            </a>
            <div class="collapse navbar-collapse" id="navbarButtonsExample">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a id="aNav" class="nav-link" href="./manager_home.php">Inicio</a>
                    </li>
                </ul>
                <div id="divSession">
                    <h4>Bienvenid@ <?php echo htmlspecialchars($_SESSION['nombre']); ?></h4>
                </div>
                <div class="d-flex align-items-center">
                    <a href="../CerrarSesion.php" class="btn btn-primary me-3">
                        Cerrar sesión
                    </a>
                    <button id="volverBtn" class="btn btn-secondary">Volver</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="mesas-container">
    <?php
    while ($mesa = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="mesa-container-item">'; // Contenedor para mesa y botón

        // Rectángulo con estado
        echo '<div class="estado-rectangulo ' . ($mesa['estado'] == 'ocupada' ? 'reservada' : 'libre') . '">';
        echo '<span>' . htmlspecialchars($mesa['estado']) . '</span>';
        echo '</div>';

        // Información de la mesa
        echo '<div class="mesa">';
        echo '<h3 class="mesa-id">Mesa: ' . htmlspecialchars($mesa['id_mesa']) . '</h3>';
        echo '<p class="mesa-capacidad">Capacidad: ' . htmlspecialchars($mesa['capacidad']) . ' personas</p>';
        echo '</div>';

        // Botón para reservar/liberar
        echo '<button 
                type="button" 
                class="btn-reservar ' . ($mesa['estado'] == 'ocupada' ? 'ocupada' : 'libre') . '" 
                onclick="gestionarMesa(' . htmlspecialchars($mesa['id_mesa']) . ', \'' . htmlspecialchars($mesa['estado']) . '\')">
                ' . ($mesa['estado'] == 'ocupada' ? 'Liberar' : 'Reservar') . '
              </button>';

        echo '</div>'; // Cierre del contenedor de mesa y formulario
    }
    ?>
    </div>

    <script src="../Js/volver.js"></script>
    <script>
        function gestionarMesa(idMesa, estadoActual) {
            if (estadoActual === 'ocupada') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Quieres liberar esta mesa?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, liberar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        cambiarEstadoMesa(idMesa, 'libre');
                    }
                });
            } else {
                window.location.href = 'reservar.php?id_mesa=' + idMesa + '&id_sala=4';
            }
        }

        function cambiarEstadoMesa(idMesa, nuevoEstado) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'procesar_estado_mesa.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    Swal.fire('Mesa actualizada', 'El estado de la mesa ha sido actualizado.', 'success')
                        .then(() => {
                            location.reload();
                        });
                }
            };
            xhr.send('id_mesa=' + idMesa + '&estado=' + nuevoEstado);
        }
    </script>
</body>

</html>

<?php
$con = null; // Cerramos la conexión PDO
?>
