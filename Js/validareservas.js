
document.getElementById("nombre_reserva").onblur = function() {
    validaNombre();
};
// Función para validar nombre completo
function validaNombre() {
    var nombre = document.getElementById("nombre_reserva").value;
    var errorNombre = document.getElementById("error-nombre");

    if (nombre === "") {
        errorNombre.innerHTML = "Debes ingresar un nombre completo.";
        return false;
    } else {
        errorNombre.innerHTML = "";
        return true;
    }
}
