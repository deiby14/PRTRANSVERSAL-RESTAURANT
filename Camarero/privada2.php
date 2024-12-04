<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Aquí corregimos la consulta para usar 'id_sala' en lugar de 'comedor_id'
$result = $con->query("SELECT * FROM mesas WHERE id_sala = 7"); // Comedor 1 tiene id_sala = 4

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
        /* Estilos para las mesas representadas como rectángulos */
        .mesas-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* Se ajusta según el espacio disponible */
            gap: 20px; /* Espacio entre los rectángulos */
            padding: 20px;
        }

        .mesa {
            width: 150px; /* Ancho de cada rectángulo */
            height: 150px; /* Alto de cada rectángulo */
            background-color: #007bff; /* Color del rectángulo */
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 10px; /* Bordes redondeados */
            color: white;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .mesa:hover {
            background-color: #0056b3; /* Cambio de color al pasar el ratón */
        }

        /* Estilos para el contenedor de cada mesa y su botón */
        .mesa-container-item {
            display: flex;
            flex-direction: column; /* Apila el contenido de la mesa y el botón verticalmente */
            align-items: center; /* Centra el contenido */
        }

        /* Estilos para el botón "Reservar" */
        .btn-reservar {
            width: 150px; /* Igual al ancho del rectángulo de la mesa */
            background-color: #28a745; /* Color verde del botón */
            color: white;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px; /* Separación del rectángulo */
        }

        .btn-reservar:hover {
            background-color: #218838; /* Cambio de color al pasar el ratón */
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
        // Usamos fetch(PDO::FETCH_ASSOC) para obtener un arreglo asociativo
        while ($mesa = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="mesa-container-item">'; // Contenedor para mesa y botón
            echo '<div class="mesa">';
            echo '<h3 class="nombreMesa">' . htmlspecialchars($mesa['id_mesa']) . '</h3>';
            echo '</div>';
            // Agregamos el botón de reserva debajo de cada mesa
            echo '<button class="btn-reservar" onclick="reservarMesa(' . $mesa['id_mesa'] . ')">Reservar</button>';
            echo '</div>'; // Cierre del contenedor de mesa y botón
        }
        ?>
    </div>

    <script src="../Js/volver.js"></script>
    <script>
        // Función para manejar la acción de reserva
        function reservarMesa(idMesa) {
            alert('Reserva para la mesa: ' + idMesa);
            // Aquí puedes agregar más lógica para gestionar la reserva
        }
    </script>
</body>

</html>

<?php
$con = null; // Cerramos la conexión PDO
?>
