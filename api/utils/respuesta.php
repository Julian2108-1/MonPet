
<?php

function responderJSON($codigo, $contenido)
{
    // Establecer el codigo HTTP
    http_response_code($codigo);

    // Indicar que la respuesta es JSON
    header("Content-Type: application/json; charset=utf-8");

    // Convertir el arreglo PHP a JSON
    echo json_encode(
        $contenido,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    // Finalizar la ejecucion
    exit;
}
