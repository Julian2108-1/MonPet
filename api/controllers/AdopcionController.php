
<?php

require_once __DIR__ . '/../models/adopcion.php';

class AdopcionController
{
    public static function registrar($conexion)
    {
        try {

            // Asegurar que la sesion este iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener los datos enviados en formato JSON
            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            // Validar que los datos sean correctos
            if (!is_array($datos)) {
                responderJSON(400, [
                    "success" => false,
                    "mensaje" => "Formato JSON invalido."
                ]);
            }

            // Validar campos obligatorios
            if (
                !isset($datos["id_mascota"]) ||
                !isset($datos["motivo_adopcion"])
            ) {
                responderJSON(400, [
                    "success" => false,
                    "mensaje" => "Faltan datos obligatorios."
                ]);
            }

            $idMascota = filter_var(
                $datos["id_mascota"],
                FILTER_VALIDATE_INT
            );

            $motivo = trim($datos["motivo_adopcion"]);

            // Validar identificador de mascota
            if ($idMascota === false || $idMascota <= 0) {
                responderJSON(400, [
                    "success" => false,
                    "mensaje" => "Identificador de mascota invalido."
                ]);
            }

            // Validar motivo
            if (
                $motivo === "" ||
                mb_strlen($motivo) < 10 ||
                mb_strlen($motivo) > 2000
            ) {
                responderJSON(400, [
                    "success" => false,
                    "mensaje" => "El motivo debe tener entre 10 y 2000 caracteres."
                ]);
            }

            // Obtener el documento del usuario autenticado
            if (
                !isset($_SESSION["documento_usuario"]) ||
                trim((string) $_SESSION["documento_usuario"]) === ""
            ) {
                responderJSON(401, [
                    "success" => false,
                    "mensaje" => "Debe iniciar sesion para solicitar una adopcion."
                ]);
            }

            $documentoUsuario = (string) $_SESSION["documento_usuario"];

            // Verificar disponibilidad de la mascota
            if (!Adopcion::mascotaDisponible(
                $conexion,
                $idMascota
            )) {
                responderJSON(409, [
                    "success" => false,
                    "mensaje" => "La mascota no esta disponible."
                ]);
            }

            // Verificar solicitudes existentes
            if (Adopcion::solicitudExistente(
                $conexion,
                $documentoUsuario,
                $idMascota
            )) {
                responderJSON(409, [
                    "success" => false,
                    "mensaje" => "Ya existe una solicitud activa para esta mascota."
                ]);
            }

            // Registrar solicitud
            $idSolicitud = Adopcion::registrar( 
                $conexion,
                $documentoUsuario,
                $idMascota,
                $motivo
            );

            responderJSON(201, [
                "success" => true,
                "mensaje" => "Solicitud de adopcion registrada correctamente.",
                "id_solicitud" => $idSolicitud,
                "estado" => "pendiente"
            ]);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            responderJSON(500, [
                "success" => false,
                "mensaje" => "Ocurrio un error al registrar la solicitud."
            ]);
        }
    }


    // Consultar solicitudes del usuario autenticado
    public static function misSolicitudes($conexion)
    {
        // Verificar que exista una sesion
        if (empty($_SESSION["documento_usuario"])) {

            responderJSON(401, [
                "success" => false,
                "mensaje" => "Debes iniciar sesion para consultar tus solicitudes."
            ]);

        }

        try {

            // Obtener el usuario de la sesion
            $documentoUsuario = (string) $_SESSION["documento_usuario"];

            // Consultar mediante el modelo
            $solicitudes = Adopcion::obtenerPorUsuario(
                $conexion,
                $documentoUsuario
            );

            // Responder al cliente
            responderJSON(200, [
                "success" => true,
                "mensaje" => count($solicitudes) > 0
                    ? "Solicitudes consultadas correctamente."
                    : "No tienes solicitudes registradas.",
                "cantidad" => count($solicitudes),
                "datos" => $solicitudes
            ]);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            responderJSON(500, [
                "success" => false,
                "mensaje" => "Ocurrio un error al consultar tus solicitudes."
            ]);

        }
    }



    // Consultar todas las solicitudes (Administrador)
    public static function listarTodas($conexion)
    {
        // 1. Verificar la sesion del usuario
        if (empty($_SESSION["documento_usuario"])) {

            responderJSON(401, [
                "success" => false,
                "mensaje" => "Debes iniciar sesion."
            ]);

        }

        try {

            // 2. Obtener documento del usuario autenticado
            $documento_usuario = $_SESSION["documento_usuario"];

            // 3. Verificar permisos en la base de datos
            $esAdministrador = Adopcion::esAdministrador(
                $conexion,
                $documento_usuario
            );

            if (!$esAdministrador) {

                responderJSON(403, [
                    "success" => false,
                    "mensaje" => "No tienes permisos para consultar todas las solicitudes."
                ]);

            }

            // 4. Consultar solicitudes
            $solicitudes = Adopcion::obtenerTodas($conexion);

            // 5. Devolver respuesta JSON
            responderJSON(200, [
                "success" => true,
                "mensaje" => "Solicitudes consultadas correctamente.",
                "cantidad" => count($solicitudes),
                "datos" => $solicitudes
            ]);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            responderJSON(500, [
                "success" => false,
                "mensaje" => "Ocurrio un error al consultar las solicitudes."
            ]);

        }
    }

}

?>
