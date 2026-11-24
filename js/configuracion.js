document.addEventListener('DOMContentLoaded', () => {
    const acciones = [
        ['btn-editar-perfil', 'form-editar-perfil'],
        ['btn-cambiar-contrasena', 'form-cambiar-contrasena'],
    ];
    const formulariosPost = document.querySelectorAll('main form[method="post"]');

    acciones.forEach(([botonId, formularioId]) => {
        const boton = document.getElementById(botonId);
        const formulario = document.getElementById(formularioId);
        if (!boton || !formulario) return;

        boton.setAttribute('aria-expanded', String(!formulario.hidden));
        boton.addEventListener('click', () => {
            formulario.hidden = !formulario.hidden;
            boton.setAttribute('aria-expanded', String(!formulario.hidden));
            if (!formulario.hidden) {
                formulario.querySelector('input:not([type="hidden"])')?.focus();
            }
        });

        formulario.querySelectorAll('[data-cerrar-form]').forEach((cerrar) => {
            cerrar.addEventListener('click', () => {
                formulario.reset();
                formulario.hidden = true;
                boton.setAttribute('aria-expanded', 'false');
                boton.focus();
            });
        });
    });

    const formularioContrasena = document.getElementById('form-cambiar-contrasena');
    const contrasenaNueva = formularioContrasena?.elements.namedItem('contrasena_nueva');
    const confirmarContrasena = formularioContrasena?.elements.namedItem('confirmar_contrasena_nueva');

    if (contrasenaNueva && confirmarContrasena) {
        const validarConfirmacion = () => {
            const noCoinciden = confirmarContrasena.value !== ''
                && contrasenaNueva.value !== confirmarContrasena.value;

            confirmarContrasena.setCustomValidity(
                noCoinciden ? 'La contraseña nueva y su confirmación no coinciden.' : '',
            );
        };

        contrasenaNueva.addEventListener('input', validarConfirmacion);
        confirmarContrasena.addEventListener('input', validarConfirmacion);
        formularioContrasena.addEventListener('reset', () => {
            queueMicrotask(validarConfirmacion);
        });
    }

    formulariosPost.forEach((formulario) => {
        formulario.addEventListener('submit', (evento) => {
            if (formulario.dataset.enviando === 'true') {
                evento.preventDefault();
                return;
            }

            if (!formulario.reportValidity()) {
                evento.preventDefault();
                return;
            }

            formulario.dataset.enviando = 'true';
            formulario.setAttribute('aria-busy', 'true');
        });
    });

    window.addEventListener('pageshow', () => {
        formulariosPost.forEach((formulario) => {
            delete formulario.dataset.enviando;
            formulario.removeAttribute('aria-busy');
        });
    });
});
