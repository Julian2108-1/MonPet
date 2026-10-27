<?php
require_once("../db/conexion.php");
$tipos = mysqli_query(
    $conexion,
    "SELECT * FROM tipo_documento"
);

?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/estilo_solicitud.css">
    <title>Solicitud de Adopción</title>
</head>

<body>
<header>
      <div class="contenedor-logo">
        <a href="../index.php">
        <div class="icono-logo">
          <i class="fa-solid fa-paw"></i>
        </div>
        <div class="texto-logo">
         <h1><span class="texto-titulo-1">Mon</span><span class="texto-titulo-2">Pet</span></h1>
          <p class="slogan-banner">Amor que cambia vidas <i class="fa-solid fa-heart"></i></p>
          </a>
        </div>
      </div>
      <nav class="links-navegacion">
        <ul>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="../paginas/adopcion.php">Adopción</a></li>
            <li><a href="../paginas/reportes_comunidad.php">Reportes</a></li>
            <li><a href="../paginas/Refugios.php">Refugios</a></li>
            <li><a href="../paginas/Jornadas.php">Jornadas</a></li>
            <li><a href="../paginas/Nosotros.php">Nosotros</a></li>
        </ul>
      </nav>
      <div class="acciones-usuario">
            <button class="boton-mensajes">
                <i class="fa-regular fa-bell"></i>
                <span class="contador-alertas">2</span>
            </button>

            <div class="perfil-usuario">
                <img src="../img/perfil.jpg" alt="Imagen_perfil">
                <span>Laura</span>
                <button>
                <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>
        </div>
    </header>
    
    <main class="pagina-solicitud-adopcion">
        <div class="migas-pan-reportes">
                <a href="../index.php"><i class="fa-solid fa-house"></i>Inicio</a>
                <i class="fa-solid fa-chevron-right separador-miga-reportes"></i>
                <a href="../paginas/informacion_mascota.php">Informacion Mascota</a>
                <i class="fa-solid fa-chevron-right separador-miga-reportes"></i>
                <span>Solicitud De Mascota</span>
            </div>
        <section class="encabezado-solicitud">
            <div class="informacion-solicitud">
                <h2>Solicitud de adopción<i class="fa-regular fa-heart"></i></h2>
                <p>Estás a un paso de cambiar una vida. Completa el formulario para solicitar la adopción de Luna.</p>

            </div>

            <div class="mensaje-seguridad-proceso">
                <div class="icono-seguridad">
                <i class="fa-solid fa-shield"></i>
                </div>
                <div class="informacion-seguridad">
                <strong>Proceso 100% seguro</strong>
                <span>Tu información está protegida</span>
                </div>
            </div>
        </section>

        <section class="contenido-solicitud">

            <!-- INFORMACION DE LA MASCOTA -->
            <aside class="panel-informacion-mascota">
                <article class="tarjeta-mascota">
                    <div class="imagen-principal-mascota">
                        <img src="../img/buddy.jpg" alt="Luna">
                        <span class="estado-adopcion-disponible">Disponible</span>
                        <button class="boton-favorito">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>

                    <div class="datos-generales-mascota">
                        <h3>Luna</h3>
                        <ul class="lista-caracteristicas-mascota">
                            <li class="detalle-raza">
                                <i class="fa-solid fa-paw"></i>
                                <strong>Raza</strong>
                                <span>Criolla</span>
                            </li>

                            <li class="detalle-edad">
                                <i class="fa-regular fa-calendar"></i>
                                <strong>Edad</strong>
                                <span>2 años</span>
                            </li>

                            <li class="detalle-tamano">
                                <i class="fa-solid fa-ruler"></i>
                                <strong>Tamaño</strong>
                                <span>Mediana</span>
                            </li>

                            <li class="detalle-ubicacion">
                                <i class="fa-solid fa-location-dot"></i>
                                <strong>Ubicación</strong>
                                <span>Bogotá</span>
                            </li>

                            <li class="detalle-genero">
                                <i class="fa-solid fa-venus"></i>
                                <strong>Género</strong>
                                <span>Hembra</span>
                            </li>
                        </ul>
                    </div>
                    <a class="boton-ver-informacion-mascota">
                        Ver información completa <span><i class="fa-solid fa-chevron-right"></i></span>
                    </a>
                </article>

                <article class="beneficios-adopcion">
                    <h3><i class="fa-regular fa-comments"></i>¿Por qué adoptar?</h3>
                    <ul class="lista-beneficios-adopcion">
                        <li><i class="fa-regular fa-circle-check"></i>Salvas una vida</li>
                        <li><i class="fa-regular fa-circle-check"></i>Das una segunda oportunidad</li>
                        <li><i class="fa-regular fa-circle-check"></i>Ayudas a reducir el abandono</li>
                        <li><i class="fa-regular fa-circle-check"></i>Ganas un compañero fiel</li>
                    </ul>
                </article>

                <article class="ayuda-adopcion">
                    <div class="icono-ayuda">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div class="ayuda-contacto">
                    <h3>¿Necesitas ayuda?</h3>
                    <p>Escríbenos y resolveremos<br>todas tus dudas.</p>
                    <a href="#">Contactar ahora <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                </article>
            </aside>
            <!-- FORMULARIO -->
            <section class="panel-formulario-adopcion">

                <div class="indicador-etapas-adopcion">
                    <div class="etapa-formulario etapa-activa">
                        <span class="numero-etapa">1</span>
                        <p>Información personal</p>
                    </div>

                    <div class="etapa-formulario">
                        <span class="numero-etapa">2</span>
                        <p>Tu hogar</p>
                    </div>

                    <div class="etapa-formulario">
                        <span class="numero-etapa">3</span>
                        <p>Experiencia</p>
                    </div>

                    <div class="etapa-formulario">
                        <span class="numero-etapa">4</span>
                        <p>Confirmación</p>
                    </div>
                </div>

                <form class="formulario-solicitud-adopcion" id="form-crear">

                    <fieldset class="grupo-datos-personales">
                        <legend id="paso-1"><i class="fa-regular fa-user"></i>Información personal</legend>

                        <div class="fila-campos-personales">
                            <div class="campo-nombre-completo">
                                <label>Nombre completo <span>*</span></label>
                                <div class="contenedor-input">
                                    <input type="text" placeholder="Ingresa Tu nombre Completo" required>
                                    <i class="fa-solid fa-circle-user"></i>
                                </div>
                            </div>

                            <div class="campo-correo-electronico">
                                <label>Correo electrónico <span>*</span></label>
                                <div class="contenedor-input">
                                    <input type="email" placeholder="ejemplo@correo.com" required>
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                            </div>
                        </div>

                        <div class="fila-campos-personales">
                            <div class="campo-telefono-contacto">
                                <label>Teléfono <span>*</span></label>
                                <div class="contenedor-input">
                                    <input type="tel" placeholder="300 123 4567" required>
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                            </div>

                            <div class="campo-tipo-documento">
                                <label>Tipo de documento <span>*</span></label>
                                <div class="contenedor-input">
                                    <select name="tipo_documento" id="tipo_documento" required>

                                        <option value="">Seleccione...</option>

                                        <?php while ($fila = mysqli_fetch_assoc($tipos)) { ?>

                                        <option value="<?php echo $fila["tipo_documento_id"]; ?>">
                                            <?php echo $fila["nombre"]; ?>
                                        </option>

                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="campo-numero-documento">
                            <label>Número de documento <span>*</span></label>
                            <div class="contenedor-input">
                                <input type="text" id="numero-documento" placeholder="Ingresa tu número de documento" required>
                                <i class="fa-regular fa-address-card"></i>
                            </div>
                        </div>
                    </fieldset>
                    <!-- DIRECCION -->
                    <fieldset class="grupo-direccion-residencia">
                        <legend><i class="fa-solid fa-house"></i>Dirección actual</legend>

                        <div class="fila-direccion">
                            <div class="campo-ciudad-residencia">
                                <label>Comuna <span>*</span></label>
                                <div class="contenedor-input">
                                    <i class="fa-solid fa-street-view"></i>
                                    <select id="comunas-select" name="comuna" style="width: 900px;" required>
                                        <option value="">Busca una comuna...</option>
                                        <option value="1">Comuna 1 - Compartir</option>
                                        <option value="2">Comuna 2 - Soacha Central</option>
                                        <option value="3">Comuna 3 - La Despensa</option>
                                        <option value="4">Comuna 4 - Cazucá</option>
                                        <option value="5">Comuna 5 - San Mateo</option>
                                        <option value="6">Comuna 6 - San Humberto</option>
                                    </select>
                                </div>
                            </div>

                            <div class="campo-localidad-residencia">
                                <label>Sector</label>
                                <div class="contenedor-input">
                                    <input list="lista-barrios" id="barrio" placeholder="Ingresa tu Sector o Barrio" required disabled>
                                    <datalist id="lista-barrios">

                                    </datalist>
                                    <i class="fa-solid fa-location-arrow"></i>
                                </div>
                            </div>

                        </div>
                        <div class="campo-direccion-completa">
                            <label>Dirección exacta <span>*</span></label>
                            <div class="contenedor-input">
                                <input type="text" placeholder="Calle, carrera, número, apartamento, etc." required>
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                        </div>
                    </fieldset>
                    <!-- MOTIVACION -->
                    <fieldset class="grupo-motivacion-adopcion">
                        <legend> <i class="fa-regular fa-star"></i>Cuéntanos por qué quieres adoptar a Luna
                            <span>*</span>
                        </legend>
                        <textarea placeholder="Escribe aquí tus razones..." required></textarea>
                        <p>Minimo 30 caracteres</p>
                    </fieldset>
                    <!-- DOCUMENTOS -->
                    <fieldset class="grupo-documentos-adicionales">
                        <legend><i class="fa-solid fa-paperclip"></i>Documentos (Opcional)</legend>
                        <p>Puedes adjuntar documentos que consideres importantes.</p>
                        <div class="zona-arrastrar-documentos">
                            <p>Arrastra y suelta archivos aquí</p>
                            <button type="button">Seleccionar archivos</button>
                        </div>
                    </fieldset>
                    <div class="acciones-formulario">
                        <button type="reset" class="boton-cancelar-solicitud">Cancelar</button>
                        <button type="button" class="boton-continuar-siguiente-paso" id="btn-confirmacion">Siguiente</button>
                    </div>
                </form>
                <div class="mensaje-confidencialidad-datos">
                    <i class="fa-solid fa-lock icono-1"></i>
                    <p>
                        Toda la información brindada será tratada con absoluta
                        confidencialidad y <br> utilizada únicamente para el proceso de adopción.
                    </p>
                    <i class="fa-solid fa-shield icono-2"></i>
                    <i class="fa-solid fa-check icono-3"></i>
                </div>
            </section>
        </section>
    </main>
<footer class="footer">

    <div class="footer-onda">
        <img src="../img/fondo_onda_2.png" alt="">
    </div>

    <div class="footer-contenedor">

        <div class="footer-logo">
        <div class="icono-logo2">
          <i class="fa-solid fa-paw"></i>
        </div>
        <div class="footer-informacion">
            <h2>MonPet</h2>
            <p>
                Conectamos corazones para que cada <br>mascota
                encuentre un hogar lleno<br>de amor y cuidado.
            </p>

            <div class="footer-redes">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
            </div>
            </div>
        </div>

        <div class="footer-enlaces">
            <h3>Enlaces rápidos</h3>

            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="../paginas/adopcion.php">Adopción</a></li>
                <li><a href="../paginas/reportes_comunidad.php">Reportes</a></li>
                <li><a href="../paginas/Refugios.php">Refugios</a></li>
                <li><a href="../paginas/Nosotros.php">Nosotros</a></li>
            </ul>
        </div>

        <div class="footer-ayuda">
            <h3>Ayuda</h3>

            <ul>
                <li><a href="../paginas/preguntas_frecuentes.php">Preguntas frecuentes</a></li>
                <li><a href="../paginas/como_adoptar.php">Cómo adoptar</a></li>
                <li><a href="../paginas/consejos_cuidado.php">Consejos de cuidado</a></li>
                <li><a href="../paginas/contáctanos.php">Contáctanos</a></li>
            </ul>
        </div>

        <div class="footer-legal">
            <h3>Información legal</h3>

            <ul>
                <li><a href="#">Términos y condiciones</a></li>
                <li><a href="#">Política de privacidad</a></li>
                <li><a href="#">Aviso legal</a></li>
            </ul>
        </div>

        <div class="footer-contacto">
            <h3>Contacto</h3>

            <ul>
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    Bogotá, Colombia
                </li>

                <li>
                    <i class="fa-solid fa-envelope"></i>
                    contacto@monpet.com
                </li>

                <li>
                    <i class="fa-solid fa-phone"></i>
                    +57 300 123 4567
                </li>

                <li>
                    <i class="fa-regular fa-clock"></i>
                    Lun - Vie: 8:00 a.m. - 6:00 p.m.
                </li>
            </ul>
        </div>

    </div>

    <div class="footer-inferior">
        <p>© 2026 MonPet. Todos los derechos reservados.</p>
    </div>

</footer>
    <script src="https://jquery.com"></script>
    <script src="https://jsdelivr.net"></script>
    <script src="../js/solicitud_adopciones.js"></script>
    <script src="../js/solicitud_adopcion.js"></script>
    <script src="../js/favoritos.js"></script>
</body>
</html>