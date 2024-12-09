<?php
session_start();
include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar que el usuario esté logueado
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID de la mesa desde la URL
$id_mesa = isset($_GET['id_mesa']) ? (int)$_GET['id_mesa'] : 0;

// Obtener el ID de la sala desde la URL
$id_sala = isset($_GET['id_sala']) ? (int)$_GET['id_sala'] : 0;

// Verificar que el ID de la mesa sea válido
if ($id_mesa <= 0) {
    echo "ID de mesa no válido.";
    exit();
}

// Obtener la fecha y hora actuales
$horaActual = date('Y-m-d H:i:s');

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_reserva = isset($_POST['nombre_reserva']) ? $_POST['nombre_reserva'] : '';
    $fecha_reserva = isset($_POST['fecha_reserva']) ? $_POST['fecha_reserva'] : date('Y-m-d');
    $hora_inicio = isset($_POST['hora_inicio']) ? $_POST['hora_inicio'] : '';
    $hora_fin = isset($_POST['hora_fin']) ? $_POST['hora_fin'] : '';

    // Verificar que los campos no estén vacíos
    if (empty($nombre_reserva) || empty($hora_inicio) || empty($hora_fin)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Calcular la hora completa de la reserva
        $hora_inicio_completa = $fecha_reserva . ' ' . $hora_inicio;
        $hora_fin_completa = $fecha_reserva . ' ' . $hora_fin;

        // Verificar si la mesa ya está ocupada en el rango de hora seleccionado
        $stmt = $con->prepare("SELECT 1 FROM reservas WHERE id_mesa = :id_mesa AND ((hora_reserva < :hora_fin AND hora_fin > :hora_inicio))");
        $stmt->execute([
            'id_mesa' => $id_mesa,
            'hora_inicio' => $hora_inicio_completa,
            'hora_fin' => $hora_fin_completa
        ]);

        if ($stmt->rowCount() > 0) {
            $error = "La mesa ya está ocupada en el rango de hora seleccionado.";
        } else {
            // Obtener el ID del camarero logueado
            $id_camarero = $_SESSION['id_usuario'];

            // Insertar la reserva en la base de datos
            $stmt = $con->prepare("INSERT INTO reservas (id_mesa, nombre_cliente, hora_reserva, hora_fin, camarero_id) 
                                   VALUES (:id_mesa, :nombre_cliente, :hora_reserva, :hora_fin, :camarero_id)");
            $stmt->execute([
                'id_mesa' => $id_mesa,
                'nombre_cliente' => $nombre_reserva,
                'hora_reserva' => $hora_inicio_completa,
                'hora_fin' => $hora_fin_completa,
                'camarero_id' => $id_camarero
            ]);

            // Actualizar el estado de la mesa a 'ocupada'
            $stmt = $con->prepare("UPDATE mesas SET estado = 'ocupada' WHERE id_mesa = :id_mesa");
            $stmt->execute(['id_mesa' => $id_mesa]);

            $success = "Reserva realizada con éxito.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Mesa</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        function updateHourOptions() {
            const fechaReserva = document.getElementById('fecha_reserva').value;
            const horaActual = new Date().toTimeString().slice(0, 5);
            const fechaActual = new Date().toISOString().slice(0, 10);

            const horaInicioSelect = document.getElementById('hora_inicio');
            const horaFinSelect = document.getElementById('hora_fin');

            for (let select of [horaInicioSelect, horaFinSelect]) {
                for (let option of select.options) {
                    if (fechaReserva === fechaActual && option.value < horaActual) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                }
            }
        }
    </script>
</head>

<body onload="updateHourOptions()">
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
        <div class="container">
            <a href="./manager_home.php" class="navbar-brand">
                <img id="LogoNav" src="../img/LOGO-REST.png" alt="Logo" />
            </a>
        </div>
    </nav>

    <div class="container">
        <h1>Realizar Reserva para la Mesa <?php echo htmlspecialchars($id_mesa); ?></h1>

        <?php
        // Mostrar errores o éxito
        if (isset($error)) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        } elseif (isset($success)) {
            echo '<div class="alert alert-success">' . $success . '</div>';
        }
        ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nombre_reserva" class="form-label">Nombre de la Reserva</label>
                <input type="text" class="form-control" id="nombre_reserva" name="nombre_reserva" required>
            </div>

            <div class="mb-3">
                <label for="fecha_reserva" class="form-label">Fecha de la Reserva</label>
                <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" min="<?php echo date('Y-m-d'); ?>" onchange="updateHourOptions()" required>
            </div>

            <div class="mb-3">
                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                <select class="form-control" id="hora_inicio" name="hora_inicio" required>
                    <?php
                    for ($h = 0; $h < 24; $h++) {
                        for ($m = 0; $m < 60; $m += 30) {
                            $hora = sprintf('%02d:%02d', $h, $m);
                            echo '<option value="' . $hora . '">' . $hora . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="hora_fin" class="form-label">Hora de Fin</label>
                <select class="form-control" id="hora_fin" name="hora_fin" required>
                    <?php
                    for ($h = 0; $h < 24; $h++) {
                        for ($m = 0; $m < 60; $m += 30) {
                            $hora = sprintf('%02d:%02d', $h, $m);
                            echo '<option value="' . $hora . '">' . $hora . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
        </form>

        <div class="reservas-existentes mt-4">
            <h3>Reservas Existentes para la Mesa <?php echo htmlspecialchars($id_mesa); ?></h3>

            <?php
            $stmt = $con->prepare("SELECT nombre_cliente, hora_reserva, hora_fin FROM reservas WHERE id_mesa = :id_mesa ORDER BY hora_reserva");
            $stmt->execute(['id_mesa' => $id_mesa]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($reservas) {
                echo '<ul>';
                foreach ($reservas as $reserva) {
                    echo '<li>';
                    echo 'Cliente: ' . htmlspecialchars($reserva['nombre_cliente']) . ' - ';
                    echo 'Desde: ' . htmlspecialchars($reserva['hora_reserva']) . ' - ';
                    echo 'Hasta: ' . htmlspecialchars($reserva['hora_fin']);
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No hay reservas para esta mesa.</p>';
            }
            ?>
        </div>

        <a href="mostrar_mesas.php?id_sala=<?php echo $id_sala; ?>" class="btn btn-secondary mt-3">Volver a las Mesas</a>
    </div>

</body>
</html>

<?php
$con = null; // Cerrar la conexión
?>  
