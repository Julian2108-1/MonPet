<?php
/**
 * MonPet - Detalle de solicitud de adopción.
 * La página consulta únicamente la solicitud del usuario autenticado.
 * Requiere ../db/conexion.php y una conexión MySQLi en $conexion.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/../db/conexion.php';

if (!isset($_SESSION['documento_usuario']) || trim((string) $_SESSION['documento_usuario']) === '') {
    header('Location: ../index.php');
    exit;
}

if (!isset($conexion) || !($conexion instanceof mysqli)) {
    error_log('MonPet: no se encontró una conexión MySQLi válida en $conexion.');
    http_response_code(500);
    exit('No fue posible establecer la conexión con la base de datos. Verifica db/conexion.php.');
}

$conexion->set_charset('utf8mb4');
$documentoUsuario = (string) $_SESSION['documento_usuario'];
$solicitudIdEntrada = $_GET['id'] ?? $_GET['soli_adopcion_id'] ?? null;
$solicitudId = null;

if ($solicitudIdEntrada !== null && $solicitudIdEntrada !== '') {
    $solicitudId = filter_var($solicitudIdEntrada, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if ($solicitudId === false) {
        http_response_code(400);
        exit('El identificador de la solicitud no es válido.');
    }
}

function monpet_e($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function monpet_texto($valor, string $alternativa = 'No registrado'): string
{
    $texto = trim((string) ($valor ?? ''));
    return $texto !== '' ? monpet_e($texto) : monpet_e($alternativa);
}


function monpet_stmt_fetch_assoc($stmt): ?array
{
    $metadata = $stmt->result_metadata();
    if (!$metadata) {
        return null;
    }

    $fila = [];
    $referencias = [];
    foreach ($metadata->fetch_fields() as $campo) {
        $fila[$campo->name] = null;
        $referencias[] = &$fila[$campo->name];
    }
    $metadata->free();

    call_user_func_array([$stmt, 'bind_result'], $referencias);
    if (!$stmt->fetch()) {
        return null;
    }

    return array_map(static function ($valor) { return $valor; }, $fila);
}

function monpet_stmt_fetch_all($stmt): array
{
    $metadata = $stmt->result_metadata();
    if (!$metadata) {
        return [];
    }

    $fila = [];
    $referencias = [];
    foreach ($metadata->fetch_fields() as $campo) {
        $fila[$campo->name] = null;
        $referencias[] = &$fila[$campo->name];
    }
    $metadata->free();

    call_user_func_array([$stmt, 'bind_result'], $referencias);
    $filas = [];
    while ($stmt->fetch()) {
        $filas[] = array_map(static function ($valor) { return $valor; }, $fila);
    }

    return $filas;
}

function monpet_fecha(?string $fecha): string
{
    if (!$fecha) {
        return 'No registrada';
    }

    try {
        return (new DateTimeImmutable($fecha))->format('d/m/Y - h:i a');
    } catch (Exception $e) {
        return 'No registrada';
    }
}

function monpet_edad(?string $fechaNacimiento): string
{
    if (!$fechaNacimiento || $fechaNacimiento === '0000-00-00') {
        return 'Edad no registrada';
    }

    try {
        $nacimiento = new DateTimeImmutable($fechaNacimiento);
        $hoy = new DateTimeImmutable('today');
        if ($nacimiento > $hoy) {
            return 'Edad no registrada';
        }

        $diferencia = $nacimiento->diff($hoy);
        if ($diferencia->y >= 1) {
            return $diferencia->y . ($diferencia->y === 1 ? ' año' : ' años');
        }
        if ($diferencia->m >= 1) {
            return $diferencia->m . ($diferencia->m === 1 ? ' mes' : ' meses');
        }
        return 'Menos de un mes';
    } catch (Exception $e) {
        return 'Edad no registrada';
    }
}

function monpet_estado(string $estado): array
{
    $normalizado = strtolower(trim($estado));
    $normalizado = strtr($normalizado, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u'
    ]);
    $normalizado = preg_replace('/[^a-z0-9]+/', '-', $normalizado) ?: 'sin-estado';

    $estados = [
        'pendiente' => [
            'Pendiente', 'estado-pendiente', 'fa-hourglass-half',
            'Tu solicitud fue registrada y está pendiente de revisión.'
        ],
        'en-revision' => [
            'En revisión', 'estado-en-revision', 'fa-clock',
            'Tu solicitud se encuentra en revisión.'
        ],
        'aprobada' => [
            'Aprobada', 'estado-aprobada', 'fa-circle-check',
            'El estado registrado para tu solicitud es aprobada.'
        ],
        'aprobado' => [
            'Aprobada', 'estado-aprobada', 'fa-circle-check',
            'El estado registrado para tu solicitud es aprobado.'
        ],
        'rechazada' => [
            'Rechazada', 'estado-rechazada', 'fa-circle-xmark',
            'El estado registrado para tu solicitud es rechazada.'
        ],
        'rechazado' => [
            'Rechazada', 'estado-rechazada', 'fa-circle-xmark',
            'El estado registrado para tu solicitud es rechazado.'
        ],
        'retirada' => [
            'Retirada', 'estado-retirada', 'fa-circle-minus',
            'Esta solicitud figura como retirada.'
        ],
        'cancelada' => [
            'Cancelada', 'estado-retirada', 'fa-circle-minus',
            'Esta solicitud figura como cancelada.'
        ],
        'completada' => [
            'Completada', 'estado-aprobada', 'fa-circle-check',
            'El proceso figura como completado.'
        ]
    ];

    if (isset($estados[$normalizado])) {
        return $estados[$normalizado];
    }

    $etiqueta = trim($estado) !== '' ? ucfirst(strtolower(trim($estado))) : 'Sin estado';
    return [$etiqueta, 'estado-generico', 'fa-circle-info', 'Este es el estado registrado en la plataforma.'];
}

function monpet_ruta_imagen(?string $ruta): ?string
{
    $ruta = trim((string) $ruta);
    if ($ruta === '') {
        return null;
    }

    if (preg_match('~^(https?://|/)~i', $ruta)) {
        return $ruta;
    }

    if (str_starts_with($ruta, '../')) {
        return $ruta;
    }

    return '../' . ltrim($ruta, '/');
}

$sqlBase = "
    SELECT
        sa.soli_adopcion_id,
        sa.comuna,
        sa.sector,
        sa.direccion AS direccion_solicitud,
        sa.sustento_economico,
        sa.observaciones,
        sa.estado AS estado_solicitud,
        sa.fecha_solicitud,
        u.documento_usuario,
        u.nombres,
        u.apellidos,
        u.correo,
        u.ciudad AS ciudad_usuario,
        u.pais AS pais_usuario,
        td.nombre AS tipo_documento,
        tv.descripcion_vivienda,
        m.mascota_id,
        m.nombre AS nombre_mascota,
        m.descripcion AS descripcion_mascota,
        m.foto AS foto_mascota,
        m.estado AS estado_mascota,
        m.fecha_nacimiento,
        m.tamano,
        r.nombre AS nombre_raza,
        e.nombre AS nombre_especie,
        ref.nombre AS nombre_refugio,
        ref.ciudad AS ciudad_refugio
    FROM solicitud_adopcion sa
    INNER JOIN usuario u
        ON u.documento_usuario = sa.documento_usuario
    LEFT JOIN tipo_documento td
        ON td.tipo_documento_id = u.tipo_documento_id
    LEFT JOIN tipo_vivienda tv
        ON tv.tipo_vivienda_id = sa.tipo_vivienda_id
    LEFT JOIN mascota m
        ON m.mascota_id = sa.mascota_id
    LEFT JOIN raza r
        ON r.raza_id = m.raza_id
    LEFT JOIN especie e
        ON e.id_especie = r.id_especie
    LEFT JOIN refugio ref
        ON ref.nit = m.nit
    WHERE sa.documento_usuario = ?
";

try {
    if ($solicitudId !== null) {
        $sql = $sqlBase . ' AND sa.soli_adopcion_id = ? LIMIT 1';
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('si', $documentoUsuario, $solicitudId);
    } else {
        $sql = $sqlBase . ' ORDER BY sa.fecha_solicitud DESC, sa.soli_adopcion_id DESC LIMIT 1';
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('s', $documentoUsuario);
    }

    $stmt->execute();
    $solicitud = monpet_stmt_fetch_assoc($stmt);
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('MonPet - Error al consultar detalle de solicitud: ' . $e->getMessage());
    http_response_code(500);
    exit('Ocurrió un error al consultar el detalle de la solicitud.');
}

if (!$solicitud) {
    http_response_code(404);
}

$archivos = [];
$fechaUltimoArchivo = null;

if ($solicitud) {
    try {
        $stmtArchivos = $conexion->prepare(
            'SELECT solicitud_archivo_id, ruta_archivo, tipo_archivo, fecha_carga
             FROM solicitud_archivo
             WHERE soli_adopcion_id = ?
             ORDER BY fecha_carga DESC, solicitud_archivo_id DESC'
        );
        $idSolicitudArchivos = (int) $solicitud['soli_adopcion_id'];
        $stmtArchivos->bind_param('i', $idSolicitudArchivos);
        $stmtArchivos->execute();
        $archivos = monpet_stmt_fetch_all($stmtArchivos);
        $stmtArchivos->close();

        if (!empty($archivos[0]['fecha_carga'])) {
            $fechaUltimoArchivo = $archivos[0]['fecha_carga'];
        }
    } catch (mysqli_sql_exception $e) {
        error_log('MonPet - Error al consultar archivos de solicitud: ' . $e->getMessage());
        $archivos = [];
    }
}

// El perfil del encabezado se consulta por separado del detalle para que
// la inicial siga apareciendo incluso si el usuario aún no tiene solicitudes.
$nombreUsuario = '';
try {
    $stmtUsuario = $conexion->prepare(
        'SELECT nombres, apellidos FROM usuario WHERE documento_usuario = ? LIMIT 1'
    );
    $stmtUsuario->bind_param('s', $documentoUsuario);
    $stmtUsuario->execute();
    $datosUsuario = monpet_stmt_fetch_assoc($stmtUsuario);
    $stmtUsuario->close();
    if ($datosUsuario) {
        $nombreUsuario = trim(($datosUsuario['nombres'] ?? '') . ' ' . ($datosUsuario['apellidos'] ?? ''));
    }
} catch (mysqli_sql_exception $e) {
    error_log('MonPet - Error al consultar el nombre del usuario: ' . $e->getMessage());
}

preg_match('/^./us', $nombreUsuario, $coincidenciaInicial);
$inicialUsuario = $nombreUsuario !== '' ? strtoupper($coincidenciaInicial[0] ?? substr($nombreUsuario, 0, 1)) : 'U';
$estadoInfo = $solicitud ? monpet_estado((string) $solicitud['estado_solicitud']) : null;
$fotoMascota = $solicitud ? monpet_ruta_imagen($solicitud['foto_mascota'] ?? null) : null;
$fechaEnvio = $solicitud ? monpet_fecha($solicitud['fecha_solicitud'] ?? null) : 'No registrada';
$fechaUltimoArchivoTexto = $fechaUltimoArchivo ? monpet_fecha($fechaUltimoArchivo) : 'Sin documentos adjuntos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonPet - Detalle de solicitud de adopción</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Rancho&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/detalle_solicitud.css?v=20261117">
</head>
<body>
    <header>
        <div class="contenedor-logo">
            <a href="../index.php" aria-label="Ir al inicio de MonPet">
                <div class="icono-logo"><i class="fa-solid fa-paw"></i></div>
                <div class="texto-logo">
                    <h1><span class="texto-titulo-1">Mon</span><span class="texto-titulo-2">Pet</span></h1>
                    <p class="slogan-banner">Amor que cambia vidas <i class="fa-solid fa-heart"></i></p>
                </div>
            </a>
        </div>

        <nav class="links-navegacion" aria-label="Navegación principal">
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
            <div class="icono-notificacion" aria-label="Notificaciones">
                <i class="fa-regular fa-bell"></i>
            </div>
            <div class="usuario-perfil">
                <span class="foto-usuario foto-iniciales" aria-hidden="true"><?= monpet_e($inicialUsuario) ?></span>
                <div class="info-usuario">
                    <strong><?= monpet_texto($nombreUsuario, 'Usuario') ?></strong>
                    <small>Mi cuenta</small>
                </div>
            </div>
        </div>
    </header>

    <div class="zona-principal-cuenta">
        <aside class="barra-lateral-reportes">
            <nav class="navegacion-lateral-reportes" aria-label="Menú de cuenta">
                <a href="../index.php" class="elemento-navegacion-reportes">
                    <i class="fa-solid fa-house"></i><span>Inicio</span>
                </a>
                <span class="elemento-navegacion-reportes enlace-pendiente" aria-disabled="true" title="Ruta no incluida en los archivos recibidos">
                    <i class="fa-regular fa-user"></i><span>Mi perfil</span>
                </span>
                <span class="elemento-navegacion-reportes enlace-pendiente" aria-disabled="true" title="Ruta no incluida en los archivos recibidos">
                    <i class="fa-regular fa-star"></i><span>Mis favoritos</span>
                </span>
                <a href="mis_solicitudes.php" class="elemento-navegacion-reportes activo" aria-current="page">
                    <i class="fa-regular fa-file-lines"></i><span>Mis solicitudes</span>
                </a>
                <span class="elemento-navegacion-reportes enlace-pendiente" aria-disabled="true" title="Ruta no incluida en los archivos recibidos">
                    <i class="fa-regular fa-flag"></i><span>Mis reportes</span>
                </span>
                <span class="elemento-navegacion-reportes enlace-pendiente" aria-disabled="true" title="Ruta no incluida en los archivos recibidos">
                    <i class="fa-regular fa-comment-dots"></i><span>Mensajes</span>
                </span>
                <span class="elemento-navegacion-reportes enlace-pendiente" aria-disabled="true" title="Ruta no incluida en los archivos recibidos">
                    <i class="fa-solid fa-gear"></i><span>Configuración</span>
                </span>
            </nav>

            <div class="tarjeta-ayuda-reportes">
                <div class="icono-ayuda-reportes"><i class="fa-solid fa-headset"></i></div>
                <div class="informacion-ayuda-reportes">
                    <h3>¿Necesitas ayuda?</h3>
                    <p>Estamos aquí para ayudarte.</p>
                    <span class="enlace-soporte-reportes">Contactar soporte <i class="fa-solid fa-arrow-up-right-from-square"></i></span>
                </div>
            </div>
        </aside>

        <main class="contenido-detalle-solicitud">
            <nav class="migas-navegacion" aria-label="Ruta de navegación">
                <a href="../index.php" class="enlace-miga"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
                <i class="fa-solid fa-chevron-right separador-miga"></i>
                <span class="enlace-miga">Mi cuenta</span>
                <i class="fa-solid fa-chevron-right separador-miga"></i>
                <a href="mis_solicitudes.php" class="enlace-miga">Mis solicitudes</a>
                <i class="fa-solid fa-chevron-right separador-miga"></i>
                <span class="miga-actual">Detalle de solicitud</span>
            </nav>

            <?php if (!$solicitud): ?>
                <section class="mensaje-sin-solicitud" role="status">
                    <i class="fa-regular fa-folder-open"></i>
                    <h1>No encontramos esta solicitud</h1>
                    <p>Verifica el identificador o vuelve a la sección de mis solicitudes.</p>
                    <a href="mis_solicitudes.php" class="boton-volver"><i class="fa-solid fa-arrow-left"></i><span>Volver a mis solicitudes</span></a>
                </section>
            <?php else: ?>
                <section class="encabezado-detalle">
                    <div class="titulo-detalle">
                        <div class="icono-titulo"><i class="fa-solid fa-file-lines"></i></div>
                        <div class="texto-encabezado">
                            <h1>Detalle de solicitud de adopción</h1>
                            <p>N.º de solicitud: <strong>#<?= (int) $solicitud['soli_adopcion_id'] ?></strong></p>
                        </div>
                    </div>
                    <a href="mis_solicitudes.php" class="boton-volver">
                        <i class="fa-solid fa-arrow-left"></i><span>Volver a mis solicitudes</span>
                    </a>
                </section>

                <section class="tarjeta-estado-solicitud">
                    <div class="bloque-estado-actual">
                        <h2>Estado actual</h2>
                        <div class="estado-en-revision <?= monpet_e($estadoInfo[1]) ?>">
                            <i class="fa-solid <?= monpet_e($estadoInfo[2]) ?>"></i>
                            <span><?= monpet_e($estadoInfo[0]) ?></span>
                        </div>
                        <p><?= monpet_e($estadoInfo[3]) ?></p>
                        <p class="texto-secundario-estado">Este estado corresponde al valor guardado en tu solicitud.</p>
                    </div>

                    <div class="fechas-solicitud">
                        <div class="dato-fecha">
                            <div class="icono-fecha"><i class="fa-regular fa-calendar"></i></div>
                            <div><h3>Fecha de solicitud</h3><p><?= monpet_e($fechaEnvio) ?></p></div>
                        </div>
                        <div class="dato-fecha">
                            <div class="icono-fecha"><i class="fa-solid fa-paperclip"></i></div>
                            <div><h3>Último documento adjuntado</h3><p><?= monpet_e($fechaUltimoArchivoTexto) ?></p></div>
                        </div>
                    </div>

                    <div class="ilustracion-estado" aria-hidden="true">
                        <img src="../img/bgKYZ01.svg" alt="">
                    </div>
                </section>

                <section class="tarjeta-resumen-solicitud">
                    <div class="resumen-mascota">
                        <h2>Mascota</h2>
                        <?php if (!empty($solicitud['mascota_id'])): ?>
                            <div class="ficha-mascota">
                                <?php if ($fotoMascota): ?>
                                    <img class="foto-mascota" src="<?= monpet_e($fotoMascota) ?>" alt="Fotografía de <?= monpet_e($solicitud['nombre_mascota']) ?>">
                                <?php else: ?>
                                    <div class="foto-mascota foto-mascota-vacia" role="img" aria-label="Mascota sin fotografía"><i class="fa-solid fa-paw"></i></div>
                                <?php endif; ?>
                                <div class="datos-mascota">
                                    <div class="nombre-mascota"><h3><?= monpet_texto($solicitud['nombre_mascota'], 'Mascota sin nombre') ?></h3></div>
                                    <p class="raza-mascota">
                                        <?= monpet_texto($solicitud['nombre_raza'], 'Raza no registrada') ?>
                                        <?php if (!empty($solicitud['nombre_especie'])): ?> · <?= monpet_e($solicitud['nombre_especie']) ?><?php endif; ?>
                                    </p>
                                    <div class="caracteristicas-mascota">
                                        <span><i class="fa-regular fa-clock"></i><?= monpet_e(monpet_edad($solicitud['fecha_nacimiento'] ?? null)) ?></span>
                                        <span><i class="fa-solid fa-paw"></i><?= monpet_texto($solicitud['tamano'], 'Tamaño no registrado') ?></span>
                                    </div>
                                    <p class="ubicacion-mascota"><i class="fa-solid fa-location-dot"></i><?= monpet_texto($solicitud['ciudad_refugio'], 'Ubicación no registrada') ?></p>
                                    <span class="etiqueta-disponible"><?= monpet_texto($solicitud['estado_mascota'], 'Estado no registrado') ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="dato-no-disponible">La base de datos no tiene una mascota asociada a esta solicitud.</p>
                        <?php endif; ?>
                    </div>

                    <div class="separador-resumen"></div>

                    <div class="resumen-datos-solicitud">
                        <h2>Resumen de la solicitud</h2>
                        <div class="dato-resumen">
                            <h3>Refugio</h3>
                            <p><i class="fa-solid fa-house"></i><?= monpet_texto($solicitud['nombre_refugio'], 'No asociado a un refugio') ?></p>
                        </div>
                        <div class="dato-resumen">
                            <h3>Tipo de vivienda</h3>
                            <p><i class="fa-solid fa-house-user"></i><?= monpet_texto($solicitud['descripcion_vivienda']) ?></p>
                        </div>
                        <div class="dato-resumen">
                            <h3>Solicitante</h3>
                            <p><i class="fa-regular fa-user"></i><?= monpet_texto($nombreUsuario, 'Usuario') ?></p>
                        </div>
                    </div>
                </section>

                <section class="tarjeta-informacion-solicitud">
                    <div class="pestanas-solicitud">
                        <span class="pestana-activa">Información registrada</span>
                    </div>

                    <div class="fila-datos-solicitud">
                        <article class="bloque-informacion">
                            <h2>Información personal</h2>
                            <div class="fila-dato-personal"><span>Nombre completo</span><strong><?= monpet_texto($nombreUsuario, 'Usuario') ?></strong></div>
                            <div class="fila-dato-personal"><span>Tipo de documento</span><strong><?= monpet_texto($solicitud['tipo_documento']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Número de documento</span><strong><?= monpet_texto($solicitud['documento_usuario']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Correo electrónico</span><strong><?= monpet_texto($solicitud['correo']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Ciudad</span><strong><?= monpet_texto($solicitud['ciudad_usuario']) ?></strong></div>
                        </article>

                        <article class="bloque-informacion">
                            <h2>Información del hogar</h2>
                            <div class="fila-dato-personal"><span>Comuna</span><strong><?= monpet_texto($solicitud['comuna']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Sector o barrio</span><strong><?= monpet_texto($solicitud['sector']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Dirección</span><strong><?= monpet_texto($solicitud['direccion_solicitud']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Tipo de vivienda</span><strong><?= monpet_texto($solicitud['descripcion_vivienda']) ?></strong></div>
                            <div class="fila-dato-personal"><span>Sustento económico</span><strong><?= monpet_texto($solicitud['sustento_economico']) ?></strong></div>
                        </article>
                    </div>

                    <article class="mensaje-adoptante">
                        <h2>Información para adoptar</h2>
                        <div class="texto-mensaje">
                            <i class="fa-solid fa-quote-left" aria-hidden="true"></i>
                            <p><?= nl2br(monpet_texto($solicitud['observaciones'], 'No se registraron observaciones adicionales.')) ?></p>
                        </div>
                    </article>

                    <article class="documentos-adjuntos">
                        <h2>Documentos adjuntos</h2>
                        <?php if (!empty($archivos)): ?>
                            <div class="lista-documentos">
                                <?php foreach ($archivos as $archivo): ?>
                                    <?php
                                        $nombreArchivo = basename(str_replace('\\', '/', (string) $archivo['ruta_archivo']));
                                        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
                                        $iconoArchivo = $extension === 'pdf' ? 'fa-file-pdf' : (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) ? 'fa-file-image' : 'fa-file-lines');
                                        $tipoArchivo = trim((string) ($archivo['tipo_archivo'] ?? ''));
                                    ?>
                                    <div class="documento-adjunto">
                                        <div class="icono-documento"><i class="fa-regular <?= monpet_e($iconoArchivo) ?>"></i></div>
                                        <div class="datos-documento">
                                            <strong><?= monpet_texto($tipoArchivo, 'Archivo adjunto') ?></strong>
                                            <span><?= monpet_texto($nombreArchivo, 'Nombre no disponible') ?></span>
                                        </div>
                                        <a href="descargar_archivo_solicitud.php?id=<?= (int) $archivo['solicitud_archivo_id'] ?>" class="boton-descarga" aria-label="Descargar <?= monpet_e($nombreArchivo) ?>">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="dato-no-disponible">Esta solicitud no tiene documentos adjuntos registrados.</p>
                        <?php endif; ?>
                    </article>
                </section>

                <section class="aviso-retiro-solicitud">
                    <div>
                        <h2>¿Deseas retirar tu solicitud?</h2>
                        <p>La opción para retirar una solicitud todavía está pendiente de desarrollo.</p>
                    </div>
                    <button type="button" class="boton-retirar-solicitud" disabled aria-disabled="true">
                        <i class="fa-regular fa-trash-can"></i><span>Retirar solicitud</span>
                    </button>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <footer class="footer">
        <div class="footer-onda"><img src="../img/fondo_onda_2.png" alt=""></div>
        <div class="footer-contenedor">
            <div class="footer-logo">
                <div class="icono-logo2"><i class="fa-solid fa-paw"></i></div>
                <div class="footer-informacion">
                    <h2>MonPet</h2>
                    <p>Conectamos corazones para que cada<br>mascota encuentre un hogar lleno<br>de amor y cuidado.</p>
                    <div class="footer-redes" aria-label="Redes sociales">
                        <span aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></span>
                        <span aria-label="Instagram"><i class="fa-brands fa-instagram"></i></span>
                        <span aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></span>
                        <span aria-label="YouTube"><i class="fa-brands fa-youtube"></i></span>
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
                    <li><span>Términos y condiciones</span></li>
                    <li><span>Política de privacidad</span></li>
                    <li><span>Aviso legal</span></li>
                </ul>
            </div>

            <div class="footer-contacto">
                <h3>Contacto</h3>
                <ul>
                    <li><i class="fa-solid fa-location-dot"></i>Bogotá, Colombia</li>
                    <li><i class="fa-solid fa-envelope"></i>contacto@monpet.com</li>
                    <li><i class="fa-solid fa-phone"></i>+57 300 123 4567</li>
                    <li><i class="fa-regular fa-clock"></i>Lun - Vie: 8:00 a. m. - 6:00 p. m.</li>
                </ul>
            </div>
        </div>
        <div class="footer-inferior"><p>© <?= date('Y') ?> MonPet. Todos los derechos reservados.</p></div>
    </footer>
</body>
</html>
