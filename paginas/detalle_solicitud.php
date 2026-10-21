<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonPet - Detalle de solicitud de adopción</title>

    <!-- Tipografía utilizada en la interfaz -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Hoja de estilos de esta interfaz -->
    <link rel="stylesheet" href="../css/detalle_solicitud.css">
</head>

<body>

    <!-- HEADER PRINCIPAL -->
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

        <div class="perfil-header">
            <div class="icono-notificacion">
                <i class="fa-solid fa-bell"></i>
                <span class="badge-notif">3</span>
            </div>
            <div class="usuario-perfil">
                <img src="../img/perfil.jpg" alt="Laura García" class="foto-usuario">
                <div class="info-usuario">
                    <strong>Laura García <i class="fa-solid fa-chevron-down"></i></strong>
                    <small>Ver perfil</small>
                </div>
            </div>
        </div>
    </header>

    <!-- =========================================================
         CONTENIDO PRINCIPAL DE LA CUENTA
         ========================================================= -->
    <div class="zona-principal-cuenta">
        <aside class="barra-lateral-reportes">
            <nav class="navegacion-lateral-reportes">

                <a href="../index.php" class="elemento-navegacion-reportes">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>

                <a href="#" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-user"></i>
                    <span>Mi perfil</span>
                </a>

                <a href="#" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-star"></i>
                    <span>Mis favoritos</span>
                </a>

                <a href="#" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-file-lines"></i>
                    <span>Mis solicitudes</span>
                </a>

                <a href="perfil_usuario_reportes.html" class="elemento-navegacion-reportes activo">
                    <i class="fa-regular fa-flag"></i>
                    <span>Mis reportes</span>
                </a>

                <a href="#" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-comment-dots"></i>
                    <span>Mensajes</span>

                    <span class="contador-mensajes-reportes">3</span>
                </a>

                <a href="#" class="elemento-navegacion-reportes">
                    <i class="fa-solid fa-gear"></i>
                    <span>Configuración</span>
                </a>

            </nav>

            <!-- Ayuda -->
            <div class="tarjeta-ayuda-reportes">
                <div class="icono-ayuda-reportes">
                    <i class="fa-solid fa-headset"></i>
                </div>

                <div class="informacion-ayuda-reportes">
                    <h3>¿Necesitas ayuda?</h3>
                    <p>Estamos aquí para ayudarte.</p>
                    <a href="#" class="enlace-soporte-reportes">Contactar soporte <i
                            class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </div>
            </div>
        </aside>

        <!-- =====================================================
             CONTENIDO DE LA PÁGINA
             ===================================================== -->
        <main class="contenido-detalle-solicitud">

            <!-- Migas de pan -->
            <nav class="migas-navegacion" aria-label="Ruta de navegación">
                <a href="#" class="enlace-miga">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>

                <i class="fa-solid fa-chevron-right separador-miga"></i>

                <a href="#" class="enlace-miga">Mi cuenta</a>

                <i class="fa-solid fa-chevron-right separador-miga"></i>

                <a href="#" class="enlace-miga">Mis solicitudes</a>

                <i class="fa-solid fa-chevron-right separador-miga"></i>

                <span class="miga-actual">Detalle de solicitud</span>
            </nav>

            <!-- Encabezado de la página -->
            <section class="encabezado-detalle">
                <div class="titulo-detalle">
                    <div class="icono-titulo">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>

                    <div class="texto-encabezado">
                        <h1>Detalle de solicitud de adopción</h1>
                        <p>ID de solicitud: <strong>#ADP-2026-0024</strong></p>
                    </div>
                </div>

                <a href="#" class="boton-volver">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Volver a mis solicitudes</span>
                </a>
            </section>

            <!-- =================================================
                 TARJETA DEL ESTADO DE LA SOLICITUD
                 ================================================= -->
            <section class="tarjeta-estado-solicitud">

                <!-- Estado -->
                <div class="bloque-estado-actual">
                    <h2>Estado actual</h2>

                    <div class="estado-en-revision">
                        <i class="fa-regular fa-clock"></i>
                        <span>En revisión</span>
                    </div>

                    <p>Tu solicitud está siendo revisada por el equipo del refugio.</p>
                    <p>Te notificaremos por correo cuando haya novedades.</p>
                </div>

                <!-- Fechas -->
                <div class="fechas-solicitud">

                    <div class="dato-fecha">
                        <div class="icono-fecha">
                            <i class="fa-regular fa-calendar"></i>
                        </div>

                        <div>
                            <h3>Fecha enviada</h3>
                            <p>25 de julio de 2026 - 04:15 p. m.</p>
                        </div>
                    </div>

                    <div class="dato-fecha">
                        <div class="icono-fecha">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </div>

                        <div>
                            <h3>Última actualización</h3>
                            <p>27 de julio de 2026 - 09:30 a. m.</p>
                        </div>
                    </div>

                </div>

                <!-- Ilustración del estado -->
                <div class="ilustracion-estado" aria-hidden="true">


            </section>

            <!-- =================================================
                 RESUMEN DE LA MASCOTA Y DE LA SOLICITUD
                 ================================================= -->
            <section class="tarjeta-resumen-solicitud">

                <!-- Información de la mascota -->
                <div class="resumen-mascota">
                    <h2>Mascota</h2>

                    <div class="ficha-mascota">
                        <img
                            class="foto-mascota"
                            src="https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=700&q=85"
                            alt="Buddy, Labrador Retriever"
                        >

                        <div class="datos-mascota">
                            <div class="nombre-mascota">
                                <h3>Buddy</h3>
                                <i class="fa-solid fa-mars"></i>
                            </div>

                            <p class="raza-mascota">Labrador Retriever</p>

                            <div class="caracteristicas-mascota">
                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    2 años
                                </span>

                                <span>
                                    <i class="fa-solid fa-paw"></i>
                                    Grande
                                </span>
                            </div>

                            <p class="ubicacion-mascota">
                                <i class="fa-solid fa-location-dot"></i>
                                Bogotá, Cundinamarca
                            </p>

                            <span class="etiqueta-disponible">Disponible para adopción</span>
                        </div>
                    </div>
                </div>

                <!-- Separador -->
                <div class="separador-resumen"></div>

                <!-- Resumen de la solicitud -->
                <div class="resumen-datos-solicitud">
                    <h2>Resumen de la solicitud</h2>

                    <div class="dato-resumen">
                        <h3>Refugio</h3>
                        <p>
                            <i class="fa-solid fa-house"></i>
                            Huelitas Felices
                        </p>
                    </div>

                    <div class="dato-resumen">
                        <h3>Tipo de adopción</h3>
                        <p>
                            <i class="fa-regular fa-clock"></i>
                            Adopción definitiva
                        </p>
                    </div>

                    <div class="dato-resumen">
                        <h3>Miembro del hogar responsable</h3>
                        <p>
                            <i class="fa-regular fa-user"></i>
                            Laura García
                        </p>
                    </div>

                </div>

            </section>

            <!-- =================================================
                 INFORMACIÓN DETALLADA DE LA SOLICITUD
                 ================================================= -->
            <section class="tarjeta-informacion-solicitud">

                <!-- Pestañas visuales -->
                <div class="pestanas-solicitud">
                    <button class="pestana-activa" type="button">
                        Información de la solicitud
                    </button>

                    <button type="button" class="pestana-secundaria">
                        Respuestas del refugio
                        <span class="contador-respuestas">2</span>
                    </button>

                    <button type="button" class="pestana-secundaria">
                        Historial de seguimiento
                    </button>
                </div>

                <!-- Información personal y hogar -->
                <div class="fila-datos-solicitud">

                    <!-- Información personal -->
                    <article class="bloque-informacion">
                        <h2>Información personal</h2>

                        <div class="fila-dato-personal">
                            <span>Nombre completo</span>
                            <strong>Laura García</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Tipo y número de documento</span>
                            <strong>Cédula de ciudadanía - 1234567890</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Correo electrónico</span>
                            <strong>laura.garcia@email.com</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Teléfono</span>
                            <strong>+57 300 123 4567</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Dirección</span>
                            <strong>Carrera 15 # 123-45, Bogotá, Cundinamarca</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Ocupación</span>
                            <strong>Diseñadora gráfica</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Estado civil</span>
                            <strong>Soltera</strong>
                        </div>
                    </article>

                    <!-- Composición del hogar -->
                    <article class="bloque-informacion">
                        <h2>Composición del hogar</h2>

                        <div class="fila-dato-personal">
                            <span>Adultos</span>
                            <strong>2</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Niños</span>
                            <strong>1</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Otras mascotas</span>
                            <strong>1 perro (vacunado y esterilizado)</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Vive en</span>
                            <strong>Apartamento</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Tiene jardín / patio</span>
                            <strong>No</strong>
                        </div>

                        <div class="fila-dato-personal">
                            <span>Tiempo que pasará solo</span>
                            <strong>4 - 6 horas</strong>
                        </div>
                    </article>

                </div>

                <!-- Información escrita por el adoptante -->
                <article class="mensaje-adoptante">
                    <h2>Información para adoptar</h2>

                    <div class="texto-mensaje">
                        <i class="fa-solid fa-quote-left"></i>

                        <p>
                            Siempre he amado los perros y siento que estoy lista para darle a Buddy un hogar lleno de amor,
                            cuidados y estabilidad. Tengo el tiempo, los recursos y la disposición para brindarle una vida feliz.
                        </p>
                    </div>
                </article>

                <!-- Documentos adjuntos -->
                <article class="documentos-adjuntos">
                    <h2>Documentos adjuntos</h2>

                    <div class="lista-documentos">

                        <div class="documento-adjunto">
                            <div class="icono-documento">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>

                            <div class="datos-documento">
                                <strong>Documento de identidad</strong>
                                <span>laura_garcia_cc.pdf</span>
                            </div>

                            <a href="#" class="boton-descarga" aria-label="Descargar documento de identidad">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>

                        <div class="documento-adjunto">
                            <div class="icono-documento icono-residencia">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>

                            <div class="datos-documento">
                                <strong>Comprobante de residencia</strong>
                                <span>recibo_agua_junio.pdf</span>
                            </div>

                            <a href="#" class="boton-descarga" aria-label="Descargar comprobante de residencia">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>

                        <div class="documento-adjunto">
                            <div class="icono-documento icono-imagen">
                                <i class="fa-regular fa-image"></i>
                            </div>

                            <div class="datos-documento">
                                <strong>Foto del hogar</strong>
                                <span>hogar_laura.jpg</span>
                            </div>

                            <a href="#" class="boton-descarga" aria-label="Descargar foto del hogar">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>

                    </div>
                </article>

            </section>

            <!-- =================================================
                 OPCIÓN PARA RETIRAR LA SOLICITUD
                 ================================================= -->
            <section class="aviso-retiro-solicitud">
                <div>
                    <h2>¿Deseas retirar tu solicitud?</h2>
                    <p>Si ya no deseas continuar con el proceso de adopción de Buddy, puedes retirar tu solicitud.</p>
                </div>

                <button type="button" class="boton-retirar-solicitud">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>Retirar solicitud</span>
                </button>
            </section>

        </main>
    </div>

    <footer class="footer">

        <div class="footer-onda">
            <img src="../img/fondo_onda_2.png" alt="fondo_footer">
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


</body>
</html>
