<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./CSS/styles.css">
    <script src="./Js/validaciones.js" defer></script>
</head>

<body id="bodyLogIn">

    <div id="containerLogIn">
        <div class="left-section">
            <form action="./validaciones/validacion.php" method="POST" autocomplete="off">
                <div class="inputs">
                    <!-- Ecitar inyeccion HTML -->
                    <label class="labelLogIn" for="nombre">Usuario:</label>
                    <input class="inputLogIn" type="text" id="nombre" name="nombre" placeholder="Introducir usuario" 
                    value="<?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') : ''; ?>"> 
                    
                    <br>
                    <!-- validacion usuario javascript y en rojo -->
                    <span id="error-nombre" style="color: red;"></span> 
                </div>

                <div class="inputs">
                    <label class="labelLogIn" for="contraseña">Contraseña:</label>
                    <input class="inputLogIn" type="password" id="contraseña" name="contrasena" placeholder="Introducir contraseña" autocomplete="off">
                     <br>
                     <!-- validacion javascript para contraseña -->
                    <span id="error-contraseña" style="color: red;"></span> 
                </div>

                <button type="submit" name="login" class="botonLogIn">Iniciar sesión</button>

                <!-- Mensaje de error si las credenciales son incorrectas -->
                <?php if (isset($_GET['error']) && $_GET['error'] == 'incorrecto') { ?>
                    <p style="color: red;">Usuario o contraseña incorrectos</p>
                <?php } ?>
            </form>
        </div>

        <div class="right-section">
            <img src="./img/LOGO-REST.png" alt="Logo" id="logoLogIn">
        </div>
    </div>

</body>

</html>
