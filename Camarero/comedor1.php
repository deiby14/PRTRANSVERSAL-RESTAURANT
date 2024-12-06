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
            echo '<div class="mesa-container-item">'; // Contenedor para mesa y estado

            // Rectángulo con estado
            echo '<div class="estado-rectangulo ' . ($mesa['estado'] == 'ocupada' ? 'ocupada' : 'libre') . '">';
            echo '<a href="gestionar_mesa.php?id_mesa=' . htmlspecialchars($mesa['id_mesa']) . '&estado=' . htmlspecialchars($mesa['estado']) . '" style="text-decoration: none; color: inherit;">';
            echo '<span>' . htmlspecialchars($mesa['estado']) . '</span>';
            echo '</a>';
            echo '</div>'; // Cierre del rectángulo de estado

            // Información de la mesa
            echo '<div class="mesa">';
            echo '<h3 class="mesa-id">Mesa: ' . htmlspecialchars($mesa['id_mesa']) . '</h3>';
            echo '<p class="mesa-capacidad">Capacidad: ' . htmlspecialchars($mesa['capacidad']) . ' personas</p>';
            echo '</div>'; // Cierre de la información de la mesa

            echo '</div>'; // Cierre del contenedor de mesa
        }
        ?>
    </div>

    <script src="../Js/volver.js"></script>
   
</body>

</html>

<?php
$con = null; // Cerramos la conexión PDO
?>
