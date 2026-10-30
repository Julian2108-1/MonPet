

document.addEventListener("DOMContentLoaded", function () {

    // 1. Barrios disponibles para cada comuna
    const barriosPorComuna = {
        "1": [
            "Compartir",
            "Quintanar",
            "Llanos de Soacha",
            "Tierra Grata",
            "Parque Campestre"
        ],
        "2": [
            "Soacha Centro",
            "Camino Real",
            "El Sol",
            "La Unión",
            "Praderas de Santa Ana"
        ],
        "3": [
            "Ciudad Verde",
            "La Despensa",
            "León XIII",
            "Los Olivos",
            "Potrero Grande"
        ],
        "4": [
            "Cazucá",
            "El Paraíso",
            "La Isla",
            "Julio Rincón",
            "Santo Domingo"
        ],
        "5": [
            "San Mateo",
            "Quintanares",
            "El Porvenir",
            "Club Tibanica",
            "Casalinda"
        ],
        "6": [
            "San Humberto",
            "Chicó",
            "La Amistad",
            "Copenhague",
            "El Triunfo"
        ]
    };

    // 2. Capturamos los elementos de tu HTML
    const selectComuna = document.getElementById("comunas-select");
    const inputBarrio = document.getElementById("barrio");
    const datalistBarrios = document.getElementById("lista-barrios");

    // 3. Estado inicial: primero se debe seleccionar una comuna
    inputBarrio.disabled = true;
    inputBarrio.placeholder = "Primero elige una comuna...";

    // 4. Cuando se seleccione una comuna
    selectComuna.addEventListener("change", function () {

        const comunaSeleccionada = this.value;

        // Limpiamos la selección anterior
        inputBarrio.value = "";
        datalistBarrios.innerHTML = "";

        // Si no se seleccionó ninguna comuna
        if (comunaSeleccionada === "") {
            inputBarrio.disabled = true;
            inputBarrio.placeholder = "Primero elige una comuna...";
            return;
        }

        // Habilitamos el campo de barrios
        inputBarrio.disabled = false;
        inputBarrio.placeholder = "Escribe para buscar tu barrio...";

        // Obtenemos los barrios de la comuna elegida
        const barrios = barriosPorComuna[comunaSeleccionada] || [];

        // Agregamos las opciones al datalist
        barrios.forEach(function (barrio) {
            const option = document.createElement("option");
            option.value = barrio;
            datalistBarrios.appendChild(option);
        });
    });

});
