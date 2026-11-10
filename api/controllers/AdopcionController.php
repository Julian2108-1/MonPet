
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
}

?>
