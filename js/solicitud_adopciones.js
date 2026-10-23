
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

// =====================================================
// CONFIRMACIÓN DE LA SOLICITUD DE ADOPCIÓN - MONPET
// =====================================================

const botonConfirmacion = document.getElementById("btn-confirmacion");
const formulario = document.getElementById("form-crear");

botonConfirmacion.addEventListener("click", async () => {

    // =====================================================
    // VALIDAR LA MOTIVACIÓN
    // =====================================================

    const motivacion = document.querySelector(
        ".grupo-motivacion-adopcion textarea"
    );


    if (!formulario.checkValidity()) {
        formulario.reportValidity();

        await Swal.fire({
            title: "¡Te Faltan Campos!",
            html: `<img src="../img/perrito_vector.svg"alt="Mascota enojada" style="width: 500px; height: 500px;"/>`,
            text: "Debes completar los campos antes de enviar",
            confirmButtonText:"Aceptar",
            width: "700px",
            confirmButtonColor:"#8054c7",
            customClass: {
                title: "titulo-alerta-campos",
                htmlContainer: "contenido-alerta",
                text: "texto-alerta",
                confirmButton: "boton-alerta"
            }

        })
        return;
    }

    if (!motivacion || motivacion.value.trim().length < 30) {
        await Swal.fire({
            icon: "warning",
            title: "Motivación incompleta",
            text: "Escribe al menos 30 caracteres para continuar.",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#8054c7"
        });

        motivacion?.focus();
        return;
    }

    // =====================================================
    // MOSTRAR TÉRMINOS Y POLÍTICA DE PRIVACIDAD
    // =====================================================

    const resultado = await Swal.fire({
        width: 680,
        padding: "32px",

        customClass: {
            popup: "ventana-confirmacion-monpet",
            title: "titulo-confirmacion-monpet",
            htmlContainer: "contenido-confirmacion-monpet",
            confirmButton: "boton-confirmar-monpet",
            cancelButton: "boton-cancelar-monpet"
        },

        title: "Confirma tu solicitud",
        icon: "question",
        iconColor: "#8054c7",

        html: `
            <p>
                Estás a un paso de continuar con la adopción de Luna.
                Lee y acepta las siguientes condiciones.
            </p>

            <label class="opcion-confirmacion-monpet">
                <input
                    type="checkbox"
                    id="aceptar-terminos"
                >

                <span class="informacion-confirmacion">
                    <strong>Acepto los términos y condiciones.</strong>
                    <br>

                    Declaro que he leído y acepto las condiciones
                    aplicables al proceso de adopción.

                    <br>

                    <a
                        href="../documentos/terminos.html"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Leer términos completos
                    </a>
                </span>
            </label>

            <label class="opcion-confirmacion-monpet">
                <input
                    type="checkbox"
                    id="aceptar-privacidad"
                >

                <span class="informacion-confirmacion">
                    <strong>Acepto la política de privacidad.</strong>
                    <br>

                    Autorizo el tratamiento de mis datos personales
                    para gestionar mi solicitud de adopción, según
                    la política informada.

                    <br>

                    <a
                        href="../documentos/privacidad.html"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Leer política completa
                    </a>
                </span>
            </label>

            <p style="
                font-size: 1.5rem;
                margin-top: 18px;
                color: #756b82;
            ">
                Tu información debe ser tratada conforme a la política
                de privacidad y la normativa aplicable.
            </p>
        `,

        showCancelButton: true,
        confirmButtonText: "Aceptar y continuar",
        cancelButtonText: "Volver a revisar",

        confirmButtonColor: "#8054c7",
        cancelButtonColor: "#8a8493",

        reverseButtons: true,
        focusConfirm: false,

        // Validar que ambas casillas estén marcadas
        preConfirm: () => {
            const terminos = document.getElementById(
                "aceptar-terminos"
            );

            const privacidad = document.getElementById(
                "aceptar-privacidad"
            );

            if (!terminos.checked || !privacidad.checked) {
                Swal.showValidationMessage(
                    "Debes marcar las dos casillas para continuar."
                );

                return false;
            }

            return {
                aceptaTerminos: true,
                aceptaPrivacidad: true
            };
        }
    });

    // =====================================================
    // SI CANCELA, NO CAMBIAR DE ETAPA
    // =====================================================

    if (!resultado.isConfirmed) {
        return;
    }

    // =====================================================
    // ACTIVAR VISUALMENTE LA ETAPA 4
    // =====================================================

    const etapas = document.querySelectorAll(
        ".indicador-etapas-adopcion .etapa-formulario"
    );

    etapas.forEach((etapa, indice) => {
        etapa.classList.toggle(
            "etapa-activa",
            indice === 3
        );

        etapa.classList.toggle(
            "etapa-completada",
            indice < 3
        );
    });

    // =====================================================
    // 5. MOSTRAR MENSAJE FINAL
    // =====================================================

    await Swal.fire({
        icon: "success",
        title: "¡Confirmación completada!",
        text: "Aceptaste los términos y la política de privacidad. Tu solicitud está lista para el siguiente paso.",
        confirmButtonText: "Entendido",
        confirmButtonColor: "#8054c7"
    });

});