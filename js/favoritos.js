// =====================================================
// CORAZÓN MARCADO COMO FAVORITO O NO MARCADO
// =====================================================

// Busca todas las tarjetas de mascotas
const tarjetas = document.querySelectorAll(".tarjeta-mascota");

tarjetas.forEach(function (tarjeta) {

    // =====================================================
    // BUSCAR ELEMENTOS DE LA TARJETA
    // =====================================================

    // Busca el botón favorito dentro de la tarjeta
    const botonFavorito =
        tarjeta.querySelector(".boton-favorito");

    // Busca el icono del corazón dentro del botón
    const iconoCorazon =
        botonFavorito.querySelector("i");

    // Busca la imagen de la mascota
    const imagen =
        tarjeta.querySelector(".imagen-principal-mascota img");

    // Busca el nombre de la mascota
    const nombreMascota =
        tarjeta.querySelector(".datos-generales-mascota h3");


    // =====================================================
    // CREAR CLAVE ÚNICA PARA LA MASCOTA
    // =====================================================

    // Obtiene el nombre de la mascota
    const nombre =
        nombreMascota.textContent.trim();

    // Crea una clave única para guardar el favorito
    const claveFavorito =
        "monpet-favorito-" + nombre.toLowerCase();


    // =====================================================
    // COMPROBAR SI YA ESTABA MARCADA COMO FAVORITA
    // =====================================================

    const favoritoGuardado =
        localStorage.getItem(claveFavorito);


    // Si estaba guardada como favorita
    if (favoritoGuardado === "true") {

        // Quita el corazón vacío
        iconoCorazon.classList.remove("fa-regular");

        // Coloca el corazón lleno
        iconoCorazon.classList.add("fa-solid");
    }


    // =====================================================
    // CUANDO SE HACE CLIC EN EL CORAZÓN
    // =====================================================

    botonFavorito.addEventListener(
        "click",
        function (evento) {

            // Evita que el clic afecte otros elementos
            evento.stopPropagation();

            // Comprueba si actualmente está marcado
            const estaMarcado =
                iconoCorazon.classList.contains("fa-solid");


            // =================================================
            // QUITAR DE FAVORITOS
            // =================================================

            if (estaMarcado) {

                // Quita el corazón lleno
                iconoCorazon.classList.remove("fa-solid");

                // Coloca el corazón vacío
                iconoCorazon.classList.add("fa-regular");

                // Elimina el favorito de localStorage
                localStorage.removeItem(claveFavorito);
            }


            // =================================================
            // AGREGAR A FAVORITOS
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