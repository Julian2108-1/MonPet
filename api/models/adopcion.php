
<?php

class Adopcion
{
    // Verificar si la mascota existe y esta disponible
    public static function mascotaDisponible($conexion, $idMascota)
    {
        $sql = "SELECT m.mascota_id
            FROM mascota m
            INNER JOIN estado_mascota e
                ON m.id_estado = e.id_estado
            WHERE m.mascota_id = ?
            AND e.nombre_estado = 'Disponible'";

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


    // Consultar las solicitudes del usuario autenticado
    public static function obtenerPorUsuario(
        $conexion,
        $documentoUsuario
    ) {

        $sql = "SELECT
                    s.soli_adopcion_id,
                    s.documento_usuario,
                    s.mascota_id,
                    s.observaciones,
                    s.estado,
                    s.fecha_solicitud,
                    s.fecha_actualizacion,
                    m.nombre AS nombre_mascota,
                    m.foto
                FROM solicitud_adopcion s
                INNER JOIN mascota m
                    ON s.mascota_id = m.mascota_id
                WHERE s.documento_usuario = ?
                ORDER BY s.fecha_solicitud DESC";

        $consulta = $conexion->prepare($sql);
        $consulta->bind_param("s", $documentoUsuario);
        $consulta->execute();
        $resultado = $consulta->get_result();
        $solicitudes = $resultado->fetch_all(MYSQLI_ASSOC);
        $consulta->close();
        return $solicitudes;
    }


    
    // Verificar si el usuario tiene el rol Administrador
    public static function esAdministrador(
        $conexion,
        $documento_usuario
    ) {

        $sql = "SELECT r.nombre
                FROM usuario u
                INNER JOIN rol r
                    ON u.rol_id = r.rol_id
                WHERE u.documento_usuario = ?";

        $consulta = $conexion->prepare($sql);
        $consulta->bind_param("s", $documento_usuario);
        $consulta->execute();
        $resultado = $consulta->get_result();
        $usuario = $resultado->fetch_assoc();
        $consulta->close();
        if (!$usuario) {
            return false;
        }

        return strtolower(trim($usuario["nombre"])) === "administrador";
    }


    // Consultar todas las solicitudes de adopcion
    public static function obtenerTodas($conexion)
    {

        $sql = "SELECT
                    s.soli_adopcion_id,
                    s.documento_usuario,
                    s.comuna,
                    s.sector,
                    s.direccion,
                    s.tipo_vivienda_id,
                    tv.descripcion_vivienda,
                    s.sustento_economico,
                    s.observaciones,
                    s.mascota_id,
                    s.estado AS estado_solicitud,
                    s.fecha_solicitud,
                    u.nombres AS nombres_solicitante,
                    u.apellidos AS apellidos_solicitante,
                    u.correo,
                    m.nombre AS nombre_mascota,
                    m.descripcion AS descripcion_mascota,
                    m.foto,
                    em.nombre_estado AS estado_mascota,
                    r.nombre AS raza,
                    e.nombre AS especie
                FROM solicitud_adopcion s
                INNER JOIN usuario u
                    ON s.documento_usuario = u.documento_usuario
                LEFT JOIN tipo_vivienda tv
                    ON s.tipo_vivienda_id = tv.tipo_vivienda_id
                LEFT JOIN mascota m
                    ON s.mascota_id = m.mascota_id
                LEFT JOIN estado_mascota em
                    ON m.id_estado = em.id_estado
                LEFT JOIN raza r
                    ON m.raza_id = r.raza_id
                LEFT JOIN especie e
                    ON r.id_especie = e.id_especie
                ORDER BY s.fecha_solicitud DESC";

        $consulta = $conexion->prepare($sql);
        $consulta->execute();
        $resultado = $consulta->get_result();
        $solicitudes = $resultado->fetch_all(MYSQLI_ASSOC);
        $consulta->close();
        return $solicitudes;
    }


}

?>
