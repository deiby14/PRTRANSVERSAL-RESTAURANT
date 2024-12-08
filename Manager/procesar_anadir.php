<?php
try {
    // Incluir la conexión a la base de datos (asegúrate de que esté configurada correctamente)
    require_once 'conexion.php'; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tipo = $_POST['tipo']; // Tipo de elemento (mesas, salas, sillas)

        // Validación de datos
        if ($tipo === 'mesas') {
            if (isset($_POST['capacidad'], $_POST['id_sala'])) {
                $capacidad = $_POST['capacidad'];
                $id_sala = $_POST['id_sala'];

                // Validar que los datos no estén vacíos
                if (!empty($capacidad) && !empty($id_sala)) {
                    // Insertar en la tabla de mesas
                    $stmt = $con->prepare("INSERT INTO mesas (capacidad, estado, id_sala) VALUES (:capacidad, 'libre', :id_sala)");
                    $stmt->bindParam(':capacidad', $capacidad);
                    $stmt->bindParam(':id_sala', $id_sala);
                    $stmt->execute();

                    echo "<script>alert('Mesa añadida con éxito.'); window.location.href='administrar.php';</script>";
                } else {
                    echo "<script>alert('Por favor, complete todos los campos.'); window.location.href='administrar.php';</script>";
                }
            }
        } elseif ($tipo === 'sillas') {
            if (isset($_POST['cantidad'], $_POST['id_mesa'])) {
                $cantidad = $_POST['cantidad'];
                $id_mesa = $_POST['id_mesa'];

                // Validar que los datos no estén vacíos
                if (!empty($cantidad) && !empty($id_mesa)) {
                    // Insertar en la tabla de sillas
                    $stmt = $con->prepare("INSERT INTO sillas (id_mesa) VALUES (:id_mesa)");
                    $stmt->bindParam(':id_mesa', $id_mesa);

                    for ($i = 0; $i < $cantidad; $i++) {
                        $stmt->execute();
                    }

                    echo "<script>alert('Sillas añadidas con éxito.'); window.location.href='administrar.php';</script>";
                } else {
                    echo "<script>alert('Por favor, complete todos los campos.'); window.location.href='administrar.php';</script>";
                }
            }
        } elseif ($tipo === 'salas') {
            if (isset($_POST['nombre'], $_POST['capacidad'])) {
                $nombre = $_POST['nombre'];
                $capacidad = $_POST['capacidad'];

                // Validar que los datos no estén vacíos
                if (!empty($nombre) && !empty($capacidad)) {
                    // Insertar en la tabla de salas
                    $stmt = $con->prepare("INSERT INTO salas (nombre, capacidad) VALUES (:nombre, :capacidad)");
                    $stmt->bindParam(':nombre', $nombre);
                    $stmt->bindParam(':capacidad', $capacidad);
                    $stmt->execute();

                    echo "<script>alert('Sala añadida con éxito.'); window.location.href='administrar.php';</script>";
                } else {
                    echo "<script>alert('Por favor, complete todos los campos.'); window.location.href='administrar.php';</script>";
                }
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
