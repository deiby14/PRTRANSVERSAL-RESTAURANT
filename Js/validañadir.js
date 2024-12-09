// Validar nombre completo

document.getElementById("nombre_completo").onblur = function() {
    validaNombre();
};

// Validar contraseña
document.getElementById("contrasena").onblur = function() {
    validaContrasena();
};

// Validar confirmar contraseña
document.getElementById("repetir_contrasena").onblur = function() {
    validaConfirmarContrasena();
};

// Validar tipo de usuario
document.getElementById("tipo_usuario").onblur = function() {
    validaTipoUsuario();
};

// Función para validar nombre completo
function validaNombre() {
    var nombre = document.getElementById("nombre_completo").value;
    var errorNombre = document.getElementById("error-nombre");

    if (nombre === "") {
        errorNombre.innerHTML = "Debes ingresar un nombre completo.";
        return false;
    } else {
        errorNombre.innerHTML = "";
        return true;
    }
}

// Función para validar contraseña
function validaContrasena() {
    var contrasena = document.getElementById("contrasena").value;
    var errorContrasena = document.getElementById("error-contrasena");

    if (contrasena === "") {
        errorContrasena.innerHTML = "Debes ingresar una contraseña.";
        return false;
    } else {
        errorContrasena.innerHTML = "";
        return true;
    }
}

// Función para validar la confirmación de la contraseña
function validaConfirmarContrasena() {
    var contrasena = document.getElementById("contrasena").value;
    var confirmarContrasena = document.getElementById("repetir_contrasena").value;
    var errorConfirmarContrasena = document.getElementById("error-confirmar");

    if (confirmarContrasena !== "" && confirmarContrasena !== contrasena) {
        errorConfirmarContrasena.innerHTML = "Las contraseñas no coinciden.";
        return false;
    } else if (confirmarContrasena === "") {
        errorConfirmarContrasena.innerHTML = "Debes confirmar la contraseña.";
        return false;
    } else {
        errorConfirmarContrasena.innerHTML = "";
        return true;
    }
}

// Función para validar tipo de usuario
function validaTipoUsuario() {
    var tipoUsuario = document.getElementById("tipo_usuario").value;
    var errorTipoUsuario = document.getElementById("error-tipo-usuario");

    if (tipoUsuario === "") {
        errorTipoUsuario.innerHTML = "Debes seleccionar un tipo de usuario.";
        return false;
    } else {
        errorTipoUsuario.innerHTML = "";
        return true;
    }
}
