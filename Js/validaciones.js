document.addEventListener("DOMContentLoaded", function() {
    // Asignar eventos a los elementos después de que el DOM esté cargado
    document.getElementById("nombre").addEventListener("blur", validaNombre)
    document.getElementById("contraseña").addEventListener("blur", validaContrasena)
  
});


function validaNombre() {
    var nombre = document.getElementById("nombre").value;
    var errorNombre = document.getElementById("error-nombre");
    const contieneCaracteres = /[^a-zA-Z0-9\s]/; 

    if (nombre === "") {
        errorNombre.innerHTML = "Debes ingresar un nombre";
        return false;
    } else if (contieneCaracteres.test(nombre)) {
        errorNombre.innerHTML = "No puede tener caracteres especiales";
        return false;
    } else {
        errorNombre.innerHTML = "";
        return true;
    }
}


function validaContrasena() {
    var contrasena = document.getElementById("contraseña").value;
    var errorContrasena = document.getElementById("error-contraseña");
 

    if (contrasena === "") {
        errorContrasena.innerHTML = "Debes ingresar una contraseña";
        return false;
    } else {
        errorContrasena.innerHTML = "";
        return true;
    }



}
