<?php

require_once("../../db/conexion.php");

$nombre = $_POST["nombre"] ?? "";
$apellido = $_POST["apellido"] ?? "";
$tipo_documento = $_POST["tipo_documento"] ?? "";
$documento = $_POST["documento"] ?? "";
$fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";
$genero = $_POST["genero"] ?? "";
$correo = $_POST["correo"] ?? "";
$telefono = $_POST["telefono"] ?? "";
$direccion = $_POST["direccion"] ?? "";
$ciudad = $_POST["ciudad"] ?? "";
$contrasena = $_POST["contrasena"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $consultaDocumento = mysqli_query($conexion,
        "SELECT * FROM usuario WHERE documento_usuario='$documento'");

    if (mysqli_num_rows($consultaDocumento) > 0) {

        echo "<script>
                alert('El número de documento ya está registrado');
                window.history.back();
              </script>";
        exit();
    }

    $consultaCorreo = mysqli_query($conexion,
        "SELECT * FROM usuario WHERE correo='$correo'");

    if (mysqli_num_rows($consultaCorreo) > 0) {

        echo "<script>
                alert('El correo ya está registrado');
                window.history.back();
              </script>";
        exit();
    }

    $contrasenaEncriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    $rol = 4;
    $estado = 1;

    $sql = "INSERT INTO usuario
    (
        nombres,
        apellidos,
        tipo_documento_id,
        documento_usuario,
        fecha_nacimiento,
        genero,
        correo,
        telefono,
        direccion,
        ciudad,
        contrasena_hash,
        rol_id,
        estado_id
    )

    VALUES
    (
        '$nombre',
        '$apellido',
        '$tipo_documento',
        '$documento',
        '$fecha_nacimiento',
        '$genero',
        '$correo',
        '$telefono',
        '$direccion',
        '$ciudad',
        '$contrasenaEncriptada',
        '$rol',
        '$estado'
    )";

    if (mysqli_query($conexion, $sql)) {

        echo "<script>
                alert('Cuenta creada correctamente');
                window.location='../../paginas/iniciar_sesion.php';
              </script>";

    } else {

        echo "<script>
                alert('Error al registrar el usuario');
                window.history.back();
              </script>";

    }

}
?>