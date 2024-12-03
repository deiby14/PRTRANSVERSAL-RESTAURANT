function showContent(sectionId) {
    document.querySelectorAll('.content').forEach(section => {
        section.style.display = 'none';
    });

    document.getElementById(sectionId).style.display = 'block';
    document.getElementById("ocultarImg").classList.add("ocultar");

    if (sectionId === "Comedores") {
        document.getElementById("volverComedor").style.display = "block";
    } else if (sectionId === "Privadas") {
        document.getElementById("volverPrivada").style.display = "block";
    } else if (sectionId === "Terrazas") {
        document.getElementById("volverTerraza").style.display = "block";
    }
}
function VolverSalas() {
    document.querySelectorAll('.content').forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById("ocultarImg").classList.remove("ocultar"); // Muestra las imágenes iniciales
    document.querySelectorAll('.volverBtn').forEach(button => {
        button.style.display = 'none'; // Oculta los botones "Volver"
    });
}
document.getElementById("Comedor").onclick = function (event) {
    event.preventDefault();
    showContent("Comedores");
};
document.getElementById("Privada").onclick = function (event) {
    event.preventDefault();
    showContent("Privadas");
};
document.getElementById("Terraza").onclick = function (event) {
    event.preventDefault();
    showContent("Terrazas");
};
document.getElementById("volverComedor").onclick = VolverSalas;
document.getElementById("volverPrivada").onclick = VolverSalas;
document.getElementById("volverTerraza").onclick = VolverSalas;