<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Consulta para obtener las mesas libres y su nombre de sala
$query = "
    SELECT mesas.id_mesa, mesas.capacidad, mesas.id_sala, salas.nombre AS nombre_sala
    FROM mesas
    JOIN salas ON mesas.id_sala = salas.id_sala
    WHERE mesas.estado = 'libre'"; // Obtener solo las mesas libres
$result = $con->query($query);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Reservas</title>
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
                    <a href="./camarero_home.php" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Formulario de Reserva de Mesa</h2>
        <form action="procesar_reserva.php" method="POST">
            <div class="mb-3">
                <label for="mesa" class="form-label">Selecciona una mesa</label>
                <select class="form-select" id="mesa" name="id_mesa" required>
                    <option value="">Seleccione una mesa</option>
                    <?php
                    // Mostrar las mesas libres de todas las salas
                    while ($mesa = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($mesa['id_mesa']) . '">';
                        echo 'Mesa ' . htmlspecialchars($mesa['id_mesa']) . ' - ';
                        echo 'Capacidad: ' . htmlspecialchars($mesa['capacidad']) . ' personas - ';
                        echo 'Sala: ' . htmlspecialchars($mesa['nombre_sala']);
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="nombre_cliente" class="form-label">Nombre del cliente</label>
                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
            </div>
            <div class="mb-3">
                <label for="hora_reserva" class="form-label">Hora de reserva</label>
                <input type="time" class="form-control" id="hora_reserva" name="hora_reserva" required>
            </div>
            <div class="mb-3">
    <label for="cantidad_personas" class="form-label">Cantidad de Personas</label>
    <input type="number" class="form-control" id="cantidad_personas" name="cantidad_personas" required>
</div>

            <button type="submit" class="btn btn-primary">Reservar Mesa</button>
        </form>
        <br>
        <button type="" class="btn btn-primary">Ver reservas</button>
       
    </div>
    <br>
    <button type="" class="btn btn-primary">Ver reservas</button>

    <script>
        // Aquí puedes agregar más scripts si es necesario
    </script>
</body>

</html>

<?php
$con = null; // Cerramos la conexión PDO
?>
