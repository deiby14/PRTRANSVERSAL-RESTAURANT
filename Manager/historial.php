<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php';

// Verificar si se ha solicitado limpiar los filtros
if (isset($_POST['limpiar'])) {
    header("Location: historial.php");
    exit();
}

// Obtener las salas para el selector
$querySalas = "SELECT id_sala, nombre FROM salas";
$stmtSalas = $con->prepare($querySalas);
$stmtSalas->execute();
$salas = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);

// Obtener las mesas para el selector
$queryMesas = "SELECT id_mesa FROM mesas";
$stmtMesas = $con->prepare($queryMesas);
$stmtMesas->execute();
$mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);

// Obtener los usuarios para el selector (solo manager o camarero)
$queryUsuarios = "SELECT id_usuario, nombre_completo FROM usuarios WHERE tipo_usuario IN ('manager', 'camarero')";
$stmtUsuarios = $con->prepare($queryUsuarios);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

// Aplicar filtros si se han seleccionado
$salaSeleccionada = $_POST['sala'] ?? '';
$mesaSeleccionada = $_POST['mesa'] ?? '';
$usuarioSeleccionado = $_POST['usuario'] ?? '';
$nombreReserva = $_POST['nombre_reserva'] ?? '';

// Filtro sumativo por sala, mesa, usuario y nombre de reserva
$queryReservas = "SELECT s.nombre, m.id_mesa, r.nombre_cliente, COUNT(r.id_reserva) AS total_reservas
                  FROM salas s
                  LEFT JOIN mesas m ON s.id_sala = m.id_sala
                  LEFT JOIN reservas r ON m.id_mesa = r.id_mesa
                  LEFT JOIN usuarios u ON r.camarero_id = u.id_usuario
                  WHERE (:sala = '' OR s.id_sala = :sala)
                  AND (:mesa = '' OR m.id_mesa = :mesa)
                  AND (:usuario = '' OR u.id_usuario = :usuario)
                  AND (:nombre_reserva = '' OR r.nombre_cliente LIKE :nombre_reserva)
                  GROUP BY s.id_sala, m.id_mesa, r.nombre_cliente";
$stmtReservas = $con->prepare($queryReservas);
$stmtReservas->bindParam(':sala', $salaSeleccionada);
$stmtReservas->bindParam(':mesa', $mesaSeleccionada);
$stmtReservas->bindParam(':usuario', $usuarioSeleccionado);
$nombreReservaWildcard = "%$nombreReserva%";
$stmtReservas->bindParam(':nombre_reserva', $nombreReservaWildcard);
$stmtReservas->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Reservas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap');

        * {
            margin: 0;
            box-sizing: border-box;
            font-family: "Kanit", sans-serif;
            font-weight: 600;
        }

        body {
            background-image: url('ruta/a/tu/imagen.jpg');
            background-size: cover;
            background-position: center;
            padding: 20px;
        }

        .btn-volver {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-volver:hover {
            background-color: #218838;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            margin-right: 5px;
            font-weight: bold;
        }

        select, input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(25% - 20px);
        }

        button {
            padding: 10px 20px;
            background-color: #e58517;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #bf6f00ad;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .reservas-filtradas {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        p {
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }
    </style>
    <script>
        function autoSubmit() {
            document.getElementById('filterForm').submit();
        }
    </script>
</head>
<body>
    <a href="manager_home.php" class="btn-volver">Volver a Manager Home</a>

    <form method="post" action="" id="filterForm">
        <label for="sala">Filtrar por Sala:</label>
        <select name="sala" id="sala" onchange="autoSubmit()">
            <option value="">Todas</option>
            <?php foreach ($salas as $sala): ?>
                <option value="<?= $sala['id_sala'] ?>" <?= $salaSeleccionada == $sala['id_sala'] ? 'selected' : '' ?>>
                    <?= $sala['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="mesa">Filtrar por Mesa:</label>
        <select name="mesa" id="mesa" onchange="autoSubmit()">
            <option value="">Todas</option>
            <?php foreach ($mesas as $mesa): ?>
                <option value="<?= $mesa['id_mesa'] ?>" <?= $mesaSeleccionada == $mesa['id_mesa'] ? 'selected' : '' ?>>
                    Mesa <?= $mesa['id_mesa'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="usuario">Filtrar por Usuario:</label>
        <select name="usuario" id="usuario" onchange="autoSubmit()">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id_usuario'] ?>" <?= $usuarioSeleccionado == $usuario['id_usuario'] ? 'selected' : '' ?>>
                    <?= $usuario['nombre_completo'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nombre_reserva">Filtrar por Nombre de Reserva:</label>
        <input type="text" name="nombre_reserva" id="nombre_reserva" value="<?= htmlspecialchars($nombreReserva) ?>" oninput="autoSubmit()">

        <button type="submit" name="limpiar">Limpiar Filtros</button>
    </form>

    <div class="reservas-filtradas">
        <h2>Reservas Filtradas</h2>
        <?php while ($fila = $stmtReservas->fetch(PDO::FETCH_ASSOC)): ?>
            <p>Sala: <?= $fila['nombre'] ?> - Mesa ID: <?= $fila['id_mesa'] ?> - Cliente: <?= $fila['nombre_cliente'] ?> - Total Reservas: <?= $fila['total_reservas'] ?></p>
        <?php endwhile; ?>
    </div>

    <a href="manager_home.php" class="btn-volver">Volver a Manager Home</a>
</body>
</html>
