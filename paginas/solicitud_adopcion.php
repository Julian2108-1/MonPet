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

                <form class="formulario-solicitud-adopcion" id="form-crear" novalidate>

                    <!-- ETAPA 1: INFORMACION PERSONAL -->
                    <div class="contenido-etapa-formulario" data-etapa="1">
                        <fieldset class="grupo-datos-personales">
                            <legend id="paso-1"><i class="fa-regular fa-user"></i> Información personal</legend>

                            <div class="fila-campos-personales">
                                <div class="campo-nombre-completo">
                                    <label for="nombre-completo">Nombre completo <span>*</span></label>
                                    <div class="contenedor-input">
                                        <input type="text" id="nombre-completo" name="nombre_completo"
                                               placeholder="Ingresa tu nombre completo" autocomplete="name" required>
                                        <i class="fa-solid fa-circle-user" aria-hidden="true"></i>
                                    </div>
                                </div>

                                <div class="campo-correo-electronico">
                                    <label for="correo-electronico">Correo electrónico <span>*</span></label>
                                    <div class="contenedor-input">
                                        <input type="email" id="correo-electronico" name="correo_electronico"
                                               placeholder="ejemplo@correo.com" autocomplete="email" required>
                                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="fila-campos-personales">
                                <div class="campo-telefono-contacto">
                                    <label for="telefono-contacto">Teléfono <span>*</span></label>
                                    <div class="contenedor-input">
                                        <input type="tel" id="telefono-contacto" name="telefono"
                                               placeholder="300 123 4567" autocomplete="tel"
                                               inputmode="tel" required>
                                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                    </div>
                                </div>

                                <div class="campo-tipo-documento">
                                    <label for="tipo_documento">Tipo de documento <span>*</span></label>
                                    <div class="contenedor-input">
                                        <i class="fa-solid fa-id-card-clip"></i>
                                        <select name="tipo_documento" id="tipo_documento" required>
                                            <option value="">Seleccione...</option>
                                            <?php while ($fila = mysqli_fetch_assoc($tipos)) { ?>
                                                <option value="<?php echo htmlspecialchars($fila["tipo_documento_id"], ENT_QUOTES, "UTF-8"); ?>">
                                                    <?php echo htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="campo-numero-documento">
                                <label for="numero-documento">Número de documento <span>*</span></label>
                                <div class="contenedor-input">
                                    <input type="text" id="numero-documento" name="numero_documento"
                                           placeholder="Ingresa tu número de documento"
                                           autocomplete="off" inputmode="numeric" required>
                                    <i class="fa-regular fa-address-card" aria-hidden="true"></i>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- ETAPA 2: INFORMACION DEL HOGAR -->
                    <div class="contenido-etapa-formulario" data-etapa="2" hidden>
                        <fieldset class="grupo-direccion-residencia">
                            <legend><i class="fa-solid fa-house"></i> Dirección actual</legend>

                            <div class="fila-direccion">
                                <div class="campo-ciudad-residencia">
                                    <label for="comunas-select">Comuna <span>*</span></label>
                                    <div class="contenedor-input">
                                        <i class="fa-solid fa-street-view" aria-hidden="true"></i>
                                        <select id="comunas-select" name="comuna" required>
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
                                    <label for="barrio">Sector o barrio <span>*</span></label>
                                    <div class="contenedor-input">
                                        <input list="lista-barrios" id="barrio" name="barrio"
                                               placeholder="Primero elige una comuna..." required disabled>
                                        <datalist id="lista-barrios"></datalist>
                                        <i class="fa-solid fa-location-arrow" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="campo-direccion-completa">
                                <label for="direccion-completa">Dirección exacta <span>*</span></label>
                                <div class="contenedor-input">
                                    <input type="text" id="direccion-completa" name="direccion"
                                           placeholder="Calle, carrera, número, apartamento, etc."
                                           autocomplete="street-address" required>
                                    <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- ETAPA 3: EXPERIENCIA Y DOCUMENTOS -->
                    <div class="contenido-etapa-formulario" data-etapa="3" hidden>
                        <fieldset class="grupo-motivacion-adopcion">
                            <legend><i class="fa-regular fa-star"></i> Cuéntanos por qué quieres adoptar a Luna <span>*</span></legend>
                            <textarea id="motivacion-adopcion" name="motivacion"
                                      placeholder="Escribe aquí tus razones..."
                                      minlength="30" maxlength="1000" required></textarea>
                            <p>Mínimo 30 caracteres.</p>
                        </fieldset>

                        <fieldset class="grupo-documentos-adicionales">
                            <legend><i class="fa-solid fa-paperclip"></i> Documentos (opcional)</legend>
                            <p>Puedes adjuntar documentos que consideres importantes.</p>
                            <div class="zona-arrastrar-documentos" id="zona-documentos" aria-label="Zona para adjuntar documentos">
                                <i class="fa-solid fa-cloud-arrow-up icono-carga-documentos" aria-hidden="true"></i>
                                <p>Arrastra y suelta tus archivos aquí</p>
                                <span class="separador-documentos">o</span>
                                <button type="button" id="btn-seleccionar-documentos">Seleccionar archivos</button>
                                <input
                                    type="file"
                                    id="documentos-adopcion"
                                    name="documentos[]"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    multiple
                                    hidden
                                >
                                <p class="nota-documentos">Formatos permitidos: PDF, JPG y PNG. Máximo 3 archivos de 5 MB cada uno. Es opcional.</p>
                            </div>
                            <ul class="lista-documentos" id="lista-documentos" aria-live="polite"></ul>
                        </fieldset>
                    </div>

                    <!-- ETAPA 4: RESUMEN Y CONFIRMACION -->
                    <div class="contenido-etapa-formulario" data-etapa="4" hidden>
                        <section class="resumen-solicitud" aria-labelledby="titulo-resumen">
                            <div class="encabezado-resumen">
                                <span class="icono-resumen"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i></span>
                                <div>
                                    <h3 id="titulo-resumen">Revisa tu solicitud</h3>
                                    <p>Verifica que tus datos estén correctos antes de continuar.</p>
                                </div>
                            </div>

                            <div class="grupo-resumen">
                                <h4><i class="fa-regular fa-user" aria-hidden="true"></i> Información personal</h4>
                                <div class="lista-resumen">
                                    <div class="dato-resumen"><span>Nombre completo</span><strong id="resumen-nombre">—</strong></div>
                                    <div class="dato-resumen"><span>Correo electrónico</span><strong id="resumen-correo">—</strong></div>
                                    <div class="dato-resumen"><span>Teléfono</span><strong id="resumen-telefono">—</strong></div>
                                    <div class="dato-resumen"><span>Documento</span><strong id="resumen-documento">—</strong></div>
                                </div>
                            </div>

                            <div class="grupo-resumen">
                                <h4><i class="fa-solid fa-house" aria-hidden="true"></i> Tu hogar</h4>
                                <div class="lista-resumen">
                                    <div class="dato-resumen"><span>Comuna</span><strong id="resumen-comuna">—</strong></div>
                                    <div class="dato-resumen"><span>Sector o barrio</span><strong id="resumen-barrio">—</strong></div>
                                    <div class="dato-resumen"><span>Dirección</span><strong id="resumen-direccion">—</strong></div>
                                </div>
                            </div>

                            <div class="grupo-resumen">
                                <h4><i class="fa-regular fa-star" aria-hidden="true"></i> Motivación</h4>
                                <p class="texto-resumen-motivacion" id="resumen-motivacion">—</p>
                            </div>

                            <div class="grupo-resumen">
                                <h4><i class="fa-solid fa-paperclip" aria-hidden="true"></i> Documentos adjuntos</h4>
                                <p id="resumen-documentos">No adjuntaste documentos</p>
                            </div>
                        </section>

                        <div class="aviso-confirmacion-final">
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            <p>Al continuar podrás revisar y aceptar los términos y la política de privacidad.</p>
                        </div>
                    </div>

                    <!-- CONTROLES DE NAVEGACION -->
                    <div class="acciones-formulario">
                        <button type="button" class="boton-cancelar-solicitud" id="btn-cancelar">
                            Cancelar
                        </button>

                        <!-- Enlace de texto para regresar; no es un segundo boton -->
                        <a href="#" class="enlace-anterior-paso" id="btn-anterior" hidden>
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Anterior
                        </a>

                        <!-- Unico boton principal para avanzar -->
                        <button type="button" class="boton-continuar-siguiente-paso" id="btn-continuar">
                            Siguiente
                        </button>
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
    <script src="../js/solicitud-adopcion-js/ubicacion-adopcion.js" defer></script>
    <script src="../js/solicitud-adopcion-js/formulario-adopcion.js" defer></script>
    <script src="../js/solicitud-adopcion-js/favoritos-mascotas.js" defer></script>
</body>
</html>