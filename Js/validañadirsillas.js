// Selección del campo de entrada y del contenedor de errores
const totalSillasInput = document.getElementById("cantidad");
const errorSillas = document.getElementById("error-cantidad");

// Asignar el evento onblur al campo de entrada
totalSillasInput.onblur = function () {
    validaSillas();
};

function validaSillas() {
    const totalSillas = parseInt(totalSillasInput.value);

    if (isNaN(totalSillas) || totalSillas === "") {
        errorSillas.innerHTML = "El número de sillas no puede estar vacío.";
        return false;
    } else if (totalSillas < 0) {
        errorSillas.innerHTML = "El número de sillas no puede ser negativo.";
        return false;
    } else if (totalSillas > 6) {
        errorSillas.innerHTML = "El número de sillas no puede exceder 6 personas.";
        return false;
    } else {
        errorSillas.innerHTML = ""; // Limpiar el error si todo está bien
        return true;
    }
}
