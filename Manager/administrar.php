<?php
session_start();
require_once '../conexion.php';

// Consultas dinámicas basadas en la selección
define('SELECT_ACTIONS', [
    'salas' => 'SELECT * FROM salas',
    'mesas' => 'SELECT mesas.id_mesa, mesas.capacidad, mesas.estado, salas.nombre AS sala_nombre FROM mesas LEFT JOIN salas ON mesas.id_sala = salas.id_sala',
    'sillas' => 'SELECT sillas.id_silla, sillas.id_mesa, salas.nombre AS sala_nombre 
                 FROM sillas 
                 LEFT JOIN mesas ON sillas.id_mesa = mesas.id_mesa 
                 LEFT JOIN salas ON mesas.id_sala = salas.id_sala'
]);

$selected = $_GET['tabla'] ?? 'salas'; // Predeterminado a 'salas'
$query = SELECT_ACTIONS[$selected] ?? SELECT_ACTIONS['salas'];
$data = $con->query($query)->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Administrar Recursos</h1>

    <form method="GET" action="">
        <label for="tabla">Selecciona una tabla:</label>
        <select name="tabla" id="tabla" onchange="this.form.submit()">
            <option value="salas" <?= $selected === 'salas' ? 'selected' : '' ?>>Salas</option>
            <option value="mesas" <?= $selected === 'mesas' ? 'selected' : '' ?>>Mesas</option>
            <option value="sillas" <?= $selected === 'sillas' ? 'selected' : '' ?>>Sillas</option>
        </select>
    </form>

    <table border="1">
        <thead>
        <?php if ($selected === 'salas'): ?>
            <tr>
                <th>ID Sala</th>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Acciones</th>
            </tr>
        <?php elseif ($selected === 'mesas'): ?>
            <tr>
                <th>ID Mesa</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Sala</th>
                <th>Acciones</th>
            </tr>
        <?php else: ?>
            <tr>
                <th>ID Silla</th>
                <th>ID Mesa</th>
                <th>Sala</th>
                <th>Acciones</th>
            </tr>
        <?php endif; ?>
        </thead>
        <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?= htmlspecialchars($value) ?></td>
                <?php endforeach; ?>
                <td>
                    <a href="editar.php?tabla=<?= $selected ?>&id=<?= $row[array_key_first($row)] ?>">Editar</a>
                    <a href="eliminar.php?tabla=<?= $selected ?>&id=<?= $row[array_key_first($row)] ?>" onclick="return confirm('¿Estás seguro de eliminar este registro?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="agregar.php?tabla=<?= $selected ?>">Añadir nuevo <?= ucfirst($selected) ?></a>
</body>
</html>
