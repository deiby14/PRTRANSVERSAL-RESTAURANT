
document.getElementById("capacidad").onblur = function() {
    validaNombre();
};



function validaNombre() {
    var nombre = document.getElementById("capacidad").value;
    var errorNombre = document.getElementById("error-capacidad");

    if (nombre === "") {
        errorNombre.innerHTML = "Debes ingresar el campo .";
        return false;
    } else {
        errorNombre.innerHTML = "";
        return true;
    }
}