<?php

include_once("../conexion.php");
session_start();

// Recojemos el valor de la sesión 'id_usuario' y lo guardamos en una variable
$camareroActual = $_SESSION['id_usuario'];

try {
    // Preparar la consulta para verificar el tipo de usuario
    $stmtComprobar = $pdo->prepare("SELECT tipo_usuario FROM usuarios WHERE id_usuario = :id_usuario");
    $stmtComprobar->execute(['id_usuario' => $camareroActual]);
    $tipoUsuario = $stmtComprobar->fetchColumn();

    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ' . '../index.php');
        exit();
    } elseif ($tipoUsuario != "manager") {
        header('Location: ' . '../Camarero/camarero_home.php');
        exit();
    } else {
        // Recojemos el valor del input para filtrar por sala y el valor del input para filtrar por fecha
        $buscar_sala = isset($_GET['buscarSala']) ? $_GET['buscarSala'] : '';
        $buscar_camarero = isset($_GET['buscarCamarero']) ? $_GET['buscarCamarero'] : '';
        $buscar_estado = isset($_GET['buscarEstado']) ? $_GET['buscarEstado'] : '';
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

        // Comprobamos si se se recibe el valor de 'pagina', si se recibe, lo recojemos, sino nos lleva a 'Historial Mesas'
        $paginas = isset($_GET['pagina']) ? $_GET['pagina'] : 'default';

        // Creamos una variable, que más tarde comprobará si la consulta que estamos creando ya tiene un ORDER BY definido
        $tieneORDERBY = false;

        // CAMBIAR ENTRE PÁGINAS (OCUPACIONES / SALAS / SALAS MÁS USADAS / HISTORIAL MESAS)
        switch ($paginas) {
            case 'historial':
                $sqlHistorial = "SELECT mesas.id_mesa AS id_mesa, mesas.capacidad, mesas.estado, mesas.id_sala,
                    salas.id_sala, salas.nombre, salas.capacidad,
                    ocupaciones.id_ocupacion, ocupaciones.id_mesa AS id_mesas_ocupadas, ocupaciones.sillas, ocupaciones.fecha_ocupacion, ocupaciones.fecha_libera,
                    usuarios.id_usuario, usuarios.nombre_completo, usuarios.contraseña, usuarios.tipo_usuario
                    FROM mesas
                    INNER JOIN salas ON salas.id_sala = mesas.id_sala
                    LEFT JOIN ocupaciones ON ocupaciones.id_mesa = mesas.id_mesa
                    LEFT JOIN usuarios ON usuarios.id_usuario = ocupaciones.id_usuario";
                $tieneORDERBY = false;
                break;

            case 'sala':
                $sqlHistorial = "SELECT salas.id_sala, salas.nombre, salas.capacidad, 
                    mesas.id_mesa, mesas.capacidad AS capacidad_mesa, mesas.estado, mesas.id_sala
                    FROM salas
                    INNER JOIN mesas ON salas.id_sala = mesas.id_sala
                    GROUP BY salas.id_sala";
                $tieneORDERBY = false;
                break;

            case 'uso':
                $sqlHistorial = "SELECT ocupaciones.id_mesa, COUNT(ocupaciones.id_mesa) AS numero_de_usos, 
                    GROUP_CONCAT(ocupaciones.id_ocupacion) AS ocupaciones_concatenadas, mesas.capacidad, mesas.estado, mesas.id_sala,
                    salas.id_sala, salas.nombre, salas.capacidad
                    FROM ocupaciones
                    INNER JOIN mesas ON mesas.id_mesa = ocupaciones.id_mesa
                    INNER JOIN salas ON salas.id_sala = mesas.id_sala
                    GROUP BY ocupaciones.id_mesa
                    ORDER BY numero_de_usos DESC";
                $tieneORDERBY = true;
                break;

            default:
                $sqlHistorial = "SELECT ocupaciones.id_ocupacion, ocupaciones.id_mesa, ocupaciones.id_usuario, 
                ocupaciones.fecha_ocupacion, ocupaciones.fecha_libera, ocupaciones.sillas, mesas.id_mesa, mesas.capacidad, 
                mesas.estado, mesas.id_sala,
                salas.nombre,
                usuarios.id_usuario, usuarios.nombre_completo, usuarios.contraseña, usuarios.tipo_usuario
                FROM ocupaciones
                INNER JOIN mesas ON mesas.id_mesa = ocupaciones.id_mesa
                INNER JOIN salas ON salas.id_sala = mesas.id_sala
                INNER JOIN usuarios ON usuarios.id_usuario = ocupaciones.id_usuario";
                $tieneORDERBY = false;
                break;
        }

        // AÑADIMOS LOS FILTROS
        $filtros = [];
        $parametros = [];

        if ($buscar_camarero != "") {
            $filtros[] = "usuarios.nombre_completo LIKE :buscarCamarero";
            $parametros['buscarCamarero'] = '%' . $buscar_camarero . '%';
        }

        if ($buscar_sala != "") {
            $filtros[] = "salas.nombre LIKE :buscarSala";
            $parametros['buscarSala'] = '%' . $buscar_sala . '%';
        }

        if ($buscar_estado != "") {
            $filtros[] = "mesas.estado LIKE :buscarEstado";
            $parametros['buscarEstado'] = '%' . $buscar_estado . '%';
        }

        if ($fecha != "") {
            $filtros[] = "ocupaciones.fecha_ocupacion LIKE :fecha";
            $parametros['fecha'] = '%' . $fecha . '%';
        }

        if (!empty($filtros)) {
            $sqlHistorial .= " WHERE " . implode(" AND ", $filtros);
        }

        if (!$tieneORDERBY) {
            $sqlHistorial .= " ORDER BY salas.id_sala, mesas.id_mesa";
        }

        // Preparamos y ejecutamos la consulta
        $stmtPáginaHistorial = $pdo->prepare($sqlHistorial);
        $stmtPáginaHistorial->execute($parametros);
        $resultado = $stmtPáginaHistorial->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mesas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <style>
        .divNavbar {
            width: 160%;
        }

        @media (max-width: 1680px) {
            .divNavbar {
                width: 3000%;
            }
        }
    </style>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">

            <!-- Botón para hacer el navbar responsive (mete todos los elementos en un responsive) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- FILTROS Y PÁGINAS -->
            <div class="collapse navbar-collapse divNavbar" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <!-- Los filtros solo se mostrarán en la página de 'Ocupaciones' -->
                    <?php if ($paginas != 'historial' && $paginas != 'uso' && $paginas != 'sala') : ?>
                        <form class="form-inline my-2 my-lg-0 d-flex align-items-center" method="GET">
                            <input class="form-control mr-sm-2" type="search" name="buscarCamarero" placeholder="Buscar Camarero" aria-label="Buscar Camarero" value="<?php if(isset($_GET['buscarCamarero'])) {echo $_GET['buscarCamarero'];} ?>">
                            <input class="form-control mr-sm-2" style="margin-left: 10px;" type="search" name="buscarSala" placeholder="Buscar Sala" aria-label="Buscar Sala" value="<?php if(isset($_GET['buscarSala'])) {echo $_GET['buscarSala'];} ?>">
                            <input class="form-control mr-sm-2" style="margin-left: 10px;" type="search" name="buscarEstado" placeholder="Buscar Estado" aria-label="Buscar Estado" value="<?php if(isset($_GET['buscarEstado'])) {echo $_GET['buscarEstado'];} ?>">
                            <input style="color: #000000A6; margin-left: 10px;" class="form-control me-2" type="date" id="start" name="fecha" value="<?php if(isset($_GET['fecha'])) {echo $_GET['fecha'];} ?>"/>
                            <button type="submit" class="btn btn-primary" style="height: 93%;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 21">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                            </button>
                            <button type="submit" class="btn btn-danger" name="limpiar_filtros" style="height: 93%; margin-left: 10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="26" fill="currentColor" class="bi bi-eraser-fill" viewBox="0 0 16 21">
                                <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm.66 11.34L3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                            </svg>
                            </button>
                        </form>
                    <?php

                        if (isset($_GET['limpiar_filtros'])) {
                            // Redirigir a la misma página sin parámetros
                            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
                            exit();
                        }

                    endif; ?>

                    <!-- Página Historial Mesas (predeterminada) -->
                    <li class="nav-item" style="margin-left: 10px;">
                        <a class="nav-link" href="?pagina=default">Ocupaciones</a>
                    </li>

                    <!-- Página Ocupaciones -->
                    <li class="nav-item">
                        <a class="nav-link" href="?pagina=historial">Historial Mesas</a>
                    </li>

                    <!-- Página Salas -->
                    <li class="nav-item">
                        <a class="nav-link" href="?pagina=sala">Salas</a>
                    </li>

                    <!-- Página Mesas más usadas -->
                    <li class="nav-item" style="margin-right: 10px;">
                        <a class="nav-link" href="?pagina=uso">Mesas más usadas</a>
                    </li>
                    <li>
                        <a href="./manager_home.php" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 18">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- Dependiendo de que opción escojamos en el navbar, nos mostrará una página u otra -->
        <?php if ($paginas == 'historial') : ?>
            <h2>Historial mesas</h2>
        <?php elseif ($paginas == 'uso') : ?>
            <h2>Mesas más usadas a menos</h2>
        <?php elseif ($paginas == 'sala') : ?>
            <h2>Salas</h2>
        <?php else : ?>
            <h2>Ocupaciones</h2>
        <?php endif; ?>

        <!-- Creamos la tabla, y dependiendo de que opción hayamos escojido, creamos un encabezado (para las columnas) u otro -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <?php if ($paginas == 'historial') : ?>

                        <th>Ocupación</th>
                        <th>Mesa</th>
                        <th>Sillas ocupadas</th>
                        <th>Sala</th>
                        <th>Estado</th>
                        <th>Camarero</th>
                        <th>Fecha ocupación</th>
                        <th>Fecha liberación</th>

                    <?php elseif ($paginas == 'uso') : ?>

                        <th>Número de Usos</th>
                        <th>Mesa</th>
                        <th>Salas</th>
                        <th>Ocupaciones</th>

                    <?php elseif ($paginas == 'sala') : ?>

                        <th>Nombre Sala</th>
                        <th>Capacidad Sala</th>

                    <?php else : ?>

                        <th>Ocupación</th>
                        <th>Mesa</th>
                        <th>Sillas ocupadas</th>
                        <th>Sala</th>
                        <th>Estado</th>
                        <th>Camarero</th>
                        <th>Fecha ocupación</th>
                        <th>Fecha liberaci��n</th>

                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php

                // Si existe $resultado y devuelve filas, entonces
                if ($resultado && mysqli_num_rows($resultado) > 0) {

                    // Recoje las filas y las guarda una una variable (por cada una), llamada $fila
                    // , para mostrar los datos de la tabla
                    while ($fila = mysqli_fetch_assoc($resultado)) {


                        echo "<tr>";

                            // MOSTRAR OCUPACIONES
                            if ($paginas == 'historial') {

                            // Si esta mesa ha sido ocupada (si existe 'id_ocupacion') mostramos 'Ocupación (y su ID)'
                            // si no, mostramos un mensaje de que aún no ha sido ocupada
                            if ($fila['id_ocupacion'] != NULL) {

                                echo "<td style='width: 23%;' >Ocupación " . $fila['id_ocupacion'] . "</td>";
                            } else {

                                echo "<td style='width: 23%;'>Esta mesa aún no se ha ocupado</td>";
                            }

                            // Si la ID de esta mesa está ocupada, mostraremos la ID de la mesa desde la tabla 'ocupaciones' ('id_mesas_ocupadas'),
                            // Si no lo está, mostraremos la ID de la mesa de la tabla 'mesas'
                            if ($fila['id_mesas_ocupadas'] != NULL) {

                                echo "<td style='width: 6%;'>Mesa " . $fila['id_mesas_ocupadas'] . "</td>";
                            } else {

                                echo "<td style='width: 6%;'>Mesa " . $fila['id_mesa'] . "</td>";
                            }


                            if ($fila['sillas'] != NULL) {

                                echo "<td style='width: 6%;'>" . $fila['sillas'] . "</td>";
                            } else {

                                echo "<td style='width: 6%;'> No hay sillas ocupadas aún</td>";
                            }


                            // Mostramos el nombre de la sala y su estado actual
                            echo "<td>" . $fila['nombre'] . "</td>";
                            echo "<td>" . $fila['estado'] . "</td>";

                            // Si esta mesa ha sido asignada por un camarero (si existe una id_usuario),
                            // mostramos el nombre de este, si no, le mostramos un mensaje de que ningún camarero la ha asignado
                            if ($fila['id_usuario'] != NULL) {

                                echo "<td style='width: 25%;'>" . $fila['nombre_completo'] . "</td>";
                            } else {

                                echo "<td style='width: 25%;'> Ningún camarero ha asignado esta mesa </td>";
                            }

                            // Si la mesa se ha ocupado (la fecha de ocupación existe), la mostramos,
                            // Si no, mostramos un mensaje  de que a
                            if ($fila['fecha_ocupacion'] != NULL) {

                                echo "<td style='width: 20%;'>" . $fila['fecha_ocupacion'] . "</td>";
                            } else {

                                echo "<td style='width: 20%;'> Esta mesa aún no ha sido ocupada</td>";
                            }

                            // Si existe la fecha de liberación de la ocupación, se muestra
                            // Si NO existe, Y no está ocupada (en la tabla "ocupaciones"), está ocupada
                            // Si NO existe, Y está ocupada (en la tabla "ocupaciones"), aún no se ha ocupado
                            if ($fila['fecha_libera'] != NULL) {

                                echo "<td style='width: 20%;'>" . $fila['fecha_libera'] . "</td>";
                            } elseif ($fila['fecha_libera'] == NULL && $fila['id_ocupacion'] != NULL) {

                                    echo "<td style='width: 20%;'> Esta mesa actualmente está siendo ocupada</td>";
                                    
                                } elseif ($fila['id_ocupacion'] == NULL) {
                                    
                                    echo "<td style='width: 20%;'> Esta mesa aún no se ha ocupado </td>";

                                }
                                

                            // MOSTRAR DE LAS MESAS MÁS USADAS A MENOS
                            } elseif ($paginas == 'uso') {

                                // Si solo se ha usado una vez, mostramos (el número de veces, y el texto 'vez')
                                if ($fila['numero_de_usos'] == 1) {

                                    echo "<td>".$fila['numero_de_usos']." vez</td>";

                                } else {

                                    echo "<td>".$fila['numero_de_usos']." veces</td>";

                                }
                                
                                // Mostramos las mesas y el nombre de la sala en la que están
                                echo "<td>Mesa ".$fila['id_mesa']."</td>";                                
                                echo "<td>".$fila['nombre']."</td>";

                                // Mostramos las ocupaciones en las que se ha usado esta mesa 
                                echo "<td>";

                                    // Buscamos todas las comas en la cadena 'ocupaciones_concatenadas' y lo reemplaza por ', Ocupación '
                                    // de manera que queda "Ocupación 1, Ocupación 2, Ocupación 3, etc..."
                                    echo "Ocupación " . str_replace(',', ', Ocupación ', $fila['ocupaciones_concatenadas']);

                                echo "</td>";


                            // MOSTRAR SALAS
                            } elseif ($paginas == 'sala') {

                                // Mostramos el nombre de la sala y su capacidad
                                echo "<td>".$fila['nombre']."</td>";
                                echo "<td>".$fila['capacidad']."</td>";


                            // MOSTRAR TANTO LAS MESAS OCUPADAS Y NO OCUPADAS
                            } else {

                                



                                // Si esta mesa ha sido ocupada (si existe 'id_ocupacion') mostramos 'Ocupación (y su ID)'
                                // si no, mostramos un mensaje de que aún no ha sido ocupada
                                if ($fila['id_ocupacion'] != NULL) {

                                    echo "<td>Ocupación ".$fila['id_ocupacion']."</td>";

                                } else {

                                    echo "<td>Esta mesa aún no ha sido ocupada</td>";

                                }
                                
                                // Mostramos las mesas, las sillas, las salas y su estado actual
                                echo "<td>Mesa ".$fila['id_mesa']."</td>";

                                echo "<td>".$fila['sillas']."</td>";

                                echo "<td>".$fila['nombre']."</td>";

                                echo "<td>".$fila['estado']."</td>";

                                // Si esta mesa ha sido asignada por un camarero (si existe una id_usuario),
                                // mostramos el nombre de este, si no, le mostramos un mensaje de que ningún camarero la ha asignado
                                if ($fila['id_usuario'] != NULL) {

                                    echo "<td>".$fila['nombre_completo']."</td>";

                                } else {

                                    echo "<td> Ningún camarero ha asignado esta mesa </td>";

                                }

                                // Mostramos la fecha en la que se ha ocupado la mesa
                                echo "<td>".$fila['fecha_ocupacion']."</td>";

                                // Si la fecha en la que se ha desocupado existe, la mostramos
                                // Si no, mostramos un mensaje de que sigue ocupada
                                if ($fila['fecha_libera'] != NULL) {

                                    echo "<td>".$fila['fecha_libera']."</td>";

                                } else {

                                    echo "<td> Esta mesa actualmente está siendo ocupada </td>";

                                }

                            }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay resultados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>