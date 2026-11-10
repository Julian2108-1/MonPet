
<?php

require_once __DIR__ . '/../controllers/MascotaController.php';

function manejarRutaMascotas($metodo, $ruta, $conexion)
{
    // Verificar si la ruta corresponde a mascotas
    if ($ruta !== '/mascotas') {
        return false;
    }

    // Verificar el metodo HTTP
    if ($metodo !== 'GET') {

        responderJSON(405, [
            "success" => false,
            "mensaje" => "Metodo HTTP no permitido para esta ruta."
        ]);

    }

    // Ejecutar el controlador
    MascotaController::listarDisponibles($conexion);

    return true;
}
