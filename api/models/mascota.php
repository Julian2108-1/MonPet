
<?php

class Mascota
{
    // Consultar las mascotas disponibles
    public static function obtenerDisponibles($conexion)
    {
        // Consulta SQL
        $sql = "SELECT
                    mascota_id,
                    nombre,
                    descripcion,
                    altura,
                    tamano,
                    color,
                    foto,
                    estado,
                    pelaje,
                    longitud,
                    fecha_nacimiento,
                    fecha_ingreso,
                    nit,
                    hogar_paso_id,
                    raza_id,
                    documento_voluntario_id,
                    veterinario_id
                FROM mascota
                WHERE estado = ?
                ORDER BY fecha_ingreso DESC";

        // Preparar consulta
        $consulta = $conexion->prepare($sql);

        // Estado que queremos consultar
        $estado = "Disponible";

        // Asociar el parametro
        $consulta->bind_param("s", $estado);

        // Ejecutar consulta
        $consulta->execute();

        // Obtener resultados
        $resultado = $consulta->get_result();

        // Convertir resultados en un arreglo
        $mascotas = $resultado->fetch_all(MYSQLI_ASSOC);

        // Cerrar consulta
        $consulta->close();

        return $mascotas;
    }
}
