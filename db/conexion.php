
<?php

// Creamos una variable para guardar la Ruta del archivo .env 
$rutaEnv = dirname(__DIR__) . '/.env';

// Verificar que exista el archivo para que no haya problemas al momento de la conexion
if (!file_exists($rutaEnv)) {
    error_log('No se encontró el archivo .env');
    exit('Error de configuración del servidor.');
}

// Leer las variables del archivo .env paraa tener toda la informacion que contenga el archivo 
$configuracion = parse_ini_file($rutaEnv, false, INI_SCANNER_RAW);

if ($configuracion === false) {
    error_log('No se pudo leer el archivo .env');
    exit('Error de configuración del servidor.');
}

// Obtener los datos de conexión aqui traemos los datos de archivo .env
$host = $configuracion['DB_HOST'] ?? 'localhost';
$puerto = (int) ($configuracion['DB_PORT'] ?? 3306);
$baseDatos = $configuracion['DB_NAME'] ?? '';
$usuario = $configuracion['DB_USER'] ?? '';
$contrasena = $configuracion['DB_PASS'] ?? '';
$charset = $configuracion['DB_CHARSET'] ?? 'utf8mb4';

// Comprobar que existan los datos necesarios para la conexion de la base de datos
if ($baseDatos === '' || $usuario === '') {
    error_log('Faltan datos de conexión en .env');
    exit('Error de configuración del servidor.');
}

// Crear la conexión con MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli(
        $host,
        $usuario,
        $contrasena,
        $baseDatos,
        $puerto
    );

    // Configurar la codificación
    $conexion->set_charset($charset);

} catch (mysqli_sql_exception $e) {
    error_log('Error de conexión MySQLi: ' . $e->getMessage());

    http_response_code(500);
    exit('No fue posible conectar con la base de datos.');
}

