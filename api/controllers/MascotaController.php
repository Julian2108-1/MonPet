
<?php

require_once __DIR__ . '/../models/mascota.php';

class MascotaController
{
    // Servicio para consultar mascotas disponibles
    public static function listarDisponibles($conexion)
    {
        try {

            // Consultar los datos mediante el modelo
            $mascotas = Mascota::obtenerDisponibles($conexion);

            // Enviar respuesta exitosa
            responderJSON(200, [
                "success" => true,
                "mensaje" => count($mascotas) > 0
                    ? "Mascotas disponibles consultadas correctamente."
                    : "No hay mascotas disponibles.",
                "cantidad" => count($mascotas),
                "datos" => $mascotas
            ]);

        } catch (Throwable $e) {

            // Registrar el error
            error_log($e->getMessage());

            // Responder al cliente
            responderJSON(500, [
                "success" => false,
                "mensaje" => "Ocurrio un error al consultar las mascotas."
            ]);

        }
    }
}
