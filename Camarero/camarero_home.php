<?php
session_start();
include_once('../conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar que el usuario esté logueado
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el tipo de usuario para asegurarse de que solo el manager pueda acceder
$camareroActual = $_SESSION['id_usuario'];
$stmtComprobar = $con->prepare("SELECT tipo_usuario FROM usuarios WHERE id_usuario = :id_usuario");
$stmtComprobar->execute(['id_usuario' => $camareroActual]);
$tipoUsuario = $stmtComprobar->fetchColumn();

if ($tipoUsuario != "camarero") {
    header('Location: camarero_home.php');
    exit();
}

// Obtener todas las salas desde la base de datos
$stmtSalas = $con->query("SELECT * FROM salas");
$salas = $stmtSalas->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.0/mdb.min.css" rel="stylesheet">
    <!-- Bootstrap JS (y dependencias) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</head>

<body id="bodyGen">
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
        <div class="container">
            <!-- Logo como botón de hamburguesa -->
            <a href="./manager_home.php" data-bs-toggle="collapse" data-bs-target="#navbarButtonsExample" aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                <img id="LogoNav" src="../img/LOGO-REST.png" alt="Logo" />
            </a>
            <div class="collapse navbar-collapse" id="navbarButtonsExample">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a id="aNav" class="nav-link" href="./manager_home.php">Inicio</a>
                    </li>
                </ul>
                <div id="divSession">
                    <h4>Bienvenid@ <?php echo htmlspecialchars($_SESSION['nombre']); ?></h4>
                </div>
                <div class="d-flex align-items-center">
                    <a href="../CerrarSesion.php" class="btn btn-primary me-3">Cerrar sesión</a>
                    <a href="camarero_home.php" class="btn btn-secondary">Volver</a>
                    <a href="form_reservas.php" class="btn btn-secondary">Hacer reservas</a>
                </div>
            </div>
        </div>
    </nav>

    <h1>Seleccionar Sala</h1>
    <div class="salas-container">
        <?php
        // Mostrar las salas dinámicamente
        foreach ($salas as $sala) {
            echo '<div class="sala-item">';
            echo '<a href="../Manager/mostrar_mesas.php?id_sala=' . $sala['id_sala'] . '">' . htmlspecialchars($sala['nombre']) . '</a>';
            echo '</div>';
        }
        ?>
    </div>

</body>
</html>

<?php
$con = null; // Cerrar la conexión
?>
