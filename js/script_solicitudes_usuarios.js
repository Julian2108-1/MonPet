// ==========================================================================
// LÓGICA DE INTERACTIVIDAD CON JAVASCRIPT VANILLA
// ==========================================================================

// 1. Esperamos a que todo el contenido del DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {

    // Seleccionamos todos los botones de las pestañas de filtro
    const filterButtons = document.querySelectorAll('.tab-btn');
    
    // Seleccionamos todas las tarjetas de solicitudes
    const requestCards = document.querySelectorAll('.request-card');

    // 2. Agregamos un evento de 'click' a cada botón de filtro
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {

            // Paso A: Quitar la clase 'active' de todos los botones
            filterButtons.forEach(btn => btn.classList.remove('active'));

            // Paso B: Marcar como 'active' el botón al que se le dio click
            button.classList.add('active');

            // Paso C: Obtener el valor del filtro seleccionado (ej: 'all', 'review', etc.)
            const selectedFilter = button.getAttribute('data-filter');

            // Paso D: Mostrar u ocultar las tarjetas según el filtro
            requestCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');

                if (selectedFilter === 'all' || selectedFilter === cardStatus) {
                    // Muestra la tarjeta si coincide con el filtro o si es 'Todas'
                    card.style.display = 'grid';
                } else {
                    // Oculta la tarjeta si no coincide
                    card.style.display = 'none';
                }
            });
        });
    });
});

// ==========================================================================
// FUNCIONES PARA EL MODAL DE DETALLES
// ==========================================================================

// Función para abrir el modal y actualizar la información de la mascota
function openModal(petName) {
    const modal = document.getElementById('details-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');

    // Asignamos información dinámica según el nombre
    modalTitle.textContent = `Detalles de la solicitud para ${petName}`;
    modalBody.textContent = `Aquí puedes consultar el seguimiento completo, documentos adjuntos y datos de contacto asignados para la adopción de ${petName}.`;

    // Hacemos visible el modal
    modal.style.display = 'block';
}

// Función para cerrar el modal al hacer clic en la "X"
function closeModal() {
    const modal = document.getElementById('details-modal');
    modal.style.display = 'none';
}

// Función para cerrar el modal si el usuario hace clic fuera de la caja blanca
window.onclick = function(event) {
    const modal = document.getElementById('details-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};