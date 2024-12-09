<?php
session_start();
include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar que el usuario esté logueado
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el id_sala desde la URL
$id_sala = isset($_GET['id_sala']) ? (int)$_GET['id_sala'] : 0;
if ($id_sala <= 0) {
    echo "Sala no válida";
    exit();
}

// Obtener las mesas asociadas a esta sala
$stmtMesas = $con->prepare("SELECT * FROM mesas WHERE id_sala = :id_sala");
$stmtMesas->execute(['id_sala' => $id_sala]);
$mesas = $stmtMesas->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas de la Sala</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
    <!-- Bootstrap JS (y dependencias) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <style>
        /* Contenedor principal de las mesas */
        .mesas-container {
            display: flex;
            flex-wrap: wrap; /* Permite que las mesas se acomoden en varias filas */
            justify-content: space-around;
            gap: 20px;
            padding: 20px;
        }

        /* Contenedor de cada mesa */
        .mesa-container-item {
            width: 250px;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        /* Estilo para el rectángulo de estado */
        .estado-rectangulo {
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 4px;
        }

        .estado-rectangulo.libre {
            background-color: #28a745; /* Verde */
            color: white;
        }

        .estado-rectangulo.ocupada {
            background-color: #dc3545; /* Rojo */
            color: white;
        }

        .mesa {
            margin-bottom: 10px;
        }

        /* Diseño de la mesa */
        .mesa-id {
            font-size: 1.2em;
            font-weight: bold;
        }

        .mesa-capacidad {
            font-size: 1em;
            color: #555;
        }
    </style>
</head>

<body>
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
                    <a href="../CerrarSesion.php" class="btn btn-primary me-3">Cerrar sesión</a>
                    <a href="trabajadores.php" class="btn btn-primary me-3">Trabajadores</a>
                    <a href="./historial.php" class="btn btn-secondary me-3">Ver Historial</a>
                    <a href="./manager_home.php" class="btn btn-secondary">Volver</a>
                    <a href="administrar.php" class="btn btn-secondary">Administrar</a>
                </div>
            </div>
        </div>
    </nav>

    <h1>Mesas de la Sala</h1>
    <div class="mesas-container">
        <?php
        // Mostrar las mesas de esta sala
        foreach ($mesas as $mesa) {
            echo '<div class="mesa-container-item">';
            echo '<div class="estado-rectangulo ' . ($mesa['estado'] == 'ocupada' ? 'ocupada' : 'libre') . '">';
            echo '<a href="gestionar_mesa.php?id_mesa=' . $mesa['id_mesa'] . '&estado=' . $mesa['estado'] . '&id_sala=' . $id_sala . '">';
            echo '<span>' . htmlspecialchars($mesa['estado']) . '</span>';
            echo '</a>';
            echo '</div>';
            echo '<div class="mesa">';
            echo '<h3 class="mesa-id">Mesa: ' . htmlspecialchars($mesa['id_mesa']) . '</h3>';
            echo '<p class="mesa-capacidad">Capacidad: ' . htmlspecialchars($mesa['capacidad']) . ' personas</p>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>

</body>
</html>

<?php
$con = null; // Cerrar la conexión
?>
