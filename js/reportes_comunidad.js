
const botonesDetalles = document.querySelectorAll(".boton-detalles");

botonesDetalles.forEach(function (boton) {

    boton.addEventListener("click", function () {

        const idMascota = boton.dataset.reporte;

        window.location.href =
            "informacion_mascota.php?id=" + idMascota;

    });

});