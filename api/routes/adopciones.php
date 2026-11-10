
<?php

require_once __DIR__ . '/../controllers/AdopcionController.php';

function manejarRutaAdopciones($metodo, $ruta, $conexion)
{
    if ($ruta === '/adopciones') {

        if ($metodo !== 'POST') {
            responderJSON(405, [
                "success" => false,
                "mensaje" => "Metodo HTTP no permitido."
            ]);
        }

        AdopcionController::registrar($conexion);

        return true;
    }

    return false;
}
