
<?php

// Carga el inicio de sesion actual para continuar con lo demas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. Cargar las respuestas JSON
require_once __DIR__ . '/utils/respuesta.php';

// 2. Cargar conexion a MySQL
require_once __DIR__ . '/config/database.php';

// 3. Cargar las rutas
require_once __DIR__ . '/routes/mascotas.php';
require_once __DIR__ . '/routes/adopciones.php';

// 4. Identificar el metodo HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

// 5. Obtener la direccion solicitada
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 6. Identificar la carpeta base de la API
$base = rtrim(
    str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])),
    '/'
);

// 7. Obtener solamente la ruta del servicio
if ($base !== '' && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}

$ruta = '/' . trim($uri, '/');

// 8. Ejecutar la ruta correspondiente
$atendida = manejarRutaMascotas(
    $metodo,
    $ruta,
    $conexion
);

if (!$atendida) {
    $atendida = manejarRutaAdopciones(
        $metodo,
        $ruta,
        $conexion
    );
}
// 9. Si la ruta no existe, devolver error 404
if (!$atendida) {

    responderJSON(404, [
        "success" => false,
        "mensaje" => "La ruta solicitada no existe."
    ]);

}
