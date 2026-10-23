
document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("form-crear");

    const etapas = document.querySelectorAll(
        ".indicador-etapas-adopcion .etapa-formulario"
    );

    const nombre = formulario.querySelector(
        ".campo-nombre-completo input"
    );

    const correo = formulario.querySelector(
        ".campo-correo-electronico input"
    );

    const telefono = formulario.querySelector(
        ".campo-telefono-contacto input"
    );

    const tipoDocumento = formulario.querySelector(
        ".campo-tipo-documento select"
    );

    const numeroDocumento = formulario.querySelector(
        "#numero-documento"
    );

    const ciudad = formulario.querySelector(
        ".campo-ciudad-residencia select"
    );

    const direccion = formulario.querySelector(
        ".campo-direccion-completa input"
    );

    const motivacion = formulario.querySelector(
        ".grupo-motivacion-adopcion textarea"
    );

    function campoCompleto(campo) {
        return campo &&
            campo.value.trim() !== "" &&
            campo.checkValidity();
    }

    function activarEtapa(numero) {
        etapas.forEach((etapa, indice) => {
            etapa.classList.toggle(
                "etapa-activa",
                indice === numero - 1
            );

            etapa.classList.toggle(
                "etapa-completada",
                indice < numero - 1
            );
        });
    }

    function actualizarEtapa() {
        const datosPersonalesCompletos =
            campoCompleto(nombre) &&
            campoCompleto(correo) &&
            campoCompleto(telefono) &&
            campoCompleto(tipoDocumento) &&
            campoCompleto(numeroDocumento);

        const direccionCompleta =
            campoCompleto(ciudad) &&
            campoCompleto(direccion);

        const experienciaCompleta =
            campoCompleto(motivacion) &&
            motivacion.value.trim().length >= 30;

        if (datosPersonalesCompletos &&
            direccionCompleta &&
            experienciaCompleta) {
            activarEtapa(4);
        } else if (datosPersonalesCompletos &&
                   direccionCompleta) {
            activarEtapa(3);
        } else if (datosPersonalesCompletos) {
            activarEtapa(2);
        } else {
            activarEtapa(1);
        }
    }

    formulario.querySelectorAll("input, select, textarea")
        .forEach(campo => {
            campo.addEventListener("input", actualizarEtapa);
            campo.addEventListener("change", actualizarEtapa);
        });

    actualizarEtapa();
});

