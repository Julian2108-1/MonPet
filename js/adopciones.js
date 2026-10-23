document.addEventListener("DOMContentLoaded", function () {

    // Busca el contenedor donde están todas las tarjetas de las mascotas
    const galeria = document.querySelector(".galeria-mascotas");

    // Busca el contenedor del botón siguiente para hacer la funcion del carrusel
    const botonSiguiente = galeria.querySelector(".boton-siguiente");


    // =========================================================
    // CARRUSEL PARA MOVER LAS TARJETAS MEDIANTES BOTONES
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


    // Hace que estas funciones funcionen con los onclick que ya tienes en tu HTML
    window.anterior = anterior;
    window.siguiente = siguiente;


    // Busca todas las tarjetas una sola vez mediante la clase de la etiqueta
    const tarjetas =
        galeria.querySelectorAll(".ficha-mascota");


    tarjetas.forEach(function (tarjeta) {

        // Detecta el clic sobre la tarjeta para que nos lleve a ver la informacion de la mascota
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

            // Envía a la página de información y en la url muestra el nombre de la mascota
            window.location.href =
                "informacion_mascota.php?mascota=" +
                nombreMascota;

        });


        // =====================================================
        // CORAZÓN MARCADO COMO FAVORITO O NO MARCADO
        // =====================================================

        // Busca el botón favorito en html mediante la clase de la etiqueta
        const botonFavorito =
            tarjeta.querySelector(".favorito");

        // Busca el icono del corazón en el html mediante su etiqueta
        const iconoCorazon =
            botonFavorito.querySelector("i");


        // Busca la imagen de la mascota mediante su clase en la etiqueta
        const imagen =
            tarjeta.querySelector(".foto-mascota img");

        // Obtiene el nombre de la mascota sirve para guardar el nombre de la mascota para saber cual se marco como favorito
        const nombreArchivo =
            imagen.getAttribute("src")
                .split("/")
                .pop()
                .split(".")[0];


        // Crea una clave única para cada mascota saber que mascota es por el nombre
        const claveFavorito =
            "monpet-favorito-" + nombreArchivo;



        // Comprueba si estaba guardado anteriormente, cuando se actualiza esto verifica si esta marcado o no el corazon, y pone segun lo como estaba
        const favoritoGuardado =
            localStorage.getItem(claveFavorito);


        // Si estaba guardado, verifica si esta guardado y marcado el corazon
        if (favoritoGuardado === "true") {

            // Quita el corazón vacío, remueve la clase de la etiqueta i para cambiar el icono por otro
            iconoCorazon.classList.remove("fa-regular");

            // Agrega el corazón lleno esto cambia la clase en la etiqueta del icono para que se cambie el icono de corazon por un solido
            iconoCorazon.classList.add("fa-solid");

        }


        botonFavorito.addEventListener(
            "click",
            function (evento) {

                // Evita que el clic abra la información para que la funcion anterior no afecte
                evento.stopPropagation();


                // Comprueba si ya está marcado el icono de corazon
                const estaMarcado =
                    iconoCorazon.classList.contains("fa-solid");




                if (estaMarcado) {

                    // Quita el corazón lleno que marca el estado de fovorito
                    iconoCorazon.classList.remove("fa-solid");

                    // Coloca el corazón vacío que ya no esta marcado como favorito
                    iconoCorazon.classList.add("fa-regular");

                    // Elimina el favorito guardado y cuando se actualice la pagina ya no sale como favorito
                    localStorage.removeItem(claveFavorito);

                }

                else {

                    // Quita el corazón vacío que marca el estado de favorito
                    iconoCorazon.classList.remove("fa-regular");

                    // Coloca el corazón lleno que ya no esta marcado como favorito
                    iconoCorazon.classList.add("fa-solid");

                    // Guarda el favorito y cuando se actualice la pagina sigue saliendo como favorito
                    localStorage.setItem(
                        claveFavorito,
                        "true"
                    );

                }

            }
        );

    });

});