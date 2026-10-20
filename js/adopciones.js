document.addEventListener("DOMContentLoaded", function () {

    // =========================================================
    // ELEMENTOS PRINCIPALES
    // =========================================================

    // Busca el contenedor donde están todas las tarjetas
    const galeria = document.querySelector(".galeria-mascotas");

    // Busca el contenedor del botón siguiente
    const botonSiguiente = galeria.querySelector(".boton-siguiente");


    // =========================================================
    // CARRUSEL
    // =========================================================

    // Función para mover la primera tarjeta al final
    function siguiente() {

        // Busca nuevamente todas las tarjetas que existen actualmente
        const tarjetasActuales =
            galeria.querySelectorAll(".ficha-mascota");

        // Obtiene la primera tarjeta actual
        const primeraTarjeta = tarjetasActuales[0];

        // Mueve la primera tarjeta justo antes del botón siguiente
        galeria.insertBefore(primeraTarjeta, botonSiguiente);

    }


    // Función para mover la última tarjeta al principio
    function anterior() {

        // Busca nuevamente todas las tarjetas que existen actualmente
        const tarjetasActuales =
            galeria.querySelectorAll(".ficha-mascota");

        // Obtiene la última tarjeta actual
        const ultimaTarjeta =
            tarjetasActuales[tarjetasActuales.length - 1];

        // Obtiene la primera tarjeta actual
        const primeraTarjeta =
            tarjetasActuales[0];

        // Coloca la última tarjeta antes de la primera
        galeria.insertBefore(ultimaTarjeta, primeraTarjeta);

    }


    // Hace que estas funciones funcionen
    // con los onclick que ya tienes en tu HTML
    window.anterior = anterior;
    window.siguiente = siguiente;


    // =========================================================
    // CLIC EN CADA TARJETA
    // =========================================================

    // Busca todas las tarjetas una sola vez
    const tarjetas =
        galeria.querySelectorAll(".ficha-mascota");


    tarjetas.forEach(function (tarjeta) {

        // Detecta el clic sobre la tarjeta
        tarjeta.addEventListener("click", function () {

            // Busca la imagen de la tarjeta
            const imagen =
                tarjeta.querySelector(".foto-mascota img");

            // Obtiene la ruta de la imagen
            const rutaImagen =
                imagen.getAttribute("src");

            // Obtiene el nombre del archivo
            const nombreArchivo =
                rutaImagen.split("/").pop();

            // Quita la extensión
            const nombreMascota =
                nombreArchivo.split(".")[0];

            // Envía a la página de información
            window.location.href =
                "informacion_mascota.php?mascota=" +
                nombreMascota;

        });


        // =====================================================
        // CORAZÓN
        // =====================================================

        // Busca el botón favorito
        const botonFavorito =
            tarjeta.querySelector(".favorito");

        // Busca el icono del corazón
        const iconoCorazon =
            botonFavorito.querySelector("i");


        // Busca la imagen de la mascota
        const imagen =
            tarjeta.querySelector(".foto-mascota img");

        // Obtiene el nombre de la mascota
        const nombreArchivo =
            imagen.getAttribute("src")
                .split("/")
                .pop()
                .split(".")[0];


        // Crea una clave única para cada mascota
        const claveFavorito =
            "monpet-favorito-" + nombreArchivo;


        // =====================================================
        // COMPROBAR SI YA ES FAVORITO
        // =====================================================

        // Comprueba si estaba guardado anteriormente
        const favoritoGuardado =
            localStorage.getItem(claveFavorito);


        // Si estaba guardado
        if (favoritoGuardado === "true") {

            // Quita el corazón vacío
            iconoCorazon.classList.remove("fa-regular");

            // Agrega el corazón lleno
            iconoCorazon.classList.add("fa-solid");

        }


        // =====================================================
        // CLIC EN EL CORAZÓN
        // =====================================================

        botonFavorito.addEventListener(
            "click",
            function (evento) {

                // Evita que el clic abra la información
                evento.stopPropagation();


                // Comprueba si ya está marcado
                const estaMarcado =
                    iconoCorazon.classList.contains("fa-solid");


                // =================================================
                // DESMARCAR FAVORITO
                // =================================================

                if (estaMarcado) {

                    // Quita el corazón lleno
                    iconoCorazon.classList.remove("fa-solid");

                    // Coloca el corazón vacío
                    iconoCorazon.classList.add("fa-regular");

                    // Elimina el favorito guardado
                    localStorage.removeItem(claveFavorito);

                }


                // =================================================
                // MARCAR FAVORITO
                // =================================================

                else {

                    // Quita el corazón vacío
                    iconoCorazon.classList.remove("fa-regular");

                    // Coloca el corazón lleno
                    iconoCorazon.classList.add("fa-solid");

                    // Guarda el favorito
                    localStorage.setItem(
                        claveFavorito,
                        "true"
                    );

                }

            }
        );

    });

});