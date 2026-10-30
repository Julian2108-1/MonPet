/* =========================================================
   MONPET | FORMULARIO DE ADOPCION POR ETAPAS
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("form-crear");
    if (!formulario) return;

    const contenedoresEtapa = Array.from(
        formulario.querySelectorAll(".contenido-etapa-formulario[data-etapa]")
    );

    const indicadores = Array.from(
        document.querySelectorAll(".indicador-etapas-adopcion .etapa-formulario")
    );

    const botonCancelar = document.getElementById("btn-cancelar");
    const enlaceAnterior = document.getElementById("btn-anterior");
    const botonContinuar = document.getElementById("btn-continuar");
    const campoMotivacion = document.getElementById("motivacion-adopcion");
    const campoBarrio = document.getElementById("barrio");
    const listaBarrios = document.getElementById("lista-barrios");
    const estadoConfirmacion = document.getElementById("estado-confirmacion");

    // Carga de documentos (opcional, por ahora solo en el navegador).
    const zonaDocumentos = document.getElementById("zona-documentos");
    const inputDocumentos = document.getElementById("documentos-adopcion");
    const botonSeleccionarDocumentos = document.getElementById("btn-seleccionar-documentos");
    const listaDocumentos = document.getElementById("lista-documentos");

    const MAXIMO_ARCHIVOS = 3;
    const MAXIMO_PESO = 5 * 1024 * 1024; // 5 MB por archivo.
    const EXTENSIONES_PERMITIDAS = new Set(["pdf", "jpg", "jpeg", "png"]);
    let archivosSeleccionados = [];

    if (!botonCancelar || !botonContinuar || contenedoresEtapa.length === 0) {
        console.error("MonPet: faltan elementos necesarios del formulario por etapas.");
        return;
    }

    let etapaActual = 1;
    let terminosAceptados = false;

    /* -----------------------------------------------------
       Mostrar una etapa y actualizar el indicador.
       Solo se muestra un enlace discreto para volver y un
       unico boton principal para avanzar.
       ----------------------------------------------------- */
    function mostrarEtapa(numero) {
        etapaActual = numero;

        contenedoresEtapa.forEach((contenedor) => {
            const corresponde = Number(contenedor.dataset.etapa) === numero;
            contenedor.hidden = !corresponde;
            contenedor.setAttribute("aria-hidden", String(!corresponde));
        });

        indicadores.forEach((indicador, indice) => {
            const numeroEtapa = indice + 1;
            const activa = numeroEtapa === numero;
            const completada = numeroEtapa < numero;

            indicador.classList.toggle("etapa-activa", activa);
            indicador.classList.toggle("etapa-completada", completada);

            if (activa) {
                indicador.setAttribute("aria-current", "step");
            } else {
                indicador.removeAttribute("aria-current");
            }
        });

        // El enlace Anterior aparece desde la segunda etapa.
        if (enlaceAnterior) {
            enlaceAnterior.hidden = numero === 1;
            enlaceAnterior.setAttribute("aria-hidden", String(numero === 1));
        }

        // Hay un solo boton principal de navegacion.
        botonContinuar.textContent = numero === 4
            ? (terminosAceptados ? "Condiciones aceptadas" : "Aceptar términos y continuar")
            : "Siguiente";
        botonContinuar.disabled = numero === 4 && terminosAceptados;

        if (estadoConfirmacion) {
            estadoConfirmacion.hidden = !terminosAceptados;
        }

        const etapaVisible = formulario.querySelector(
            `.contenido-etapa-formulario[data-etapa="${numero}"]`
        );
        const primerCampo = etapaVisible?.querySelector(
            'input:not([disabled]), select:not([disabled]), textarea:not([disabled])'
        );

        if (primerCampo) {
            primerCampo.focus({ preventScroll: true });
        }
    }

    /* -----------------------------------------------------
       Encontrar el primer campo invalido de la etapa actual.
       ----------------------------------------------------- */
    function obtenerPrimerCampoInvalido(numero) {
        const contenedor = formulario.querySelector(
            `.contenido-etapa-formulario[data-etapa="${numero}"]`
        );

        if (!contenedor) return null;

        if (numero === 3 && campoMotivacion) {
            const cantidad = campoMotivacion.value.trim().length;
            campoMotivacion.setCustomValidity(
                cantidad > 0 && cantidad < 30
                    ? "Escribe al menos 30 caracteres para continuar."
                    : ""
            );
        }

        const campos = Array.from(
            contenedor.querySelectorAll("input, select, textarea")
        ).filter((campo) => !campo.disabled);

        return campos.find((campo) => !campo.checkValidity()) || null;
    }

    /* -----------------------------------------------------
       Se muestra al intentar avanzar con campos pendientes
       o con una motivacion menor a 30 caracteres.
       ----------------------------------------------------- */
    async function mostrarAlertaCamposIncompletos(campoInvalido) {
        const motivacionCorta = campoInvalido === campoMotivacion &&
            campoMotivacion.value.trim().length < 30;

        const mensaje = motivacionCorta
            ? "La motivación debe tener al menos 30 caracteres."
            : "Debes completar los campos obligatorios de esta etapa antes de continuar.";

        if (window.Swal) {
            await Swal.fire({
                title: "¡Te faltan campos!",
                html: `
                    <img
                        src="../img/perrito_enojado_animado_mejorado.svg"
                        alt="Mascota animada indicando que faltan datos"
                        style="display:block; width:min(190px, 65vw); height:auto; margin:0 auto 14px;"
                    >
                    <p class="texto-alerta">${mensaje}</p>
                `,
                confirmButtonText: "Revisar campos",
                confirmButtonColor: "#8054c7",
                width: 460,
                padding: "24px",
                customClass: {
                    title: "titulo-alerta-campos",
                    htmlContainer: "contenido-alerta",
                    confirmButton: "boton-alerta"
                }
            });
        } else {
            window.alert(mensaje);
        }

        if (campoInvalido) {
            campoInvalido.scrollIntoView({ behavior: "smooth", block: "center" });
            campoInvalido.focus({ preventScroll: true });
        }
    }

    /* -----------------------------------------------------
       CARGA DE DOCUMENTOS OPCIONALES
       - PDF, JPG, JPEG y PNG.
       - Hasta 3 archivos, de máximo 5 MB cada uno.
       - En esta etapa se muestran y administran localmente.
         Todavía no se envían ni se guardan en el servidor.
       ----------------------------------------------------- */
    function obtenerExtension(nombreArchivo) {
        const partes = nombreArchivo.toLowerCase().split(".");
        return partes.length > 1 ? partes.pop() : "";
    }

    function formatearPeso(bytes) {
        if (bytes < 1024 * 1024) {
            return `${(bytes / 1024).toFixed(0)} KB`;
        }
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    }

    function sincronizarInputDocumentos() {
        if (!inputDocumentos || typeof DataTransfer === "undefined") return;

        try {
            const transferencia = new DataTransfer();
            archivosSeleccionados.forEach((archivo) => transferencia.items.add(archivo));
            inputDocumentos.files = transferencia.files;
        } catch (error) {
            console.warn("MonPet: no fue posible sincronizar la lista de documentos.", error);
        }
    }

    function renderizarListaDocumentos() {
        if (!listaDocumentos) return;

        listaDocumentos.replaceChildren();

        if (archivosSeleccionados.length === 0) {
            const vacio = document.createElement("li");
            vacio.className = "documentos-sin-seleccionar";
            vacio.textContent = "Todavía no has seleccionado documentos.";
            listaDocumentos.appendChild(vacio);
            return;
        }

        archivosSeleccionados.forEach((archivo, indice) => {
            const elemento = document.createElement("li");
            elemento.className = "archivo-seleccionado";

            const informacion = document.createElement("div");
            informacion.className = "informacion-archivo";

            const icono = document.createElement("i");
            icono.className = "fa-regular fa-file-lines icono-archivo";
            icono.setAttribute("aria-hidden", "true");

            const texto = document.createElement("div");
            texto.className = "texto-archivo";

            const nombre = document.createElement("strong");
            nombre.textContent = archivo.name;
            nombre.title = archivo.name;

            const peso = document.createElement("span");
            peso.textContent = formatearPeso(archivo.size);

            texto.append(nombre, peso);
            informacion.append(icono, texto);

            const quitar = document.createElement("button");
            quitar.type = "button";
            quitar.className = "quitar-archivo";
            quitar.setAttribute("aria-label", `Quitar ${archivo.name}`);
            quitar.title = "Quitar archivo";
            quitar.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
            quitar.addEventListener("click", () => {
                archivosSeleccionados.splice(indice, 1);
                sincronizarInputDocumentos();
                renderizarListaDocumentos();
            });

            elemento.append(informacion, quitar);
            listaDocumentos.appendChild(elemento);
        });
    }

    async function mostrarAvisoDocumentos(mensajes) {
        if (!mensajes.length) return;

        const texto = mensajes.join("\n");
        if (window.Swal) {
            await Swal.fire({
                icon: "warning",
                title: "Algunos archivos no se agregaron",
                text: texto,
                confirmButtonText: "Entendido",
                confirmButtonColor: "#8054c7"
            });
        } else {
            window.alert(texto);
        }
    }

    async function agregarDocumentos(archivos) {
        const mensajes = [];

        Array.from(archivos).forEach((archivo) => {
            const extension = obtenerExtension(archivo.name);

            if (!EXTENSIONES_PERMITIDAS.has(extension)) {
                mensajes.push(`${archivo.name}: formato no permitido.`);
                return;
            }

            if (archivo.size > MAXIMO_PESO) {
                mensajes.push(`${archivo.name}: supera el máximo de 5 MB.`);
                return;
            }

            const duplicado = archivosSeleccionados.some((existente) =>
                existente.name === archivo.name &&
                existente.size === archivo.size &&
                existente.lastModified === archivo.lastModified
            );

            if (duplicado) return;

            if (archivosSeleccionados.length >= MAXIMO_ARCHIVOS) {
                mensajes.push(`Solo puedes adjuntar hasta ${MAXIMO_ARCHIVOS} archivos.`);
                return;
            }

            archivosSeleccionados.push(archivo);
        });

        sincronizarInputDocumentos();
        renderizarListaDocumentos();
        await mostrarAvisoDocumentos(mensajes);
    }

    if (inputDocumentos && botonSeleccionarDocumentos) {
        botonSeleccionarDocumentos.addEventListener("click", () => {
            inputDocumentos.click();
        });

        inputDocumentos.addEventListener("change", async () => {
            const seleccionados = Array.from(inputDocumentos.files || []);
            await agregarDocumentos(seleccionados);
        });
    }

    if (zonaDocumentos) {
        ["dragenter", "dragover"].forEach((tipoEvento) => {
            zonaDocumentos.addEventListener(tipoEvento, (evento) => {
                evento.preventDefault();
                evento.stopPropagation();
                zonaDocumentos.classList.add("zona-documentos-activa");
            });
        });

        ["dragleave", "dragend"].forEach((tipoEvento) => {
            zonaDocumentos.addEventListener(tipoEvento, (evento) => {
                evento.preventDefault();
                zonaDocumentos.classList.remove("zona-documentos-activa");
            });
        });

        zonaDocumentos.addEventListener("drop", async (evento) => {
            evento.preventDefault();
            evento.stopPropagation();
            zonaDocumentos.classList.remove("zona-documentos-activa");

            if (evento.dataTransfer?.files?.length) {
                await agregarDocumentos(evento.dataTransfer.files);
            }
        });
    }

    renderizarListaDocumentos();

    /* -----------------------------------------------------
       Construir el resumen final de manera segura con textContent.
       ----------------------------------------------------- */
    function obtenerValor(selector) {
        const campo = formulario.querySelector(selector);
        return campo ? campo.value.trim() : "";
    }

    function escribirResumen(id, valor) {
        const destino = document.getElementById(id);
        if (destino) destino.textContent = valor || "No diligenciado";
    }

    function actualizarResumen() {
        const opcionComuna = formulario.querySelector("#comunas-select option:checked");

        const comuna = opcionComuna && opcionComuna.value
            ? opcionComuna.textContent.trim()
            : "";

        escribirResumen("resumen-nombre", obtenerValor("#nombre-completo"));
        escribirResumen("resumen-correo", obtenerValor("#correo-electronico"));
        escribirResumen("resumen-telefono", obtenerValor("#telefono-contacto"));
        escribirResumen(
            "resumen-documento",
            `${obtenerValor("#numero-documento")}`.trim()
        );
        escribirResumen("resumen-comuna", comuna);
        escribirResumen("resumen-barrio", obtenerValor("#barrio"));
        escribirResumen("resumen-direccion", obtenerValor("#direccion-completa"));
        escribirResumen("resumen-motivacion", obtenerValor("#motivacion-adopcion"));
        escribirResumen(
            "resumen-documentos",
            archivosSeleccionados.length
                ? archivosSeleccionados.map((archivo) => archivo.name).join(", ")
                : "No adjuntaste documentos"
        );
    }

    /* -----------------------------------------------------
       Mostrar términos y política de privacidad.
       ----------------------------------------------------- */
    async function solicitarAceptacion() {
        if (!window.Swal) {
            window.alert("No se pudo cargar la ventana de confirmación. Verifica la conexión de SweetAlert2.");
            return;
        }

        const resultado = await Swal.fire({
            width: 680,
            padding: "28px",
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
                <p>Antes de continuar, lee y acepta las siguientes condiciones.</p>

                <label class="opcion-confirmacion-monpet">
                    <input type="checkbox" id="aceptar-terminos">
                    <span class="informacion-confirmacion">
                        <strong>Acepto los términos y condiciones.</strong><br>
                        Declaro que he leído y acepto las condiciones aplicables al proceso de adopción.<br>
                        <a href="../documentos/terminos.html" target="_blank" rel="noopener noreferrer">Leer términos completos</a>
                    </span>
                </label>

                <label class="opcion-confirmacion-monpet">
                    <input type="checkbox" id="aceptar-privacidad">
                    <span class="informacion-confirmacion">
                        <strong>Acepto la política de privacidad.</strong><br>
                        Autorizo el tratamiento de mis datos personales para gestionar mi solicitud de adopción.<br>
                        <a href="../documentos/privacidad.html" target="_blank" rel="noopener noreferrer">Leer política completa</a>
                    </span>
                </label>

                <p class="nota-legal-confirmacion">
                    La aceptación de estas condiciones no significa que la adopción haya sido aprobada.
                </p>
            `,
            showCancelButton: true,
            confirmButtonText: "Aceptar y continuar",
            cancelButtonText: "Volver a revisar",
            confirmButtonColor: "#8054c7",
            cancelButtonColor: "#8a8493",
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                const terminos = document.getElementById("aceptar-terminos");
                const privacidad = document.getElementById("aceptar-privacidad");

                if (!terminos?.checked || !privacidad?.checked) {
                    Swal.showValidationMessage(
                        "Debes marcar las dos casillas para continuar."
                    );
                    return false;
                }

                return true;
            }
        });

        if (!resultado.isConfirmed) return;

        terminosAceptados = true;
        botonContinuar.textContent = "Condiciones aceptadas";
        botonContinuar.disabled = true;

        if (estadoConfirmacion) {
            estadoConfirmacion.textContent =
                "Has aceptado los términos y la política de privacidad. La información está revisada y lista para conectarse al registro de la solicitud.";
            estadoConfirmacion.hidden = false;
        }

        await Swal.fire({
            icon: "success",
            title: "¡Confirmación completada!",
            text: "Aceptaste los términos y la política de privacidad. El siguiente paso será conectar el envío con el registro de la solicitud.",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#8054c7"
        });
    }

    /* -----------------------------------------------------
       Unico boton principal: avanzar etapa por etapa.
       En etapa 4 abre la aceptación de condiciones.
       ----------------------------------------------------- */
    botonContinuar.addEventListener("click", async () => {
        if (etapaActual === 4) {
            if (!terminosAceptados) await solicitarAceptacion();
            return;
        }

        const campoInvalido = obtenerPrimerCampoInvalido(etapaActual);
        if (campoInvalido) {
            await mostrarAlertaCamposIncompletos(campoInvalido);
            return;
        }

        const siguiente = etapaActual + 1;
        if (siguiente === 4) actualizarResumen();
        mostrarEtapa(siguiente);
    });

    /* -----------------------------------------------------
       Enlace de texto Anterior (no es un segundo botón).
       Aparece desde la etapa 2.
       ----------------------------------------------------- */
    if (enlaceAnterior) {
        enlaceAnterior.addEventListener("click", (evento) => {
            evento.preventDefault();
            if (etapaActual > 1) {
                mostrarEtapa(etapaActual - 1);
            }
        });
    }

    /* -----------------------------------------------------
       Cancelar y limpiar el formulario.
       ----------------------------------------------------- */
    botonCancelar.addEventListener("click", async () => {
        let confirmarCancelacion = false;

        if (window.Swal) {
            const respuesta = await Swal.fire({
                icon: "warning",
                title: "¿Cancelar la solicitud?",
                text: "Se borrarán los datos que hayas diligenciado en este formulario.",
                showCancelButton: true,
                confirmButtonText: "Sí, cancelar",
                cancelButtonText: "Seguir diligenciando",
                confirmButtonColor: "#8054c7",
                cancelButtonColor: "#8a8493",
                reverseButtons: true
            });
            confirmarCancelacion = respuesta.isConfirmed;
        } else {
            confirmarCancelacion = window.confirm(
                "¿Deseas borrar los datos del formulario?"
            );
        }

        if (!confirmarCancelacion) return;

        formulario.reset();
        terminosAceptados = false;
        archivosSeleccionados = [];
        if (inputDocumentos) inputDocumentos.value = "";
        sincronizarInputDocumentos();
        renderizarListaDocumentos();

        if (campoMotivacion) campoMotivacion.setCustomValidity("");
        if (campoBarrio) {
            campoBarrio.value = "";
            campoBarrio.disabled = true;
            campoBarrio.placeholder = "Primero elige una comuna...";
        }
        if (listaBarrios) listaBarrios.innerHTML = "";
        if (estadoConfirmacion) {
            estadoConfirmacion.textContent = "";
            estadoConfirmacion.hidden = true;
        }

        botonContinuar.disabled = false;
        mostrarEtapa(1);
    });

    /* Si cambia cualquier dato después de aceptar, debe aceptar otra vez. */
    function invalidarAceptacion() {
        if (!terminosAceptados) return;

        terminosAceptados = false;
        botonContinuar.disabled = false;
        botonContinuar.textContent = etapaActual === 4
            ? "Aceptar términos y continuar"
            : "Siguiente";

        if (estadoConfirmacion) {
            estadoConfirmacion.textContent =
                "Has modificado tus datos. Revisa nuevamente y vuelve a aceptar las condiciones.";
            estadoConfirmacion.hidden = false;
        }
    }

    formulario.addEventListener("input", invalidarAceptacion);
    formulario.addEventListener("change", invalidarAceptacion);

    // En esta fase el formulario todavía no se envía al servidor.
    formulario.addEventListener("submit", (evento) => {
        evento.preventDefault();
    });

    mostrarEtapa(1);
});
