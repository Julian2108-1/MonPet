<?php
/*
 * Página de configuración de MonPet.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// El inicio de sesión de MonPet guarda el documento en esta variable de sesión.
$documentoUsuario = trim((string) ($_SESSION['documento_usuario'] ?? ''));

if ($documentoUsuario === '') {
    header('Location: iniciar_sesion.php');
    exit;
}

// En la estructura compartida, conexion.php se encuentra dentro de la carpeta /db.
$rutaConexion = dirname(__DIR__) . '/db/conexion.php';

if (!is_file($rutaConexion)) {
    error_log('MonPet: no se encontró la conexión en: ' . $rutaConexion);
    http_response_code(500);
    exit('No se encontró db/conexion.php. Verifica la ubicación del archivo de conexión.');
}

require_once $rutaConexion;

if (!isset($conexion) || !($conexion instanceof mysqli)) {
    error_log('MonPet: db/conexion.php no creó la variable $conexion.');
    http_response_code(500);
    exit('El archivo db/conexion.php no creó la conexión $conexion.');
}

$usuario = null;
$errorConsulta = false;

try {
    
    $sql = 'SELECT nombres, apellidos, correo, ciudad, direccion, fecha_registro
            FROM usuario
            WHERE documento_usuario = ?
            LIMIT 1';

    $consulta = $conexion->prepare($sql);
    $consulta->bind_param('s', $documentoUsuario);
    $consulta->execute();

    // bind_result evita depender de mysqli_stmt::get_result / mysqlnd.
    $consulta->bind_result(
        $nombresDB,
        $apellidosDB,
        $correoDB,
        $ciudadDB,
        $direccionDB,
        $fechaRegistroDB
    );

    if ($consulta->fetch()) {
        $usuario = [
            'nombres' => $nombresDB,
            'apellidos' => $apellidosDB,
            'correo' => $correoDB,
            'ciudad' => $ciudadDB,
            'direccion' => $direccionDB,
            'fecha_registro' => $fechaRegistroDB,
        ];
    }

    $consulta->close();
} catch (Throwable $error) {
    $errorConsulta = true;
    error_log('MonPet: error al consultar usuario: ' . $error->getMessage());
}

// Se informa en la propia página cuando no hay coincidencia entre la sesión y la tabla.
$cuentaEncontrada = is_array($usuario);

if (!$cuentaEncontrada && !$errorConsulta) {
    error_log('MonPet: no existe usuario con documento guardado en sesión. Documento de sesión: ' . $documentoUsuario);
}

function monpetEscape($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$nombreCompleto = $cuentaEncontrada
    ? trim((string) $usuario['nombres'] . ' ' . (string) $usuario['apellidos'])
    : 'No disponible';
$correoUsuario = $cuentaEncontrada ? (string) ($usuario['correo'] ?? '') : 'No disponible';
$ciudadUsuario = $cuentaEncontrada && trim((string) ($usuario['ciudad'] ?? '')) !== ''
    ? (string) $usuario['ciudad']
    : 'No registrada';
$direccionUsuario = $cuentaEncontrada && trim((string) ($usuario['direccion'] ?? '')) !== ''
    ? (string) $usuario['direccion']
    : 'No registrada';

$fechaRegistroRaw = $cuentaEncontrada ? (string) ($usuario['fecha_registro'] ?? '') : '';
$fechaRegistroTimestamp = $fechaRegistroRaw !== '' ? strtotime($fechaRegistroRaw) : false;
$fechaRegistroMostrar = $fechaRegistroTimestamp !== false
    ? date('d/m/Y', $fechaRegistroTimestamp)
    : 'No disponible';

$paginaActual = 'configuracion.php';

// 1. Obtener la inicial de forma segura
$nombreParaInicial = $cuentaEncontrada ? trim($nombreCompleto) : '';

$inicialUsuario = $nombreParaInicial !== ''
    ? mb_strtoupper(mb_substr($nombreParaInicial, 0, 1, 'UTF-8'), 'UTF-8')
    : 'U';

// 2. Generar la imagen SVG
$letraSegura = htmlspecialchars($inicialUsuario, ENT_XML1 | ENT_QUOTES, 'UTF-8');

$svgAvatar = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">'
           . '<rect width="100" height="100" fill="#4f46e5"/>'
           . '<text x="50" y="54" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif" font-size="48" font-weight="bold">'
           . $letraSegura
           . '</text></svg>';

// 3. Convertir a Base64
$imagenAvatar = 'data:image/svg+xml;base64,' . base64_encode($svgAvatar);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Configuración de la cuenta de usuario de MonPet.">
    <title>MonPet - Configuración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/configuracion.css">
</head>

<body>
    <!-- ENCABEZADO PRINCIPAL -->
    <header>
        <div class="contenedor-logo">
            <a href="../index.php" aria-label="Ir al inicio de MonPet">
                <div class="icono-logo">
                    <i class="fa-solid fa-paw" aria-hidden="true"></i>
                </div>
                <div class="texto-logo">
                    <h1><span class="texto-titulo-1">Mon</span><span class="texto-titulo-2">Pet</span></h1>
                    <p class="slogan-banner">Amor que cambia vidas <i class="fa-solid fa-heart" aria-hidden="true"></i></p>
                </div>
            </a>
        </div>

        <nav class="links-navegacion" aria-label="Navegación principal">
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="solicitud_adopcion.php">Adopción</a></li>
                <li><a href="reportes_comunidad.php">Reportes</a></li>
                <li><a href="refugios.php">Refugios</a></li>
                <li><a href="jornadas.php">Jornadas</a></li>
                <li><a href="nosotros.php">Nosotros</a></li>
            </ul>
        </nav>

        <div class="acciones-usuario">
            <span class="boton-mensajes icono-notificacion-estatico" aria-label="Notificaciones">
                <i class="fa-solid fa-bell" aria-hidden="true"></i>
            </span>
            <a class="perfil-usuario" href="perfil_usuario.php">
                <span class="foto-usuario foto-iniciales"><?= monpetEscape($inicialUsuario) ?></span>
                <span class="info-usuario">
                    <strong><?= monpetEscape($nombreCompleto) ?></strong>
                    <small>Mi cuenta</small>
                </span>
            </a>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL CON MENÚ LATERAL Y CONTENIDO -->
    <div class="contenedor-principal-reportes">
        <aside class="barra-lateral-reportes">
            <nav class="navegacion-lateral-reportes" aria-label="Menú de usuario">
                <a href="../index.php" class="elemento-navegacion-reportes">
                    <i class="fa-solid fa-house" aria-hidden="true"></i>
                    <span>Inicio</span>
                </a>

                <a href="perfil_usuario.php" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-user" aria-hidden="true"></i>
                    <span>Mi perfil</span>
                </a>

                <a href="favoritos.php" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-star" aria-hidden="true"></i>
                    <span>Mis favoritos</span>
                </a>

                <a href="solicitudes.php" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                    <span>Mis solicitudes</span>
                </a>

                <a href="perfil_usuario_reportes.php" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-flag" aria-hidden="true"></i>
                    <span>Mis reportes</span>
                </a>

                <a href="mensajes.php" class="elemento-navegacion-reportes">
                    <i class="fa-regular fa-comment-dots" aria-hidden="true"></i>
                    <span>Mensajes</span>
                </a>

                <a href="<?= monpetEscape($paginaActual) ?>" class="elemento-navegacion-reportes activo" aria-current="page">
                    <i class="fa-solid fa-gear" aria-hidden="true"></i>
                    <span>Configuración</span>
                </a>
            </nav>

            <div class="tarjeta-ayuda-reportes">
                <div class="icono-ayuda-reportes">
                    <i class="fa-solid fa-headset" aria-hidden="true"></i>
                </div>
                <div class="informacion-ayuda-reportes">
                    <h3>¿Necesitas ayuda?</h3>
                    <p>Estamos aquí para ayudarte.</p>
                    <a class="enlace-soporte-reportes" href="contacto.php">
                        Contactar soporte <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- CONTENIDO DE CONFIGURACIÓN -->
        <main class="contenido-panel">
            <?php if ($errorConsulta): ?>
                <div class="aviso-cuenta aviso-cuenta-error" role="alert">
                    No fue posible consultar la tabla <strong>usuario</strong>. Verifica en el archivo .env que DB_NAME coincida con la base de datos importada y que exista la tabla usuario.
                </div>
            <?php elseif (!$cuentaEncontrada): ?>
                <div class="aviso-cuenta" role="alert">
                    La conexión se realizó, pero no se encontró un usuario con el documento guardado en la sesión. Comprueba que el usuario exista en la tabla <strong>usuario</strong> y que el inicio de sesión guarde el mismo documento en <strong>$_SESSION['documento_usuario']</strong>.
                </div>
            <?php endif; ?>
            <div class="migas-pan-reportes" aria-label="Ruta de navegación">
                <a href="../index.php"><i class="fa-solid fa-house" aria-hidden="true"></i> Inicio</a>
                <i class="fa-solid fa-chevron-right separador-miga-reportes" aria-hidden="true"></i>
                <a href="perfil_usuario.php">Mi cuenta</a>
                <i class="fa-solid fa-chevron-right separador-miga-reportes" aria-hidden="true"></i>
                <span aria-current="page">Configuración</span>
            </div>

            <div class="titulo-pagina">
                <div class="circulo-icono"><i class="fa-solid fa-gear" aria-hidden="true"></i></div>
                <div>
                    <h2>Configuración</h2>
                    <p>Gestiona tu cuenta y preferencias.</p>
                </div>
            </div>

            <div class="layout-configuracion">
                <div class="columna-config-principal">
                    <!-- 1. INFORMACIÓN DE LA CUENTA -->
                    <section class="caja-blanca seccion-config" id="informacion-cuenta">
                        <div class="encabezado-caja-config">
                            <div class="titulo-con-icono">
                                <i class="fa-solid fa-user icono-seccion" aria-hidden="true"></i>
                                <h3>Información de la cuenta</h3>
                            </div>
                            <button class="btn-borde-sm" type="button" disabled title="Funcionalidad pendiente de integrar">
                                Editar información
                            </button>
                        </div>

                        <div class="contenido-info-usuario">
                            <div class="lista-datos">
                                <p><small>Nombre completo</small><strong><?= monpetEscape($nombreCompleto) ?></strong></p>
                                <p><small>Correo electrónico</small><strong><?= monpetEscape($correoUsuario) ?></strong></p>
                                <p><small>Ciudad</small><strong><?= monpetEscape($ciudadUsuario) ?></strong></p>
                                <p><small>Dirección</small><strong><?= monpetEscape($direccionUsuario) ?></strong></p>
                                <p><small>Fecha de registro</small><strong><?= monpetEscape($fechaRegistroMostrar) ?></strong></p>
                            </div>
                            <div class="avatar-perfil-caja">
                                <img src="<?php echo $imagenAvatar; ?>" alt="Foto de Perfil">
                                <button class="btn-camara" type="button" disabled aria-label="Cambiar foto de perfil" title="Funcionalidad pendiente de integrar">
                                    <i class="fa-solid fa-camera" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- 2. SEGURIDAD DE LA CUENTA -->
                    <section class="caja-blanca seccion-config">
                        <div class="titulo-con-icono">
                            <i class="fa-solid fa-lock icono-seccion" aria-hidden="true"></i>
                            <h3>Seguridad de la cuenta</h3>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Contraseña</strong>
                                <p class="texto-contrasena">••••••••••••</p>
                            </div>
                            <button class="btn-borde-sm" type="button" disabled title="Funcionalidad pendiente de integrar">Cambiar contraseña</button>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Verificación en dos pasos</strong>
                                <small>Agrega una capa extra de seguridad a tu cuenta.</small>
                            </div>
                            <label class="switch-toggle" aria-label="Verificación en dos pasos (pendiente)">
                                <input type="checkbox" checked disabled>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Sesiones activas</strong>
                                <small>Gestiona los dispositivos donde has iniciado sesión.</small>
                            </div>
                            <button class="btn-borde-sm" type="button" disabled title="Funcionalidad pendiente de integrar">Ver sesiones</button>
                        </div>
                    </section>

                    <!-- 3. PREFERENCIAS DE LA CUENTA -->
                    <section class="caja-blanca seccion-config">
                        <div class="titulo-con-icono">
                            <i class="fa-solid fa-sliders icono-seccion" aria-hidden="true"></i>
                            <h3>Preferencias de la cuenta</h3>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Modo oscuro</strong>
                                <small>Activa el modo oscuro en toda la aplicación.</small>
                            </div>
                            <label class="switch-toggle" aria-label="Modo oscuro (pendiente)">
                                <input type="checkbox" disabled>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="grupo-select-config">
                            <label for="idioma">Idioma</label>
                            <select class="select-config" id="idioma" disabled>
                                <option selected>Español</option>
                                <option>English</option>
                            </select>
                        </div>

                        <div class="grupo-select-config">
                            <label for="zona-horaria">Zona horaria</label>
                            <select class="select-config" id="zona-horaria" disabled>
                                <option selected>(GMT-05:00) Bogotá</option>
                            </select>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Correos y notificaciones</strong>
                                <small>Elige cómo y cuándo quieres recibir notificaciones.</small>
                            </div>
                            <button class="btn-borde-sm" type="button" disabled title="Funcionalidad pendiente de integrar">Administrar</button>
                        </div>
                    </section>

                    <!-- 4. PRIVACIDAD -->
                    <section class="caja-blanca seccion-config">
                        <div class="titulo-con-icono">
                            <i class="fa-solid fa-shield-halved icono-seccion" aria-hidden="true"></i>
                            <h3>Privacidad</h3>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Perfil público</strong>
                                <small>Permitir que otros usuarios vean mi perfil.</small>
                            </div>
                            <label class="switch-toggle" aria-label="Perfil público (pendiente)">
                                <input type="checkbox" checked disabled>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Mostrar mi ubicación aproximada</strong>
                                <small>Permite mostrar la ubicación aproximada en mis publicaciones.</small>
                            </div>
                            <label class="switch-toggle" aria-label="Mostrar ubicación aproximada (pendiente)">
                                <input type="checkbox" checked disabled>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="fila-opcion-seguridad">
                            <div>
                                <strong>Permitir mensajes</strong>
                                <small>Otros usuarios pueden enviarme mensajes.</small>
                            </div>
                            <label class="switch-toggle" aria-label="Permitir mensajes (pendiente)">
                                <input type="checkbox" checked disabled>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </section>

                    <!-- 5. ELIMINACIÓN DE CUENTA -->
                    <section class="caja-blanca seccion-config caja-eliminar">
                        <div class="titulo-con-icono">
                            <i class="fa-solid fa-triangle-exclamation icono-seccion rojo" aria-hidden="true"></i>
                            <h3>Eliminar cuenta</h3>
                        </div>
                        <p class="texto-alerta">Esta acción es permanente y no se puede deshacer.</p>
                        <button class="btn-eliminar" type="button" disabled title="Funcionalidad pendiente de integrar">Eliminar mi cuenta</button>
                    </section>
                </div>

                <!-- COLUMNA LATERAL DE BENEFICIOS -->
                <aside class="columna-config-lateral">
                    <section class="caja-blanca tarjeta-plan">
                        <div class="encabezado-plan">
                            <i class="fa-solid fa-crown icono-corona" aria-hidden="true"></i>
                            <h4>Mis Beneficios</h4>
                        </div>
                        <div class="badge-plan-gratis">Beneficios de mi cuenta MonPet</div>
                        <ul class="lista-beneficios">
                            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Novedades Jornadas</li>
                            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Reportar Casos</li>
                            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Solicitar Adopciones</li>
                            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Mensajería Básica</li>
                        </ul>
                        <span class="btn-ver-planes">Usuario MonPet</span>
                    </section>

                    <section class="caja-blanca tarjeta-agradecimiento-lateral">
                        <div class="ilustracion-mascotas" aria-hidden="true">
                            <i class="fa-solid fa-dog"></i>
                            <i class="fa-solid fa-cat"></i>
                        </div>
                        <h4>Gracias por ser parte de MonPet <i class="fa-solid fa-heart" aria-hidden="true"></i></h4>
                        <p>Tu ayuda hace la diferencia para miles de animales.</p>
                    </section>
                </aside>
            </div>
        </main>
    </div>

    <!-- FOOTER PRINCIPAL -->
    <footer class="footer">
        <div class="footer-onda">
            <img src="../img/fondo_onda_2.png" alt="">
        </div>

        <div class="footer-contenedor">
            <div class="footer-logo">
                <div class="icono-logo2">
                    <i class="fa-solid fa-paw" aria-hidden="true"></i>
                </div>
                <div class="footer-informacion">
                    <h2>MonPet</h2>
                    <p>Conectamos corazones para que cada mascota encuentre un hogar lleno de amor y cuidado.</p>
                    <div class="footer-redes" aria-label="Redes sociales">
                        <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Abrir Facebook; perfil oficial de MonPet pendiente de configurar" title="Facebook (configurar perfil oficial de MonPet)"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                        <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Abrir Instagram; perfil oficial de MonPet pendiente de configurar" title="Instagram (configurar perfil oficial de MonPet)"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                        <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" aria-label="Abrir TikTok; perfil oficial de MonPet pendiente de configurar" title="TikTok (configurar perfil oficial de MonPet)"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></a>
                        <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer" aria-label="Abrir YouTube; canal oficial de MonPet pendiente de configurar" title="YouTube (configurar canal oficial de MonPet)"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-enlaces">
                <h3>Enlaces rápidos</h3>
                <ul>
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="solicitud_adopcion.php">Adopción</a></li>
                    <li><a href="reportes_comunidad.php">Reportes</a></li>
                    <li><a href="refugios.php">Refugios</a></li>
                    <li><a href="nosotros.php">Nosotros</a></li>
                </ul>
            </div>

            <div class="footer-ayuda">
                <h3>Ayuda</h3>
                <ul>
                    <li><a href="preguntas_frecuentes.php">Preguntas frecuentes</a></li>
                    <li><a href="como_adoptar.php">Cómo adoptar</a></li>
                    <li><a href="consejos_cuidado.php">Consejos de cuidado</a></li>
                    <li><a href="contacto.php">Contáctanos</a></li>
                </ul>
            </div>

            <div class="footer-legal">
                <h3>Información legal</h3>
                <ul>
                    <li><a href="terminos_condiciones.php">Términos y condiciones</a></li>
                    <li><a href="politica_privacidad.php">Política de privacidad</a></li>
                    <li><a href="aviso_legal.php">Aviso legal</a></li>
                </ul>
            </div>

            <div class="footer-contacto">
                <h3>Contacto</h3>
                <ul>
                    <li><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Bogotá, Colombia</li>
                    <li><i class="fa-solid fa-envelope" aria-hidden="true"></i> contacto@monpet.com</li>
                    <li><i class="fa-solid fa-phone" aria-hidden="true"></i> +57 300 123 4567</li>
                    <li><i class="fa-regular fa-clock" aria-hidden="true"></i> Lun - Vie: 8:00 a.m. - 6:00 p.m.</li>
                </ul>
            </div>
        </div>

        <div class="footer-inferior">
            <p>© <?= date('Y') ?> MonPet. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
