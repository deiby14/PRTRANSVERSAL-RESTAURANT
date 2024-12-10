<?php 
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

include('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Guardar la selección de tabla en la sesión
if (isset($_POST['tabla'])) {
    $_SESSION['selected_table'] = $_POST['tabla'];
}

// Si no está presente en la sesión, usar 'salas' como valor predeterminado
$selected = $_SESSION['selected_table'] ?? 'salas';

// Guardar la selección de sala en la sesión si está seleccionada
if (isset($_POST['sala'])) {
    $_SESSION['selected_sala'] = $_POST['sala'];
} else {
    $_SESSION['selected_sala'] = $_SESSION['selected_sala'] ?? '';
}

// Obtener las mesas disponibles
$mesas = [];
try {
    $stmt = $con->query("SELECT id_mesa, nombre FROM mesas JOIN salas ON mesas.id_sala = salas.id_sala");
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Obtener las salas disponibles
$salas = [];
try {
    $stmt = $con->query("SELECT id_sala, nombre FROM salas");
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Consultas dinámicas basadas en la selección
define('SELECT_ACTIONS', [
    'salas' => 'SELECT id_sala, nombre, capacidad FROM salas ORDER BY nombre',
    'mesas' => 'SELECT mesas.id_mesa, mesas.capacidad, salas.nombre AS sala_nombre 
                 FROM mesas 
                 LEFT JOIN salas ON mesas.id_sala = salas.id_sala ORDER BY salas.nombre',
    'sillas' => 'SELECT COUNT(sillas.id_silla) AS total_sillas, mesas.id_mesa, salas.nombre AS sala_nombre 
                 FROM sillas 
                 LEFT JOIN mesas ON sillas.id_mesa = mesas.id_mesa 
                 LEFT JOIN salas ON mesas.id_sala = salas.id_sala
                 GROUP BY mesas.id_mesa, salas.nombre ORDER BY salas.nombre'
]);

$query = SELECT_ACTIONS[$selected] ?? SELECT_ACTIONS['salas'];

// Si la opción seleccionada es 'mesas' y se ha seleccionado una sala, modificar la consulta
if ($selected === 'mesas' && isset($_POST['sala']) && $_POST['sala'] !== '') {
    $sala = $_POST['sala'];
    $query = "SELECT mesas.id_mesa, mesas.capacidad, salas.nombre AS sala_nombre 
              FROM mesas 
              LEFT JOIN salas ON mesas.id_sala = salas.id_sala
              WHERE salas.id_sala = :sala
              ORDER BY salas.nombre";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':sala', $sala, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($selected === 'sillas' && isset($_POST['sala']) && $_POST['sala'] !== '') {
    $sala = $_POST['sala'];
    $query = "SELECT mesas.id_mesa, salas.nombre AS sala_nombre, 
              IFNULL(COUNT(sillas.id_silla), 'No hay sillas aún') AS total_sillas
              FROM mesas 
              LEFT JOIN sillas ON sillas.id_mesa = mesas.id_mesa 
              LEFT JOIN salas ON mesas.id_sala = salas.id_sala
              WHERE salas.id_sala = :sala
              GROUP BY mesas.id_mesa, salas.nombre ORDER BY salas.nombre";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':sala', $sala, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no se ha seleccionado una sala, traer todas las mesas o sillas
    if ($selected === 'mesas') {
        $data = $con->query("SELECT mesas.id_mesa, mesas.capacidad, salas.nombre AS sala_nombre 
                             FROM mesas 
                             LEFT JOIN salas ON mesas.id_sala = salas.id_sala ORDER BY salas.nombre")->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($selected === 'sillas') {
        $data = $con->query("SELECT COUNT(sillas.id_silla) AS total_sillas, mesas.id_mesa, salas.nombre AS sala_nombre 
                             FROM sillas 
                             LEFT JOIN mesas ON sillas.id_mesa = mesas.id_mesa 
                             LEFT JOIN salas ON mesas.id_sala = salas.id_sala
                             GROUP BY mesas.id_mesa, salas.nombre ORDER BY salas.nombre")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si es "salas", simplemente usar la consulta por defecto
        $data = $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar</title>
    <link rel="stylesheet" href="../CSS/crud.css">
</head>
<body id="bodyGen">
 <a href="manager_home.php" class="btn btn-success">Volver</a>

    <h1>Administrar Recursos</h1>

    <form method="POST" action="">
        <label for="tabla">Selecciona una tabla:</label>
        <select name="tabla" id="tabla" onchange="this.form.submit(); actualizarBotonAnadir();">
            <option value="salas" <?= $selected === 'salas' ? 'selected' : '' ?>>Salas</option>
            <option value="mesas" <?= $selected === 'mesas' ? 'selected' : '' ?>>Mesas</option>
            <option value="sillas" <?= $selected === 'sillas' ? 'selected' : '' ?>>Sillas</option>
        </select>
    </form>

    <!-- Mostrar select para salas solo cuando se selecciona "Mesas" o "Sillas" -->
    <?php if ($selected === 'mesas' || $selected === 'sillas'): ?>
        <form method="POST" action="">
            <label for="sala">Selecciona una sala:</label>
            <select name="sala" id="sala" onchange="this.form.submit();">
                <option value="">Todas las salas</option>
                <?php foreach ($salas as $sala): ?>
                    <option value="<?= $sala['id_sala'] ?>" <?= ($_SESSION['selected_sala'] == $sala['id_sala']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sala['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="tabla" value="<?= $selected ?>">
        </form>
    <?php endif; ?>

    <div id="botonAnadir"> 
        
    </div>  

   

    <br><br>

    <table class="table">
    <thead>
        <?php if ($selected === 'salas'): ?>
            <tr>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Acciones</th>
            </tr>
        <?php elseif ($selected === 'mesas'): ?>
            <tr>
                <th>Mesa</th>
                <th>Capacidad</th>
                <th>Sala</th>
                <th>Acciones</th>
            </tr>
        <?php else: ?>
            <tr>
                <th>Total de Sillas</th>
                <th>Mesa</th>
                <th>Sala</th>
                <th>Acciones</th>
            </tr>
        <?php endif; ?>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <?php if ($selected === 'sillas'): ?>
                    <td><?= htmlspecialchars($row['total_sillas']) ?></td>
                    <td><?= htmlspecialchars($row['id_mesa']) ?></td>
                    <td><?= htmlspecialchars($row['sala_nombre']) ?></td>
                <?php elseif ($selected === 'mesas'): ?>
                    <td><?= htmlspecialchars($row['id_mesa'] ?? 'No ID') ?></td> <!-- Verifica si 'id_mesa' existe -->
                    <td><?= htmlspecialchars($row['capacidad']) ?></td>
                    <td><?= htmlspecialchars($row['sala_nombre']) ?></td>
                <?php else: ?>
                    <?php foreach ($row as $key => $value): ?>
                        <?php if ($key !== 'id_sala'): ?>
                            <td><?= htmlspecialchars($value) ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <td>
                    <!-- Enlace para editar según el tipo -->
                    <?php if ($selected === 'salas'): ?>
                        <a href="editar_salas.php?id=<?= $row['id_sala'] ?>" class="btn btn-primary">Editar</a>
                        <a href="eliminar_salas.php?tabla=<?= $selected ?>&id=<?= $row['id_sala'] ?>" class="btn btn-danger">Eliminar</a>
                    <?php elseif ($selected === 'mesas'): ?>
                        <a href="editar_mesas.php?id=<?= $row['id_mesa'] ?>" class="btn btn-primary">Editar</a>
                        <a href="eliminar_mesas.php?tabla=<?= $selected ?>&id=<?= $row['id_mesa'] ?>" class="btn btn-danger">Eliminar</a>
                    <?php elseif ($selected === 'sillas'): ?>
                        <a href="editar_sillas.php?id=<?= $row['id_mesa'] ?>" class="btn btn-primary">Editar</a>
                        <a href="eliminar_sillas.php?tabla=<?= $selected ?>&id=<?= $row['id_mesa'] ?>"  class="btn btn-danger">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <div id="botonAnadir"></div> 


    <script>
        function actualizarBotonAnadir() {
            const selectedTable = document.getElementById('tabla').value;
            const botonAnadir = document.getElementById('botonAnadir');
            botonAnadir.innerHTML = ''; 

            if (selectedTable === 'sillas') {
                botonAnadir.innerHTML = '<button onclick="window.location.href=\'añadir_sillas.php?tabla=sillas\'" class="btn btn-success">Añadir Sillas</button>';
            } else if (selectedTable === 'mesas') {
                botonAnadir.innerHTML = '<button onclick="window.location.href=\'añadir_mesas.php?tabla=mesas\'" class="btn btn-success">Añadir Mesas</button>';
            } else if (selectedTable === 'salas') {
                botonAnadir.innerHTML = '<button onclick="window.location.href=\'añadir_salas.php?tabla=salas\'" class="btn btn-success">Añadir Salas</button>';
            }
        }

        actualizarBotonAnadir();
    </script>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <script>
            alert('<?= $_SESSION['mensaje'] ?>');
        </script>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
</body>
</html>
