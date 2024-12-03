<?php
// obtener_estado_mesa.php

include('../conexion.php');

if (isset($_GET['id_mesa'])) {
    $id_mesa = $_GET['id_mesa'];

    $sql = "SELECT estado FROM mesas WHERE id_mesa = $id_mesa";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $estado = $row['estado'];

        if ($estado === 'ocupada') {
            $sql_sillas = "SELECT sillas FROM ocupaciones WHERE id_mesa = $id_mesa AND fecha_libera IS NULL";
            $result_sillas = mysqli_query($con, $sql_sillas);
            
            if (mysqli_num_rows($result_sillas) > 0) {
                $row_sillas = mysqli_fetch_assoc($result_sillas);
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
} else {
    echo json_encode(['estado' => 'libre', 'sillas' => 0]);
}

mysqli_close($con);
?>
