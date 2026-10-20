<?php

$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "monpet";

$conexion = mysqli_connect($servidor, $usuario, $contrasena, $base_datos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");

?>
