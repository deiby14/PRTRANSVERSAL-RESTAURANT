<?php
session_start();
include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar que el usuario esté logueado
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de la sala desde la URL
$id_sala = isset($_GET['id_sala']) ? (int)$_GET['id_sala'] : 0;

// Verificar que el ID de la sala sea válido
if ($id_sala <= 0) {
    // Redirigir a una página de inicio o una página predeterminada
    header("Location: camarero_home.php"); // Cambia a la página que desees como predeterminada
    exit();
}

// Obtener las mesas y el número de sillas asociadas a cada mesa
$stmtMesas = $con->prepare("
    SELECT mesas.id_mesa, IFNULL(COUNT(sillas.id_silla), 0) AS total_sillas
    FROM mesas
    LEFT JOIN sillas ON sillas.id_mesa = mesas.id_mesa
    WHERE mesas.id_sala = :id_sala
    GROUP BY mesas.id_mesa
");
$stmtMesas->execute(['id_sala' => $id_sala]);
$mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);

// Verificar las reservas existentes para cada mesa
$horaActual = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas de la Sala</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <style>
        .mesas-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            padding: 20px;
        }

        .mesa-container-item {
            width: 250px;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .mesa {
            margin-bottom: 10px;
        }

        .mesa-id {
            font-size: 1.2em;
            font-weight: bold;
        }

        .mesa-capacidad {
            font-size: 1em;
            color: #555;
        }

        .btn-reserva {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-reserva.rojo {
            background-color: #dc3545;
        }

        .btn-reserva:disabled {
            background-color: #d6d6d6;
        }
    </style>
</head>

<body id="bodyGen">
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
        <div class="container">
            <a href="camarero_home.php" data-bs-toggle="collapse" data-bs-target="#navbarButtonsExample" aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                <img id="LogoNav" src="../img/LOGO-REST.png" alt="Logo" />
            </a>
            <div class="collapse navbar-collapse" id="navbarButtonsExample">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a id="aNav" class="nav-link" href="camarero_home.php">Inicio</a>
                    </li>
                </ul>
                <div id="divSession">
                    <h4>Bienvenid@ <?php echo htmlspecialchars($_SESSION['nombre']); ?></h4>
                </div>
                <div class="d-flex align-items-center">
                    <a href="../CerrarSesion.php" class="btn btn-primary me-3">Cerrar sesión</a>
                    <a href="camarero_home.php" class="btn btn-secondary">Volver</a>
                  
                </div>
            </div>
        </div>
    </nav>

    <h1>Mesas de la Sala</h1>
    <div class="mesas-container">
        <?php
        // Mostrar las mesas de esta sala
        foreach ($mesas as $mesa) {
            // Obtener la hora actual
            $horaActual = date('Y-m-d H:i:s');

            // Verificar si la mesa está ocupada en el horario actual
            $stmtReserva = $con->prepare("SELECT 1 FROM reservas WHERE id_mesa = :id_mesa AND :horaActual BETWEEN hora_reserva AND hora_fin");
            $stmtReserva->execute(['id_mesa' => $mesa['id_mesa'], 'horaActual' => $horaActual]);
            $reservaOcupada = $stmtReserva->fetch();

            // Determinar el estado de la mesa (ocupada o libre) basado en la hora actual
            $estadoMesa = $reservaOcupada ? 'ocupada' : 'libre';

            // Determinar el color del botón dependiendo del estado de la mesa
            $botonColor = $estadoMesa == 'ocupada' ? 'red' : 'green';

            // Mostrar la mesa y el estado
            echo '<div class="mesa-container-item">';
            echo '<div class="mesa">';
            echo '<h3 class="mesa-id">Mesa: ' . htmlspecialchars($mesa['id_mesa']) . '</h3>';
            echo '<p class="mesa-capacidad">Sillas: ' . htmlspecialchars($mesa['total_sillas']) . '</p>';
            echo '</div>';
            
            // Enlace para reservar la mesa
            echo '<a href="reservar.php?id_mesa=' . $mesa['id_mesa'] . '&id_sala=' . $id_sala . '" class="btn-reserva ' . $botonColor . '">Reservar</a>';

            echo '</div>';
        }
        ?>
    </div>

</body>
</html>

<?php
$con = null; // Cerrar la conexión
?>
