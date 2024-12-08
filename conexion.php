<?php

$host = "localhost";
$usuario = "root";
$contrasena = "Agustin51";
$nombre_bd = "db_restaurante";

try {
    $con = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    // Establecer el modo de error de PDO a excepción
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>
