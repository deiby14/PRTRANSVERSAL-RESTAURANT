<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Agustin51";
$dbname = "db_restaurante";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}

// Obtener las opciones para los filtros
$sala_query = "SELECT id_sala, nombre FROM salas";
$sala_stmt = $conn->prepare($sala_query);
$sala_stmt->execute();
$salas = $sala_stmt->fetchAll(PDO::FETCH_ASSOC);

$usuario_query = "SELECT id_usuario, nombre_completo FROM usuarios WHERE tipo_usuario = 'camarero'";
$usuario_stmt = $conn->prepare($usuario_query);
$usuario_stmt->execute();
$usuarios = $usuario_stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener los valores de los filtros si se envían
$sala_filter = isset($_GET['sala']) ? $_GET['sala'] : '';
$usuario_filter = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$estado_filter = isset($_GET['estado']) ? $_GET['estado'] : '';

// Consulta SQL actualizada para incluir la cantidad de sillas por mesa
$sql = "
SELECT 
    mesas.id_mesa AS id_mesa, 
    mesas.capacidad, 
    mesas.estado, 
    mesas.id_sala,
    salas.id_sala, 
    salas.nombre AS sala_nombre, 
    salas.capacidad AS sala_capacidad,
    ocupaciones.id_ocupacion, 
    ocupaciones.id_mesa AS id_mesas_ocupadas,
    ocupaciones.fecha_ocupacion, 
    ocupaciones.fecha_libera,
    usuarios.id_usuario, 
    usuarios.nombre_completo,
    COUNT(sillas.id_silla) AS total_sillas
FROM mesas
LEFT JOIN ocupaciones ON mesas.id_mesa = ocupaciones.id_mesa
LEFT JOIN salas ON mesas.id_sala = salas.id_sala
LEFT JOIN usuarios ON usuarios.id_usuario = ocupaciones.id_usuario
LEFT JOIN sillas ON sillas.id_mesa = mesas.id_mesa
WHERE 1=1
";

// Aplicar los filtros si se han seleccionado
if ($sala_filter) {
    $sql .= " AND mesas.id_sala = :sala";
}
if ($usuario_filter) {
    $sql .= " AND usuarios.id_usuario = :usuario";
}
if ($estado_filter) {
    $sql .= " AND mesas.estado = :estado";
}

$sql .= " GROUP BY mesas.id_mesa, ocupaciones.id_ocupacion, salas.id_sala, usuarios.id_usuario
          ORDER BY ocupaciones.fecha_ocupacion";

// Ejecutar la consulta con los parámetros de los filtros
$stmt = $conn->prepare($sql);

if ($sala_filter) {
    $stmt->bindParam(':sala', $sala_filter, PDO::PARAM_INT);
}
if ($usuario_filter) {
    $stmt->bindParam(':usuario', $usuario_filter, PDO::PARAM_INT);
}
if ($estado_filter) {
    $stmt->bindParam(':estado', $estado_filter, PDO::PARAM_STR);
}

$stmt->execute();

// Obtener los resultados
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cerrar la conexión
$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ocupaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .filter-form select, .filter-form button {
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
    </style>
</head>
<body>

<div class="container">
    <h1>Historial de Ocupaciones</h1>
    
    <!-- Filtro Formulario -->
    <form id="filterForm" class="filter-form" method="GET">
        <select name="sala" id="sala" onchange="this.form.submit()">
            <option value="">Seleccione Sala</option>
            <?php foreach ($salas as $sala): ?>
                <option value="<?= $sala['id_sala'] ?>" <?= $sala['id_sala'] == $sala_filter ? 'selected' : '' ?>><?= $sala['nombre'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="usuario" id="usuario" onchange="this.form.submit()">
            <option value="">Seleccione Camarero</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id_usuario'] ?>" <?= $usuario['id_usuario'] == $usuario_filter ? 'selected' : '' ?>><?= $usuario['nombre_completo'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="">Seleccione Estado</option>
            <option value="libre" <?= $estado_filter == 'libre' ? 'selected' : '' ?>>Libre</option>
            <option value="ocupada" <?= $estado_filter == 'ocupada' ? 'selected' : '' ?>>Ocupada</option>
        </select>

        <button type="button" onclick="clearFilters()">Limpiar Filtros</button>
    </form>

    <div id="results">
        <?php if ($resultado): ?>
            <?php foreach ($resultado as $row): ?>
                <div class="result-item">
                    <p><strong>Mesa ID:</strong> <?= $row['id_mesa'] ?></p>
                    <p><strong>Capacidad de mesa:</strong> <?= $row['capacidad'] ?></p>
                    <p><strong>Estado de mesa:</strong> <?= $row['estado'] ?></p>
                    <p><strong>Sala:</strong> <?= $row['sala_nombre'] ?> (Capacidad: <?= $row['sala_capacidad'] ?>)</p>
                    <p><strong>Ocupación ID:</strong> <?= $row['id_ocupacion'] ?></p>
                    <p><strong>Fecha de ocupación:</strong> <?= $row['fecha_ocupacion'] ?></p>
                    <p><strong>Fecha de liberación:</strong> <?= ($row['fecha_libera'] ? $row['fecha_libera'] : 'No liberada aún') ?></p>
                    <p><strong>Usuario (Camarero):</strong> <?= $row['nombre_completo'] ?></p>
                    <p><strong>Total de sillas en la mesa:</strong> <?= $row['total_sillas'] ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron ocupaciones.</p>
        <?php endif; ?>
    </div>

    <div class="clear-filters">
        <button onclick="clearFilters()">Limpiar Filtros</button>
    </div>
</div>

<script>
    // Función para limpiar los filtros
    function clearFilters() {
        document.getElementById("sala").selectedIndex = 0;
        document.getElementById("usuario").selectedIndex = 0;
        document.getElementById("estado").selectedIndex = 0;
        document.getElementById("filterForm").submit();
    }
</script>

</body>
</html>
