<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Inicializar el mensaje de error
$error = '';

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cantidad = $_POST['cantidad'] ?? '';
    $id_mesa = $_POST['id_mesa'] ?? '';
    $id_sala = $_POST['id_sala'] ?? '';

    // Verificar si los campos no están vacíos
    if (empty($cantidad) || empty($id_mesa) || empty($id_sala)) {
        $error = "Debes de rellenar todos los campos";
    } elseif ($cantidad < 0) {
        // Validación para evitar números negativos
        $error = "La cantidad de sillas no puede ser un número negativo.";
    } else {
        // Obtener la capacidad máxima de la mesa y las sillas actuales
        try {
            $stmt = $con->prepare("SELECT capacidad FROM mesas WHERE id_mesa = :id_mesa");
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->execute();
            $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($mesa) {
                $capacidad_mesa = $mesa['capacidad'];

                // Obtener el número de sillas actuales en la mesa
                $stmt = $con->prepare("SELECT COUNT(*) as total_sillas FROM sillas WHERE id_mesa = :id_mesa");
                $stmt->bindParam(':id_mesa', $id_mesa);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $total_sillas = $resultado['total_sillas'];

                // Verificar si la cantidad de sillas que se desea añadir excede la capacidad
                if (($total_sillas + $cantidad) > $capacidad_mesa) {
                    $error = "No puedes añadir más sillas de las que permite la capacidad de la mesa.";
                } else {
                    // Si no se excede la capacidad, agregar las sillas
                    try {
                        $stmt = $con->prepare("INSERT INTO sillas (id_mesa) VALUES (:id_mesa)");
                        $stmt->bindParam(':id_mesa', $id_mesa);
                        for ($i = 0; $i < $cantidad; $i++) {
                            $stmt->execute();
                        }
                        $_SESSION['mensaje'] = 'Las sillas se han añadido correctamente.';
                    } catch (PDOException $e) {
                        $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
                    }

                    // Redirigir de nuevo a esta página para evitar reenvío de formulario
                    header("Location: añadir_sillas.php");
                    exit();
                }
            } else {
                $error = "La mesa seleccionada no existe.";
            }
        } catch (PDOException $e) {
            $error = "Error al consultar la capacidad de la mesa: " . $e->getMessage();
        }
    }
}

// Obtener las salas disponibles
$salas = [];
try {
    $stmt = $con->query("SELECT id_sala, nombre FROM salas");
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener salas: " . $e->getMessage();
}

// Obtener las mesas disponibles
$mesas = [];
try {
    $stmt = $con->query("SELECT id_mesa, capacidad, id_sala FROM mesas");
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener mesas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Sillas</title>
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
        .mensaje-error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['mensaje'])): ?>
        <p class="mensaje"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="id_sala">Selecciona una Sala:</label>
        <select name="id_sala" id="id_sala" onchange="filtrarMesas()">
            <option value="">Seleccione una sala</option>
            <?php foreach ($salas as $sala): ?>
                <option value="<?php echo $sala['id_sala']; ?>"><?php echo htmlspecialchars($sala['nombre']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="id_mesa">Selecciona una Mesa:</label>
        <select name="id_mesa" id="id_mesa">
            <option value="">Seleccione una mesa</option>
            <?php foreach ($mesas as $mesa): ?>
                <option value="<?php echo $mesa['id_mesa']; ?>" data-sala="<?php echo $mesa['id_sala']; ?>">Mesa: <?php echo $mesa['id_mesa']; ?>, Capacidad: <?php echo $mesa['capacidad']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="cantidad">Cantidad de Sillas:</label>
        <input type="number" id="cantidad" name="cantidad" placeholder="Cantidad de Sillas" min="0">
        <span id="error-cantidad" class="mensaje-error"></span>
        


        <button type="submit">Añadir Sillas</button>
    </form>

    <!-- Botón para volver a la página de administrar -->
    <a href="administrar.php">
        <button type="button">Volver a Administrar</button>
    </a>

    <script>
        function filtrarMesas() {
            const idSala = document.getElementById('id_sala').value;
            const selectMesa = document.getElementById('id_mesa');
            const opcionesMesas = selectMesa.querySelectorAll('option');

            opcionesMesas.forEach(opcion => {
                if (opcion.value === "" || opcion.getAttribute('data-sala') === idSala) {
                    opcion.style.display = 'block';
                } else {
                    opcion.style.display = 'none';
                }
            });

            selectMesa.value = ""; // Resetear la selección de mesa
        }
    </script>
</body>
<script src="../Js/validañadirsillas.js"></script>

</html>
