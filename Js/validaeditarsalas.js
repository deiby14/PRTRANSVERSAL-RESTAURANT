// validarAñadirSalas.js

document.getElementById('nombre').onblur = function() {
    const errorNombre = document.getElementById('error-nombre');
    if (this.value.trim() === '') {
        errorNombre.textContent = 'El campo no puede estar vacío.';
    } else if (this.value.length > 20) {
        errorNombre.textContent = 'El campo no puede superar los 20 caracteres.';
    } else {
        errorNombre.textContent = '';
    }
};

document.getElementById('capacidad').onblur = function() {
    const errorCapacidad = document.getElementById('error-capacidad');
    const capacidad = parseInt(this.value, 10);
    if (this.value.trim() === '' || isNaN(capacidad)) {
        errorCapacidad.textContent = 'La capacidad debe estar completado con un numero.';
    } else if (capacidad < 6 || capacidad > 30) {
        errorCapacidad.textContent = 'La capacidad debe estar entre 6 y 30.';
    } else {
        errorCapacidad.textContent = '';
    }
};

document.getElementById('imagen').onmouseover = function() {
    this.title = 'Debes de seleccionar un archivo';
};