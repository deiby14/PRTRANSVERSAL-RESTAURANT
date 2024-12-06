<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camarero</title>
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
                    <a href="../CerrarSesion.php" class="btn btn-primary me-3">
                        Cerrar sesión
                    </a>
                    <a href="./camarero_home.php" class="btn btn-secondary">Volver</a>
                    <a href="form_reservas.php" class="btn btn-secondary">Reservas</a>
                </div>
            </div>
        </div>
    </nav>
    <div id="ocultarImg" class="">
        <div class="column-1">
            <h1 id="h1Sel">Seleccionar sala</h1>
            <div class="image-containerMan">
                <a href="" id="Comedor">
                    <h3 class="text-overlay Comedor">Comedor</h3>
                    <img class="imgMan Comedor" src="../img/ComedorBtn.jpg" alt="Comedor">
                </a>
            </div>
            <div class="image-containerMan">
                <a href="" id="Privada">
                    <h3 class="text-overlay Privada">Privada</h3>
                    <img class="imgMan Privada" src="../img/PrivadaBtn.png" alt="Privada">
                </a>
            </div>
            <div class="image-containerMan">
                <a href="" id="Terraza">
                    <h3 class="text-overlay Terraza">Terraza</h3>
                    <img class="imgMan Terraza" src="../img/TerrazaBtn.png" alt="Terraza">
                </a>
            </div>
        </div>
    </div>
    <!-- Mostrar Comedores -->

    <div id="Comedores" class="content">
        <div class="column-1 flex">
            <div>
                <h1 id="h1Sel">Seleccionar Comedor</h1>
                <div class="image-containerMan">
                    <a href="comedor1.php" id="Comedor">
                        <h3 class="text-overlay Comedor">Comedor 1</h3>
                        <img class="imgComedor" src="../img/Comedores.png" alt="Comedor">
                    </a>
                </div>
                <div class="image-containerMan">
                    <a href="comedor2.php" id="Privada">
                        <h3 class="text-overlay Privada">Comedor 2</h3>
                        <img class="imgComedor" src="../img/Comedores.png" alt="Privada">
                    </a>
                </div>
            </div>

        </div>
    </div>
    <!-- Mostrar Salas Privadas -->
    <div id="Privadas" class="content">
        <div class="column-1">
            <h1 id="h1Sel">Seleccionar Sala Privada</h1>
            <div class="flex">
                <div class="image-containerMan">
                    <a href="privada1.php" id="Comedor">
                        <h3 class="text-overlay Comedor">Privada 1</h3>
                        <img class="imgSalaPriv" src="../img/SalasPrivadas.png" alt="Comedor">
                    </a>
                </div>
                <div class="image-containerMan">
                    <a href="privada2.php" id="Privada">
                        <h3 class="text-overlay Privada">Privada 2</h3>
                        <img class="imgSalaPriv" src="../img/SalasPrivadas.png" alt="Privada">
                    </a>
                </div>
            </div>
        </div>
        <div class="column-1 flex">
            <div class="image-containerMan">
                <a href="privada3.php" id="Terraza">
                    <h3 class="text-overlay Terraza">Privada 3</h3>
                    <img class="imgSalaPriv" src="../img/SalasPrivadas.png" alt="Terraza">
                </a>
            </div>
            <div class="image-containerMan">
                <a href="privada4.php" id="Terraza">
                    <h3 class="text-overlay Terraza">Privada 4</h3>
                    <img class="imgSalaPriv" src="../img/SalasPrivadas.png" alt="Terraza">
                </a>
            </div>
        </div>
    </div>
    <!-- Mostrar Terrazas -->
    <div id="Terrazas" class="content">
        <div class="column-1">
            <h1 id="h1Sel">Seleccionar Terraza</h1>
            <div class="image-containerMan">
                <a href="terraza1.php" id="Comedor">
                    <h3 class="text-overlay Comedor">Terraza 1</h3>
                    <img class="imgTerraza" src="../img/Terrazas.png" alt="Comedor">
                </a>
            </div>
            <div class="image-containerMan">
                <a href="terraza2.php" id="Privada">
                    <h3 class="text-overlay Privada">Terraza 2</h3>
                    <img class="imgTerraza" src="../img/Terrazas.png" alt="Privada">
                </a>
            </div>
            <div class="image-containerMan">
                <a href="terraza3.php" id="Terraza">
                    <h3 class="text-overlay Terraza">Terraza 3</h3>
                    <img class="imgTerraza" src="../img/Terrazas.png" alt="Terraza">
                </a>
            </div>
        </div>
    </div>

    <script src="../Js/MostMesas.js"></script>

</body>

</html>