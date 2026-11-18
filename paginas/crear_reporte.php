<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db/conexion.php';

if (!isset($conexion) || !($conexion instanceof mysqli)) {
    http_response_code(500);
    exit('No fue posible establecer la conexión con la base de datos.');
}

$conexion->set_charset('utf8mb4');

function escapar_reporte($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function valor_reporte(string $campo, string $predeterminado = ''): string
{
    return escapar_reporte($_POST[$campo] ?? $predeterminado);
}

function longitud_texto_reporte(string $texto): int
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
}

$documentoUsuario = isset($_SESSION['documento_usuario'])
    ? trim((string)$_SESSION['documento_usuario'])
    : '';
$usuarioAutenticado = false;
$nombreUsuario = 'Usuario';

if ($documentoUsuario !== '') {
    try {
        $consultaUsuario = $conexion->prepare(
            'SELECT nombres, apellidos FROM usuario WHERE documento_usuario = ? LIMIT 1'
        );
        $consultaUsuario->bind_param('s', $documentoUsuario);
        $consultaUsuario->execute();
        $consultaUsuario->bind_result($nombresUsuario, $apellidosUsuario);
        if ($consultaUsuario->fetch()) {
            $usuarioAutenticado = true;
            $nombreUsuario = trim($nombresUsuario . ' ' . $apellidosUsuario);
        }
        $consultaUsuario->close();
    } catch (Throwable $error) {
        error_log('Error consultando el usuario de Crear reporte: ' . $error->getMessage());
    }
}

if (empty($_SESSION['csrf_token_reporte'])) {
    $_SESSION['csrf_token_reporte'] = bin2hex(random_bytes(32));
}
$tokenCsrf = $_SESSION['csrf_token_reporte'];
$erroresReporte = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$usuarioAutenticado) {
        $erroresReporte[] = 'Debes iniciar sesión con una cuenta válida para crear un reporte.';
    }

    $tokenRecibido = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals($tokenCsrf, $tokenRecibido)) {
        $erroresReporte[] = 'La sesión del formulario venció. Recarga la página e inténtalo nuevamente.';
    }

    $tipoReporte = trim((string)($_POST['tipo_reporte'] ?? ''));
    $tituloReporte = trim((string)($_POST['titulo_reporte'] ?? ''));
    $descripcionReporte = trim((string)($_POST['descripcion_reporte'] ?? ''));
    $especieReporte = trim((string)($_POST['especie_reporte'] ?? ''));
    $razaReporte = trim((string)($_POST['raza_reporte'] ?? ''));
    $colorReporte = trim((string)($_POST['color_reporte'] ?? ''));
    $fechaReporte = trim((string)($_POST['fecha_reporte'] ?? ''));
    $estadoAnimal = trim((string)($_POST['estado_animal'] ?? ''));
    $ubicacionReporte = trim((string)($_POST['direccion_reporte'] ?? ''));

    if (!in_array($tipoReporte, ['perdida', 'encontrada'], true)) {
        $erroresReporte[] = 'Selecciona si la mascota está perdida o fue encontrada.';
    }
    if ($tituloReporte === '' || longitud_texto_reporte($tituloReporte) > 150) {
        $erroresReporte[] = 'El título es obligatorio y no debe superar los 150 caracteres.';
    }
    if ($descripcionReporte === '' || longitud_texto_reporte($descripcionReporte) > 500) {
        $erroresReporte[] = 'La descripción es obligatoria y no debe superar los 500 caracteres.';
    }
    if (!in_array($especieReporte, ['perro', 'gato'], true)) {
        $erroresReporte[] = 'Selecciona una especie válida.';
    }
    if (longitud_texto_reporte($razaReporte) > 100) {
        $erroresReporte[] = 'La raza no debe superar los 100 caracteres.';
    }
    if (longitud_texto_reporte($colorReporte) > 50) {
        $erroresReporte[] = 'El color no debe superar los 50 caracteres.';
    }
    $fechaValida = DateTime::createFromFormat('!Y-m-d', $fechaReporte);
    if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fechaReporte) {
        $erroresReporte[] = 'Selecciona una fecha válida para el reporte.';
    }
    if (!in_array($estadoAnimal, ['', 'saludable', 'herido', 'enfermo', 'desconocido'], true)) {
        $erroresReporte[] = 'Selecciona un estado del animal válido.';
    }
    if ($ubicacionReporte === '' || longitud_texto_reporte($ubicacionReporte) > 255) {
        $erroresReporte[] = 'La dirección o referencia es obligatoria y no debe superar los 255 caracteres.';
    }

    // Validar todas las imágenes antes de moverlas al directorio definitivo.
    $archivosValidos = [];
    $mimePermitidos = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    if (isset($_FILES['fotos_reporte']) && is_array($_FILES['fotos_reporte']['name'] ?? null)) {
        $cantidadArchivos = count($_FILES['fotos_reporte']['name']);
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        for ($i = 0; $i < $cantidadArchivos; $i++) {
            $errorArchivo = $_FILES['fotos_reporte']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            if ($errorArchivo === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($errorArchivo !== UPLOAD_ERR_OK) {
                $erroresReporte[] = 'Una de las imágenes no se pudo cargar. Verifica el archivo e inténtalo de nuevo.';
                continue;
            }

            $rutaTemporal = (string)($_FILES['fotos_reporte']['tmp_name'][$i] ?? '');
            $tamanoArchivo = (int)($_FILES['fotos_reporte']['size'][$i] ?? 0);

            if ($tamanoArchivo <= 0 || $tamanoArchivo > 5 * 1024 * 1024) {
                $erroresReporte[] = 'Cada imagen debe pesar máximo 5 MB.';
                continue;
            }
            if ($rutaTemporal === '' || !is_uploaded_file($rutaTemporal)) {
                $erroresReporte[] = 'Se detectó un archivo de imagen no válido.';
                continue;
            }

            $mime = $finfo->file($rutaTemporal);
            if (!isset($mimePermitidos[$mime]) || @getimagesize($rutaTemporal) === false) {
                $erroresReporte[] = 'Solo se permiten imágenes JPG, JPEG o PNG válidas.';
                continue;
            }

            $archivosValidos[] = [
                'tmp_name' => $rutaTemporal,
                'extension' => $mimePermitidos[$mime],
                'mime' => $mime,
            ];
        }
    }

    if (!$erroresReporte) {
        $directorioFotos = __DIR__ . '/../uploads/reportes';
        if (!is_dir($directorioFotos) && !mkdir($directorioFotos, 0755, true) && !is_dir($directorioFotos)) {
            $erroresReporte[] = 'No se pudo preparar el directorio para guardar las imágenes.';
        }
    }

    if (!$erroresReporte) {
        $rutasGuardadas = [];
        $conexionIniciada = false;

        try {
            $conexion->begin_transaction();
            $conexionIniciada = true;

            foreach ($archivosValidos as $archivo) {
                $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $archivo['extension'];
                $destinoAbsoluto = $directorioFotos . DIRECTORY_SEPARATOR . $nombreSeguro;
                if (!move_uploaded_file($archivo['tmp_name'], $destinoAbsoluto)) {
                    throw new RuntimeException('No fue posible mover una de las imágenes cargadas.');
                }
                $rutasGuardadas[] = [
                    'ruta' => 'uploads/reportes/' . $nombreSeguro,
                    'mime' => $archivo['mime'],
                    'absoluta' => $destinoAbsoluto,
                ];
            }

            $fotoPrincipal = $rutasGuardadas[0]['ruta'] ?? null;
            $estadoAnimalBD = $estadoAnimal !== '' ? $estadoAnimal : null;
            $razaBD = $razaReporte !== '' ? $razaReporte : null;
            $colorBD = $colorReporte !== '' ? $colorReporte : null;

            $insertarReporte = $conexion->prepare(
                'INSERT INTO reporte
                (tipo_reporte, titulo, descripcion, especie, raza, color, fecha_reporte,
                 estado_animal, foto, ubicacion, documento_usuario)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insertarReporte->bind_param(
                'sssssssssss',
                $tipoReporte,
                $tituloReporte,
                $descripcionReporte,
                $especieReporte,
                $razaBD,
                $colorBD,
                $fechaReporte,
                $estadoAnimalBD,
                $fotoPrincipal,
                $ubicacionReporte,
                $documentoUsuario
            );
            $insertarReporte->execute();
            $idReporte = (int)$conexion->insert_id;
            $insertarReporte->close();

            if ($rutasGuardadas) {
                $insertarFoto = $conexion->prepare(
                    'INSERT INTO reporte_foto (reporte_id, ruta_foto, tipo_archivo) VALUES (?, ?, ?)'
                );
                foreach ($rutasGuardadas as $foto) {
                    $rutaFoto = $foto['ruta'];
                    $tipoArchivo = $foto['mime'];
                    $insertarFoto->bind_param('iss', $idReporte, $rutaFoto, $tipoArchivo);
                    $insertarFoto->execute();
                }
                $insertarFoto->close();
            }

            $conexion->commit();
            $_SESSION['mensaje_reporte_exito'] = 'Tu reporte se registró correctamente.';
            header('Location: crear_reporte.php');
            exit;
        } catch (Throwable $error) {
            if ($conexionIniciada) {
                $conexion->rollback();
            }
            foreach ($rutasGuardadas ?? [] as $foto) {
                if (is_file($foto['absoluta'])) {
                    @unlink($foto['absoluta']);
                }
            }
            error_log('Error guardando reporte en MonPet: ' . $error->getMessage());
            $erroresReporte[] = 'No fue posible guardar el reporte. Verifica que la actualización de la base de datos esté instalada e inténtalo nuevamente.';
        }
    }
}

$mensajeExito = $_SESSION['mensaje_reporte_exito'] ?? '';
unset($_SESSION['mensaje_reporte_exito']);
$tipoSeleccionado = (string)($_POST['tipo_reporte'] ?? 'perdida');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear reporte - MonPet</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/crear_reporte.css?v=20261117-ajuste2">
</head>

<body>
    <!-- HEADER PRINCIPAL: estilos compartidos de la interfaz MonPet -->
    <header>
        <div class="contenedor-logo">
            <a href="../index.php" aria-label="Ir al inicio de MonPet">
                <div class="icono-logo"><i class="fa-solid fa-paw" aria-hidden="true"></i></div>
                <div class="texto-logo">
                    <h1><span class="texto-titulo-1">Mon</span><span class="texto-titulo-2">Pet</span></h1>
                    <p class="slogan-banner">Amor que cambia vidas <i class="fa-solid fa-heart" aria-hidden="true"></i></p>
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

        <div class="acciones-usuario">
            <button type="button" class="boton-mensajes" aria-label="Notificaciones">
                <i class="fa-regular fa-bell" aria-hidden="true"></i>
                <span class="contador-alertas">3</span>
            </button>
            <div class="perfil-usuario">
                <img src="../img/perfil.jpg" alt="Foto de perfil" class="foto-usuario">
                <span><?= escapar_reporte($nombreUsuario) ?></span>
                <button type="button" aria-label="Opciones del perfil">
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="contenido-crear-reporte">

        <!-- =====================================================
             ENCABEZADO DE LA PÁGINA
             ===================================================== -->

        <div class="ruta-navegacion-reporte">

            <a href="../index.php" class="enlace-ruta-reporte">
                <i class="fa-solid fa-house"></i>
                <span>Inicio</span>
            </a>

            <i class="fa-solid fa-chevron-right separador-ruta-reporte"></i>

            <a href="../paginas/reportes_comunidad.php" class="enlace-ruta-reporte">
                Reportes
            </a>

            <i class="fa-solid fa-chevron-right separador-ruta-reporte"></i>

            <span class="ruta-actual-reporte">
                Crear reporte
            </span>

        </div>


        <section class="encabezado-crear-reporte">

            <div class="icono-encabezado-reporte">
                <i class="fa-solid fa-bullhorn"></i>
            </div>

            <div class="texto-encabezado-reporte">

                <h1>Crear reporte</h1>

                <p>
                    Tu reporte puede ayudar a encontrar una mascota perdida,
                    denunciar un caso de abandono <br>o alertar sobre una situación
                    de riesgo. Todos juntos hacemos una comunidad más segura.
                </p>

            </div>

        </section>

        <?php if ($mensajeExito !== ''): ?>
            <div class="mensaje-formulario mensaje-exito" role="status">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <?= escapar_reporte($mensajeExito) ?>
            </div>
        <?php endif; ?>

        <?php if ($erroresReporte): ?>
            <div class="mensaje-formulario mensaje-error" role="alert">
                <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                <div>
                    <strong>No se pudo enviar el reporte:</strong>
                    <ul>
                        <?php foreach ($erroresReporte as $errorReporte): ?>
                            <li><?= escapar_reporte($errorReporte) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$usuarioAutenticado): ?>
            <div class="mensaje-formulario mensaje-error" role="status">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                Para registrar un reporte debes iniciar sesión con una cuenta de MonPet.
            </div>
        <?php endif; ?>

        <form class="formulario-crear-reporte" id="formulario-reporte" method="POST"
            action="crear_reporte.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= escapar_reporte($tokenCsrf) ?>">

        <!-- =====================================================
             PASO 1 - TIPO DE REPORTE
             ===================================================== -->

        <section class="seccion-reporte seccion-tipo-reporte">

            <div class="titulo-seccion-reporte">

                <span class="numero-seccion-reporte">1</span>

                <div>
                    <h2>Tipo de reporte</h2>

                    <p>
                        Selecciona el tipo de reporte que deseas realizar.
                    </p>
                </div>

            </div>


            <div class="opciones-tipo-reporte">

                <!-- Mascota perdida -->
                <label class="opcion-tipo-reporte <?= $tipoSeleccionado === 'perdida' ? 'opcion-tipo-seleccionada' : '' ?>">

                    <input type="radio" name="tipo_reporte" value="perdida" required <?= $tipoSeleccionado === 'perdida' ? 'checked' : '' ?>>

                    <span class="marca-seleccion-tipo">
                        <i class="fa-solid fa-check"></i>
                    </span>

                    <i class="fa-solid fa-paw icono-tipo-reporte"></i>

                    <strong>Mascota perdida</strong>

                    <span class="descripcion-tipo-reporte">
                        Reporta una mascota que se ha extraviado.
                    </span>

                </label>


                <!-- Mascota encontrada -->
                <label class="opcion-tipo-reporte <?= $tipoSeleccionado === 'encontrada' ? 'opcion-tipo-seleccionada' : '' ?>">

                    <input type="radio" name="tipo_reporte" value="encontrada" <?= $tipoSeleccionado === 'encontrada' ? 'checked' : '' ?>>

                    <span class="marca-seleccion-tipo">
                        <i class="fa-solid fa-check"></i>
                    </span>

                    <i class="fa-solid fa-house-chimney icono-tipo-reporte"></i>

                    <strong>Mascota encontrada</strong>

                    <span class="descripcion-tipo-reporte">
                        Informa sobre una mascota que encontraste.
                    </span>

                </label>

            </div>

        </section>


        <!-- =====================================================
             PASO 2 - INFORMACIÓN DEL REPORTE
             ===================================================== -->

        <section class="seccion-reporte seccion-informacion-reporte">

            <div class="titulo-seccion-reporte">

                <span class="numero-seccion-reporte">2</span>

                <div>
                    <h2>Información del reporte</h2>

                    <p>
                        Completa los datos de la mascota o la situación.
                    </p>
                </div>

            </div>


            <div class="formulario-informacion-reporte">

                <!-- Título -->
                <div class="campo-reporte campo-titulo-reporte">

                    <label for="titulo_reporte">
                        Título del reporte
                        <span>*</span>
                    </label>

                    <input type="text" id="titulo_reporte" name="titulo_reporte" maxlength="150" required
                        value="<?= valor_reporte('titulo_reporte') ?>"
                        placeholder="Ej. Perro encontrado en el parque">

                </div>


                <!-- Descripción -->
                <div class="campo-reporte campo-descripcion-reporte">

                    <label for="descripcion_reporte">
                        Descripción
                        <span>*</span>
                    </label>

                    <textarea id="descripcion_reporte" name="descripcion_reporte" maxlength="500" required
                        placeholder="Cuéntanos qué pasó, dónde lo viste, cómo es el animal, entre otros detalles..."><?= valor_reporte('descripcion_reporte') ?></textarea>

                    <small class="contador-descripcion-reporte">
                        0/500
                    </small>

                </div>


                <!-- Especie -->
                <div class="campo-reporte campo-especie-reporte">

                    <label for="especie_reporte">
                        Especie
                        <span>*</span>
                    </label>

                    <select id="especie_reporte" name="especie_reporte" required>
                        <option value="">
                            Selecciona una opción
                        </option>

                        <option value="perro" <?= (($_POST['especie_reporte'] ?? '') === 'perro') ? 'selected' : '' ?>>
                            Perro
                        </option>

                        <option value="gato" <?= (($_POST['especie_reporte'] ?? '') === 'gato') ? 'selected' : '' ?>>
                            Gato
                        </option>
                    </select>

                </div>


                <!-- Raza -->
                <div class="campo-reporte campo-raza-reporte">

                    <label for="raza_reporte">
                        Raza
                        <span class="campo-opcional-reporte">
                            (opcional)
                        </span>
                    </label>

                    <input type="text" id="raza_reporte" name="raza_reporte" maxlength="100"
                        value="<?= valor_reporte('raza_reporte') ?>"
                        placeholder="Ej. Criollo, Pastor Alemán...">

                </div>


                <!-- Color -->
                <div class="campo-reporte campo-color-reporte">

                    <label for="color_reporte">
                        Color
                        <span class="campo-opcional-reporte">
                            (opcional)
                        </span>
                    </label>

                    <input type="text" id="color_reporte" name="color_reporte" maxlength="50"
                        value="<?= valor_reporte('color_reporte') ?>" placeholder="Ej. Café, blanco, negro...">

                </div>


                <!-- Fecha -->
                <div class="campo-reporte campo-fecha-reporte">

                    <label for="fecha_reporte">
                        Fecha del reporte
                        <span>*</span>
                    </label>

                    <div class="entrada-icono-reporte">

                        <!-- <i class="fa-regular fa-calendar"></i> -->

                        <input type="date" id="fecha_reporte" name="fecha_reporte" required value="<?= valor_reporte('fecha_reporte') ?>">

                    </div>

                </div>


                <!-- Estado -->
                <div class="campo-reporte campo-estado-reporte">

                    <label for="estado_animal">
                        Estado del animal
                        <span class="campo-opcional-reporte">
                            (opcional)
                        </span>
                    </label>

                    <select id="estado_animal" name="estado_animal">

                        <option value="">
                            Selecciona una opción
                        </option>

                        <option value="saludable" <?= (($_POST['estado_animal'] ?? '') === 'saludable') ? 'selected' : '' ?>>
                            Se encuentra saludable
                        </option>

                        <option value="herido" <?= (($_POST['estado_animal'] ?? '') === 'herido') ? 'selected' : '' ?>>
                            Se encuentra herido
                        </option>

                        <option value="enfermo" <?= (($_POST['estado_animal'] ?? '') === 'enfermo') ? 'selected' : '' ?>>
                            Parece estar enfermo
                        </option>

                        <option value="desconocido" <?= (($_POST['estado_animal'] ?? '') === 'desconocido') ? 'selected' : '' ?>>
                            No se conoce
                        </option>

                    </select>

                </div>

            </div>

        </section>


        <!-- =====================================================
             PASO 3 - UBICACIÓN
             ===================================================== -->

        <section class="seccion-reporte seccion-ubicacion-reporte">

            <div class="titulo-seccion-reporte">

                <span class="numero-seccion-reporte">3</span>

                <div>
                    <h2>Ubicación</h2>

                    <p>
                        Indica el lugar donde viste la mascota o la situación.
                    </p>
                </div>

            </div>


            <div class="aviso-ubicacion-reporte">

                <i class="fa-solid fa-map-location-dot"></i>

                <span>
                    Puedes permitir que tu ubicación se detecte automáticamente
                    o marcarla en el mapa.
                </span>

            </div>


            <label class="ubicacion-actual-reporte">

                <input type="checkbox" id="usar_ubicacion_actual" name="usar_ubicacion_actual" checked>

                <span class="cuadro-checkbox-reporte">
                    <i class="fa-solid fa-check"></i>
                </span>

                <span>
                    Usar mi ubicación actual
                </span>

                <i class="fa-solid fa-location-dot icono-ubicacion-actual"></i>

            </label>


            <div class="campo-reporte campo-direccion-reporte">

                <label for="direccion_reporte">
                    Dirección o referencia
                    <span>*</span>
                </label>

                <input type="text" id="direccion_reporte" name="direccion_reporte" maxlength="255" required
                    value="<?= valor_reporte('direccion_reporte') ?>"
                    placeholder="Ej. Parque principal de Soacha, carrera 7 #12-34">

            </div>


            <!-- Área destinada al mapa -->
            <div class="mapa-ubicacion-reporte">

                <div class="controles-mapa-reporte">

                    <button type="button" aria-label="Acercar mapa">
                        +
                    </button>

                    <button type="button" aria-label="Alejar mapa">
                        −
                    </button>

                </div>


                <div class="marcador-mapa-reporte">
                    <i class="fa-solid fa-location-dot"></i>
                </div>


                <div class="nombre-lugar-mapa">
                    Parque Principal
                    <br>
                    de Soacha
                </div>


                <button type="button" class="boton-seleccionar-mapa">

                    <i class="fa-solid fa-location-dot"></i>

                    Seleccionar en el mapa

                </button>

            </div>

        </section>


        <!-- =====================================================
             PASO 4 - FOTOS Y EVIDENCIAS
             ===================================================== -->

        <section class="seccion-reporte seccion-evidencias-reporte">

            <div class="titulo-seccion-reporte">

                <span class="numero-seccion-reporte">4</span>

                <div>
                    <h2>Fotos y evidencias</h2>

                    <p>
                        Sube imágenes que ayuden a identificar o ubicar
                        la mascota o la situación.
                    </p>
                </div>

            </div>


            <div class="zona-evidencias-reporte">

                <!-- Botón para subir -->
                <label for="fotos_reporte" class="zona-subir-evidencias">

                    <i class="fa-solid fa-cloud-arrow-up"></i>

                    <span>
                        Arrastra tus archivos aquí o haz clic para seleccionar
                    </span>

                    <small>
                        JPG, PNG (máx. 5 MB por imagen)
                    </small>

                    <input type="file" id="fotos_reporte" name="fotos_reporte[]" accept=".jpg,.jpeg,.png" multiple>

                </label>


                <!-- La vista previa de archivos se incorporará en la etapa de JavaScript. -->
                <div class="estado-sin-evidencias" aria-live="polite">
                    <i class="fa-regular fa-images" aria-hidden="true"></i>
                    <span>Las imágenes seleccionadas aparecerán aquí.</span>
                </div>

                <!-- El mismo selector permite elegir varias imágenes en una sola selección. -->
                <label for="fotos_reporte" class="boton-agregar-evidencia">
                    <span class="circulo-agregar-evidencia">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </span>
                    <span>Agregar imágenes</span>
                </label>
            </div>


            <div class="aviso-revision-reporte">

                <i class="fa-solid fa-shield-dog"></i>

                <span>
                    Tu reporte será revisado por nuestro equipo y, si es
                    necesario, se compartirá con las autoridades correspondientes
                    o refugios cercanos.
                </span>

            </div>


            <!-- =================================================
                 BOTONES FINALES
                 ================================================= -->

            <div class="acciones-reporte">

                <a href="../paginas/reportes_comunidad.php" class="boton-cancelar-reporte">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    Cancelar
                </a>


                <button type="submit" class="boton-enviar-reporte" <?= !$usuarioAutenticado ? 'disabled' : '' ?>>

                    <i class="fa-solid fa-paper-plane"></i>

                    Enviar reporte

                </button>

            </div>

        </section>
        </form>

    </main>

    <!-- FOOTER PRINCIPAL -->
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

                    <div class="footer-redes" aria-label="Redes sociales de MonPet">
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
                    <li><span class="enlace-pendiente">Términos y condiciones</span></li>
                    <li><span class="enlace-pendiente">Política de privacidad</span></li>
                    <li><span class="enlace-pendiente">Aviso legal</span></li>
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
                        <a href="mailto:contacto@monpet.com">contacto@monpet.com</a>
                    </li>

                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <a href="tel:+573001234567">+57 300 123 4567</a>
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