
<?php

require_once __DIR__ . '/../controllers/AdopcionController.php';

function manejarRutaAdopciones($metodo, $ruta, $conexion)
{
    // POST: Registrar solicitud
    if ($ruta === '/adopciones' && $metodo === 'POST') {

        AdopcionController::registrar($conexion);

        return true;
    }

    // GET: Consultar solicitudes del usuario
    if ($ruta === '/adopciones/mias' && $metodo === 'GET') {

        AdopcionController::misSolicitudes($conexion);

        return true;
    }

    // GET: Consultar todas las solicitudes (Administrador)
    if ($ruta === '/admin/adopciones' && $metodo === 'GET') {

        AdopcionController::listarTodas($conexion);

        return true;
    }

    // Verificar metodos no permitidos
    if (
        $ruta === '/adopciones' ||
        $ruta === '/adopciones/mias' ||
        $ruta === '/admin/adopciones'
    ) {

        responderJSON(405, [
            "success" => false,
            "mensaje" => "Metodo HTTP no permitido para esta ruta."
        ]);

    }

    return false;
}
