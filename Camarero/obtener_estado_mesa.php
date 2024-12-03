<?php
// obtener_estado_mesa.php

include('../conexion.php');

if (isset($_GET['id_mesa'])) {
    $id_mesa = $_GET['id_mesa'];

    try {
        // Preparar y ejecutar la consulta para obtener el estado de la mesa
        $stmt = $pdo->prepare("SELECT estado FROM mesas WHERE id_mesa = :id_mesa");
        $stmt->execute(['id_mesa' => $id_mesa]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $estado = $row['estado'];

            if ($estado === 'ocupada') {
                // Preparar y ejecutar la consulta para obtener el número de sillas
                $stmt_sillas = $pdo->prepare("SELECT sillas FROM ocupaciones WHERE id_mesa = :id_mesa AND fecha_libera IS NULL");
                $stmt_sillas->execute(['id_mesa' => $id_mesa]);

                if ($stmt_sillas->rowCount() > 0) {
                    $row_sillas = $stmt_sillas->fetch(PDO::FETCH_ASSOC);
                    $sillas = $row_sillas['sillas'];
                } else {
                    $sillas = 0;
                }
            } else {
                $sillas = 0;
            }

            echo json_encode(['estado' => $estado, 'sillas' => $sillas]);
        } else {
            echo json_encode(['estado' => 'libre', 'sillas' => 0]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['estado' => 'libre', 'sillas' => 0]);
}
?>
