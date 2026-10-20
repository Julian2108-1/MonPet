
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../paginas/iniciar_sesion.php");
    exit();
}

$correo = trim($_POST["correo"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";

if ($correo === "" || $contrasena === "") {
    exit("Por favor, ingresa el correo y la contraseña.");
}

$sql = "SELECT * FROM usuarios
        WHERE correo = ? AND id_estado = 1";

$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    exit("Error al preparar la consulta: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) !== 1) {
    exit("El correo no existe o el usuario está inactivo.");
}

$usuario = mysqli_fetch_assoc($resultado);

if (!password_verify($contrasena, $usuario["contrasena"])) {
    exit("La contraseña es incorrecta.");
}

session_start();

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nombres"] = $usuario["nombres"];
$_SESSION["apellidos"] = $usuario["apellidos"];
$_SESSION["id_rol"] = $usuario["id_rol"];

$rol = (int) $usuario["id_rol"];

switch ($rol) {
    case 1:
        $destino = "../../paginas/principal.php";
        $mensaje = "Bienvenido Administrador";
        break;

    case 2:
        $destino = "../../admin/principal.php";
        $mensaje = "Bienvenido Veterinario";
        break;

    case 3:
        $destino = "../../admin/principal.php";
        $mensaje = "Bienvenido Voluntario";
        break;

    case 4:
        $destino = "../../index.php";
        $mensaje = "Bienvenido a MonPet";
        break;

    default:
        exit("El rol del usuario no es válido.");
}

mysqli_stmt_close($stmt);

header("Location: " . $destino);
exit();

?>
