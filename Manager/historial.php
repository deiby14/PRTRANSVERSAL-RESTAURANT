<?php
include_once("../conexion.php");

// Obtener las opciones para los filtros
$sala_query = "SELECT id_sala, nombre FROM salas";
$sala_stmt = $con->prepare($sala_query);
$sala_stmt->execute();
$salas = $sala_stmt->fetchAll(PDO::FETCH_ASSOC);

$usuario_query = "SELECT id_usuario, nombre_completo, tipo_usuario FROM usuarios";
$usuario_stmt = $con->prepare($usuario_query);
$usuario_stmt->execute();
$usuarios = $usuario_stmt->fetchAll(PDO::FETCH_ASSOC);

$mesa_query = "SELECT id_mesa FROM mesas";
$mesa_stmt = $con->prepare($mesa_query);
$mesa_stmt->execute();
$mesas = $mesa_stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener los valores de los filtros si se envían
$sala_filter = isset($_GET['sala']) ? $_GET['sala'] : '';
$usuario_filter = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$nombre_reserva_filter = isset($_GET['nombre_reserva']) ? $_GET['nombre_reserva'] : '';
$mesa_filter = isset($_GET['mesa']) ? $_GET['mesa'] : '';
$fecha_reserva_filter = isset($_GET['fecha_reserva']) ? $_GET['fecha_reserva'] : '';
$sala_mesas_reservadas_filter = isset($_GET['sala_mesas_reservadas']) ? $_GET['sala_mesas_reservadas'] : '';
$active_tab = isset($_GET['active_tab']) ? $_GET['active_tab'] : 'historial';

// Consulta SQL para el historial de reservas
$sql_historial = "
SELECT 
    mesas.id_mesa AS id_mesa, 
    mesas.capacidad, 
    salas.nombre AS sala_nombre, 
    reservas.id_reserva, 
    reservas.hora_reserva, 
    reservas.hora_fin,
    reservas.nombre_cliente,
    usuarios.nombre_completo,
    COUNT(sillas.id_silla) AS total_sillas
FROM mesas
LEFT JOIN reservas ON mesas.id_mesa = reservas.id_mesa
LEFT JOIN salas ON mesas.id_sala = salas.id_sala
LEFT JOIN usuarios ON usuarios.id_usuario = reservas.camarero_id
LEFT JOIN sillas ON sillas.id_mesa = mesas.id_mesa
WHERE 1=1
";

// Aplicar los filtros si se han seleccionado
if ($sala_filter) {
    $sql_historial .= " AND mesas.id_sala = :sala";
}
if ($usuario_filter) {
    $sql_historial .= " AND usuarios.id_usuario = :usuario";
}
if ($nombre_reserva_filter) {
    $sql_historial .= " AND reservas.nombre_cliente LIKE :nombre_reserva";
}
if ($mesa_filter) {
    $sql_historial .= " AND mesas.id_mesa = :mesa";
}
if ($fecha_reserva_filter) {
    $sql_historial .= " AND DATE(reservas.hora_reserva) = :fecha_reserva";
}

$sql_historial .= " GROUP BY mesas.id_mesa, reservas.id_reserva, salas.id_sala, usuarios.id_usuario
          ORDER BY reservas.hora_reserva";

// Ejecutar la consulta con los parámetros de los filtros
$stmt_historial = $con->prepare($sql_historial);

if ($sala_filter) {
    $stmt_historial->bindParam(':sala', $sala_filter, PDO::PARAM_INT);
}
if ($usuario_filter) {
    $stmt_historial->bindParam(':usuario', $usuario_filter, PDO::PARAM_INT);
}
if ($nombre_reserva_filter) {
    $nombre_reserva_filter = "%$nombre_reserva_filter%";
    $stmt_historial->bindParam(':nombre_reserva', $nombre_reserva_filter, PDO::PARAM_STR);
}
if ($mesa_filter) {
    $stmt_historial->bindParam(':mesa', $mesa_filter, PDO::PARAM_INT);
}
if ($fecha_reserva_filter) {
    $stmt_historial->bindParam(':fecha_reserva', $fecha_reserva_filter, PDO::PARAM_STR);
}

$stmt_historial->execute();

// Obtener los resultados del historial
$resultado_historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);

// Consulta SQL para las mesas más reservadas, incluyendo la sala
$sql_mesas_reservadas = "
SELECT 
    mesas.id_mesa, 
    salas.nombre AS sala_nombre,
    COUNT(reservas.id_reserva) AS total_reservas
FROM reservas
LEFT JOIN mesas ON reservas.id_mesa = mesas.id_mesa
LEFT JOIN salas ON mesas.id_sala = salas.id_sala
WHERE 1=1
";

if ($sala_mesas_reservadas_filter) {
    $sql_mesas_reservadas .= " AND mesas.id_sala = :sala_mesas_reservadas";
}

$sql_mesas_reservadas .= " GROUP BY mesas.id_mesa, salas.id_sala
ORDER BY total_reservas DESC
LIMIT 10";

$stmt_mesas_reservadas = $con->prepare($sql_mesas_reservadas);

if ($sala_mesas_reservadas_filter) {
    $stmt_mesas_reservadas->bindParam(':sala_mesas_reservadas', $sala_mesas_reservadas_filter, PDO::PARAM_INT);
}

$stmt_mesas_reservadas->execute();

// Obtener los resultados de las mesas más reservadas
$resultado_mesas_reservadas = $stmt_mesas_reservadas->fetchAll(PDO::FETCH_ASSOC);

// Cerrar la conexión
$con = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Reservas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../CSS/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            margin: 0 5px;
            border-radius: 4px;
        }
        .tab.active {
            background-color: #0056b3;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .filter-form select, .filter-form input, .filter-form button {
            padding: 8px;
            font-size: 16px;
            margin: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .filter-form button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }
        .result-item {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .result-item p {
            margin: 5px 0;
        }
        .clear-filters {
            text-align: center;
            margin-top: 20px;
        }
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .back-button a {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body id="bodyGen">

<div class="container">
    <!-- Botón de Volver -->
    <div class="back-button">
        <a href="manager_home.php">Volver a Inicio</a>
    </div>

    <h1>Historial de Reservas</h1>
    
    <!-- Pestañas -->
    <div class="tabs">
        <div class="tab <?= $active_tab == 'historial' ? 'active' : '' ?>" onclick="showTab('historial')">Historial de Reservas</div>
        <div class="tab <?= $active_tab == 'mesas_reservadas' ? 'active' : '' ?>" onclick="showTab('mesas_reservadas')">Mesas Más Reservadas</div>
    </div>

    <!-- Contenido de la pestaña Historial de Reservas -->
    <div id="historial" class="tab-content <?= $active_tab == 'historial' ? 'active' : '' ?>">
        <form id="filterForm" class="filter-form" method="GET">
            <input type="hidden" name="active_tab" value="historial">
            <select name="sala" id="sala" onchange="this.form.submit()">
                <option value="">Todas las salas</option>
                <?php foreach ($salas as $sala): ?>
                    <option value="<?= $sala['id_sala'] ?>" <?= $sala['id_sala'] == $sala_filter ? 'selected' : '' ?>><?= $sala['nombre'] ?></option>
                <?php endforeach; ?>
            </select>

            <select name="usuario" id="usuario" onchange="this.form.submit()">
                <option value="">Todos los usuarios</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id_usuario'] ?>" <?= $usuario['id_usuario'] == $usuario_filter ? 'selected' : '' ?>><?= $usuario['nombre_completo'] ?> (<?= $usuario['tipo_usuario'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="nombre_reserva" placeholder="Nombre de la Reserva" value="<?= htmlspecialchars($nombre_reserva_filter) ?>" onchange="this.form.submit()">

            <select name="mesa" id="mesa" onchange="this.form.submit()">
                <option value="">Todas las mesas</option>
                <?php foreach ($mesas as $mesa): ?>
                    <option value="<?= $mesa['id_mesa'] ?>" <?= $mesa['id_mesa'] == $mesa_filter ? 'selected' : '' ?>>Mesa <?= $mesa['id_mesa'] ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="fecha_reserva" value="<?= htmlspecialchars($fecha_reserva_filter) ?>" onchange="this.form.submit()">

            <button type="button" onclick="clearFilters()">Limpiar Filtros</button>
        </form>

        <div id="results">
            <?php if ($resultado_historial): ?>
                <?php foreach ($resultado_historial as $row): ?>
                    <div class="result-item">
                        <p><strong>Mesa ID:</strong> <?= $row['id_mesa'] ?></p>
                        <p><strong>Capacidad de mesa:</strong> <?= $row['capacidad'] ?></p>
                        <p><strong>Sala:</strong> <?= $row['sala_nombre'] ?></p>
                        <p><strong>Reserva ID:</strong> <?= $row['id_reserva'] ?></p>
                        <p><strong>Nombre del Cliente:</strong> <?= $row['nombre_cliente'] ?></p>
                        <p><strong>Hora de reserva:</strong> <?= $row['hora_reserva'] ?></p>
                        <p><strong>Hora de fin:</strong> <?= $row['hora_fin'] ?></p>
                        <p><strong>Usuario (Camarero):</strong> <?= $row['nombre_completo'] ?></p>
                        <p><strong>Total de sillas en la mesa:</strong> <?= $row['total_sillas'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron reservas.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contenido de la pestaña Mesas Más Reservadas -->
    <div id="mesas_reservadas" class="tab-content <?= $active_tab == 'mesas_reservadas' ? 'active' : '' ?>">
        <form id="filterFormMesas" class="filter-form" method="GET">
            <input type="hidden" name="active_tab" value="mesas_reservadas">
            <select name="sala_mesas_reservadas" id="sala_mesas_reservadas" onchange="this.form.submit()">
                <option value="">Todas las salas</option>
                <?php foreach ($salas as $sala): ?>
                    <option value="<?= $sala['id_sala'] ?>" <?= $sala['id_sala'] == $sala_mesas_reservadas_filter ? 'selected' : '' ?>><?= $sala['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <div id="results">
            <?php if ($resultado_mesas_reservadas): ?>
                <?php foreach ($resultado_mesas_reservadas as $row): ?>
                    <div class="result-item">
                        <p><strong>Mesa ID:</strong> <?= $row['id_mesa'] ?></p>
                        <p><strong>Sala:</strong> <?= $row['sala_nombre'] ?></p>
                        <p><strong>Total de Reservas:</strong> <?= $row['total_reservas'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron datos de mesas reservadas.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="clear-filters">
        <button onclick="clearFilters()">Limpiar Filtros</button>
    </div>
</div>

<script>
    function showTab(tabId) {
        // Ocultar todas las pestañas
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));

        // Mostrar la pestaña seleccionada
        document.getElementById(tabId).classList.add('active');
        document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');

        // Actualizar el campo oculto para mantener la pestaña activa
        document.querySelectorAll('input[name="active_tab"]').forEach(input => input.value = tabId);
    }

    // Función para limpiar los filtros
    function clearFilters() {
        document.getElementById("sala").selectedIndex = 0;
        document.getElementById("usuario").selectedIndex = 0;
        document.getElementById("mesa").selectedIndex = 0;
        document.querySelector('input[name="nombre_reserva"]').value = '';
        document.querySelector('input[name="fecha_reserva"]').value = '';
        document.getElementById("filterForm").submit();
    }
</script>

</body>
</html>
