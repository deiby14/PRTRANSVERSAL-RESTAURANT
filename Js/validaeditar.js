
document.getElementById("nombre_completo").onblur = function() {
    validaNombre();
};
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
