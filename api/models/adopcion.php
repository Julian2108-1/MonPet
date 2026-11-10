
<?php

class Adopcion
{
    // Verificar si la mascota existe y esta disponible
    public static function mascotaDisponible($conexion, $idMascota)
    {
        $sql = "SELECT mascota_id
                FROM mascota
                WHERE mascota_id = ?
                AND estado = 'Disponible'";

        $consulta = $conexion->prepare($sql);
        $consulta->bind_param("i", $idMascota);
        $consulta->execute();

        $resultado = $consulta->get_result();

        $disponible = $resultado->num_rows > 0;

        $consulta->close();

        return $disponible;
    }

    // Verificar si el usuario ya solicito esta mascota
    public static function solicitudExistente(
        $conexion,
        $documentoUsuario,
        $idMascota
    ) {
        $sql = "SELECT soli_adopcion_id
                FROM solicitud_adopcion
                WHERE documento_usuario = ?
                AND mascota_id = ?
                AND estado IN (
                    'pendiente',
                    'en proceso',
                    'aprobada'
                )";

        $consulta = $conexion->prepare($sql);

        $consulta->bind_param(
            "si",
            $documentoUsuario,
            $idMascota
        );

        $consulta->execute();

        $resultado = $consulta->get_result();

        $existe = $resultado->num_rows > 0;

        $consulta->close();

        return $existe;
    }

    // Registrar solicitud de adopcion
    public static function registrar(
        $conexion,
        $documentoUsuario,
        $idMascota,
        $motivo
    ) {
        $sql = "INSERT INTO solicitud_adopcion
                (
                    documento_usuario,
                    mascota_id,
                    observaciones,
                    estado
                )
                VALUES (?, ?, ?, 'pendiente')";

        $consulta = $conexion->prepare($sql);

        $consulta->bind_param(
            "sis",
            $documentoUsuario,
            $idMascota,
            $motivo
        );

        $consulta->execute();

        $idSolicitud = $conexion->insert_id;

        $consulta->close();

        return $idSolicitud;
    }
}

?>
