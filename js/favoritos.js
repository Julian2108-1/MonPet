
const botonesFavoritos = document.querySelectorAll(".boton-favorito");

botonesFavoritos.forEach((boton) => {
    boton.addEventListener("click", () => {
        const corazon = boton.querySelector("i");
        const agregado = corazon.classList.contains("fa-solid");

        if (agregado) {
            // Quitar de favoritos
            corazon.classList.remove("fa-solid");
            corazon.classList.add("fa-regular");

            boton.setAttribute("aria-pressed", "false");
            boton.setAttribute("aria-label", "Agregar a favoritos");

            alert("Mascota eliminada de favoritos 💔");
        } else {
            // Agregar a favoritos
            corazon.classList.remove("fa-regular");
            corazon.classList.add("fa-solid");

            boton.setAttribute("aria-pressed", "true");
            boton.setAttribute("aria-label", "Quitar de favoritos");

            alert("¡Luna se agregó a tus favoritos! ❤️");
        }
    });
});
