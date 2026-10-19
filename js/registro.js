document.addEventListener("DOMContentLoaded", () => {
  const formulario = document.getElementById("form-crear");

  const pasos = [
    document.getElementById("paso-1"),
    document.getElementById("paso-2"),
    document.getElementById("paso-3"),
  ];

  const botonSiguiente = document.getElementById("boton-siguiente");
  const botonAnterior = document.getElementById("boton-anterior");

  const indicadores = document.querySelectorAll(".pasos-registro > div");

  let pasoActual = 0;

  // ==========================
  // MOSTRAR PASO
  // ==========================

  function mostrarPaso(numero) {

    pasos.forEach((paso, indice) => {

        if (indice === numero) {
            paso.classList.add("activo");
        } else {
            paso.classList.remove("activo");
        }

    });

    indicadores.forEach((indicador, indice) => {

    indicador.classList.remove("paso-seleccionado");

    if (indice === numero) {
        indicador.classList.add("paso-seleccionado");
    }

});

    if (numero === 0) {
        botonAnterior.style.display = "none";
    } else {
        botonAnterior.style.display = "block";
    }

    if (numero === pasos.length - 1) {
        botonSiguiente.textContent = "Crear cuenta";
    } else {
        botonSiguiente.textContent = "Siguiente";
    }
}

  // ==========================
  // VALIDAR PASO
  // ==========================

  function validarPaso() {
    const campos = pasos[pasoActual].querySelectorAll("input, select");

    let valido = true;

    for (const campo of campos) {
      if (campo.type !== "hidden" && campo.value.trim() === "") {
        valido = false;

        campo.focus();

        Swal.fire({
          icon: "warning",
          title: "Campos incompletos",
          text: "Por favor completa todos los campos antes de continuar.",
          confirmButtonText: "Entendido",
          confirmButtonColor: "#7b2cbf",
        });

        break;
      }
    }

    // Validación específica del correo

    if (valido && pasoActual === 1) {
      const correo = document.getElementById("correo");

      const formatoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!formatoCorreo.test(correo.value)) {
        Swal.fire({
          icon: "error",
          title: "Correo no válido",
          text: "Ingresa una dirección de correo electrónico válida.",
          confirmButtonText: "Entendido",
          confirmButtonColor: "#7b2cbf",
        });

        correo.focus();

        return false;
      }
    }

    // Validación de contraseña

    if (valido && pasoActual === 2) {
      const contrasena = document.getElementById("contrasena");

      const confirmar = document.getElementById("confirmar_contrasena");

      if (contrasena.value.length < 8) {
        Swal.fire({
          icon: "warning",
          title: "Contraseña insegura",
          text: "La contraseña debe tener mínimo 8 caracteres.",
          confirmButtonText: "Entendido",
          confirmButtonColor: "#7b2cbf",
        });

        contrasena.focus();

        return false;
      }

      if (contrasena.value !== confirmar.value) {
        Swal.fire({
          icon: "error",
          title: "Las contraseñas no coinciden",
          text: "Verifica que ambas contraseñas sean iguales.",
          confirmButtonText: "Revisar",
          confirmButtonColor: "#7b2cbf",
        });

        confirmar.focus();

        return false;
      }
    }

    return valido;
  }

  // ==========================
  // SIGUIENTE
  // ==========================

  botonSiguiente.addEventListener("click", () => {
    if (!validarPaso()) {
      return;
    }

    // Si todavía no estamos en el último paso

    if (pasoActual < pasos.length - 1) {
      pasoActual++;

      mostrarPaso(pasoActual);

      Swal.fire({
        icon: "success",
        title: "¡Muy bien!",
        text: "Ahora completa la siguiente información.",
        timer: 1500,
        showConfirmButton: false,
      });
    }

    // Si estamos en el último paso
    else {
      Swal.fire({
        icon: "question",
        title: "¿Crear tu cuenta?",
        text: "Verifica que toda la información sea correcta.",
        showCancelButton: true,
        confirmButtonText: "Sí, crear cuenta",
        cancelButtonText: "Revisar",
        confirmButtonColor: "#7b2cbf",
        cancelButtonColor: "#6c757d",
      }).then((resultado) => {
        if (resultado.isConfirmed) {
          formulario.submit();
        }
      });
    }
  });

  // ==========================
  // ANTERIOR
  // ==========================

  botonAnterior.addEventListener("click", () => {
    if (pasoActual > 0) {
      pasoActual--;

      mostrarPaso(pasoActual);
    }
  });

  // ==========================
  // CANCELAR
  // ==========================

  const botonCancelar = document.querySelector(".boton-cancelar");

  botonCancelar.addEventListener("click", () => {
    Swal.fire({
      icon: "warning",
      title: "¿Cancelar registro?",
      text: "Se perderá la información que hayas ingresado.",
      showCancelButton: true,
      confirmButtonText: "Sí, cancelar",
      cancelButtonText: "Continuar registrándome",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#7b2cbf",
    }).then((resultado) => {
      if (resultado.isConfirmed) {
        formulario.reset();

        pasoActual = 0;

        mostrarPaso(pasoActual);
      }
    });
  });

  // Iniciar en el paso 1

  mostrarPaso(0);
});
