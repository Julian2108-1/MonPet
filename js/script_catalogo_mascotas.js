// Esperar a que el documento HTML esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // 1. INTERACCIÓN DE SELECCIÓN DE TIPO (PERROS / GATOS)
    // =========================================================
    const typeButtons = document.querySelectorAll('.type-btn');

    typeButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remover estado activo de todos los botones
            typeButtons.forEach(btn => btn.classList.remove('active'));
            // Agregar estado activo al botón presionado
            button.classList.add('active');
        });
    });

    // =========================================================
    // 2. BOTONES DE FAVORITO (CORAZÓN EN LAS TARJETAS)
    // =========================================================
    const favoriteButtons = document.querySelectorAll('.favorite-btn');

    favoriteButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const icon = btn.querySelector('i');
            
            // Alternar entre corazón relleno y corazón de línea
            if (icon.classList.contains('fa-regular')) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                btn.classList.add('active');
            } else {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                btn.classList.remove('active');
            }
        });
    });

    // =========================================================
    // 3. LIMPIAR FORMULARIO DE FILTROS
    // =========================================================
    const clearBtn = document.getElementById('clear-filters-btn');
    const filtersForm = document.getElementById('filters-form');

    if (clearBtn && filtersForm) {
        clearBtn.addEventListener('click', () => {
            filtersForm.reset();
            // Restaurar botones a estado inicial
            typeButtons.forEach(btn => btn.classList.remove('active'));
            if(typeButtons.length > 0) typeButtons[0].classList.add('active');
        });
    }

});