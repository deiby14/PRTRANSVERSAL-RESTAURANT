<?php
// actualizar_estado_mesa.php

include('../conexion.php');

// Iniciar la sesión para verificar el usuario
session_start();

// Verificar que el usuario esté en sesión
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    
    // Verificar si los datos fueron enviados por POST
    if (isset($_POST['id_mesa']) && isset($_POST['estado']) && isset($_POST['sillas'])) {
        $id_mesa = $_POST['id_mesa'];
        $estado = $_POST['estado'];
        $sillas = $_POST['sillas'];
        
        // Verificar que el estado sea válido
        if ($estado === 'ocupada' || $estado === 'libre') {
            // Si la mesa va a ser ocupada, primero verificamos que la cantidad de sillas no exceda su capacidad
            if ($estado === 'ocupada') {
                $stmtCapacidad = $pdo->prepare("SELECT capacidad FROM mesas WHERE id_mesa = :id_mesa");
                $stmtCapacidad->execute(['id_mesa' => $id_mesa]);
                $mesa = $stmtCapacidad->fetch(PDO::FETCH_ASSOC);
                
                if ($sillas > $mesa['capacidad']) {
                    echo json_encode(['message' => 'Número de sillas excede la capacidad de la mesa']);
                    exit;
                }
            }

            // Consulta para actualizar el estado de la mesa
            $stmtUpdate = $pdo->prepare("UPDATE mesas SET estado = :estado WHERE id_mesa = :id_mesa");
            
            if ($stmtUpdate->execute(['estado' => $estado, 'id_mesa' => $id_mesa])) {
                if ($estado === 'ocupada') {
                    // Verificar si ya existe una ocupación sin liberar para esta mesa
                    $stmtCheck = $pdo->prepare("SELECT id_ocupacion FROM ocupaciones WHERE id_mesa = :id_mesa AND fecha_libera IS NULL");
                    $stmtCheck->execute(['id_mesa' => $id_mesa]);
                    
                    if ($stmtCheck->rowCount() > 0) {
                        // Actualizar la fecha de ocupación y número de sillas si ya hay un registro
                        $stmtUpdateOcupacion = $pdo->prepare("UPDATE ocupaciones SET fecha_ocupacion = NOW(), id_usuario = :id_usuario, sillas = :sillas WHERE id_mesa = :id_mesa AND fecha_libera IS NULL");
                        $stmtUpdateOcupacion->execute(['id_usuario' => $id_usuario, 'sillas' => $sillas, 'id_mesa' => $id_mesa]);
                    } else {
                        // Crear un nuevo registro de ocupación
                        $stmtInsert = $pdo->prepare("INSERT INTO ocupaciones (id_mesa, id_usuario, sillas, fecha_ocupacion) VALUES (:id_mesa, :id_usuario, :sillas, NOW())");
                        $stmtInsert->execute(['id_mesa' => $id_mesa, 'id_usuario' => $id_usuario, 'sillas' => $sillas]);
                    }
                } else {
                    // Actualizar la fecha de liberación cuando la mesa pasa a "libre"
                    $stmtUpdateLibera = $pdo->prepare("UPDATE ocupaciones SET fecha_libera = NOW() WHERE id_mesa = :id_mesa AND fecha_libera IS NULL");
                    $stmtUpdateLibera->execute(['id_mesa' => $id_mesa]);
                }
                
                // Respuesta exitosa si todos los queries funcionan
                echo json_encode(['message' => 'Estado de la mesa actualizado correctamente', 'estado' => $estado, 'sillas' => $estado === 'ocupada' ? $sillas : 0]);
            } else {
                // Error en la actualización
                echo json_encode(['message' => 'Error al actualizar estado']);
            }
        } else {
            echo json_encode(['message' => 'Estado no válido']);
        }
    } else {
        echo json_encode(['message' => 'Parámetros incorrectos']);
    }
} else {
    echo json_encode(['message' => 'Usuario no autenticado']);
}
?>
