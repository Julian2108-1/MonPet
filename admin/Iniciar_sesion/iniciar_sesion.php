
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

$sql = "SELECT * FROM usuario
        WHERE correo = ? AND estado_id = 1";

$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    exit("Error al preparar la consulta: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) !== 1) {
    mysqli_stmt_close($stmt);
    exit("El correo no existe o el usuario está inactivo.");
}

$usuario = mysqli_fetch_assoc($resultado);

if (!password_verify($contrasena, $usuario["contrasena_hash"])) {
    mysqli_stmt_close($stmt);
    exit("La contraseña es incorrecta.");
}

session_start();

session_regenerate_id(true);

$_SESSION["documento_usuario"] = $usuario["documento_usuario"];
$_SESSION["nombres"] = $usuario["nombres"];
$_SESSION["apellidos"] = $usuario["apellidos"];
$_SESSION["rol_id"] = $usuario["rol_id"];

$rol = (int) $usuario["rol_id"];

switch ($rol) {
    case 1:
        $destino = "../../paginas/principal.php";
        break;

    case 2:
        $destino = "../../admin/principal.php";
        break;

    case 3:
        $destino = "../../admin/principal.php";
        break;

    case 4:
        $destino = "../../index.php";
        break;

    default:
        mysqli_stmt_close($stmt);
        exit("El rol del usuario no es válido.");
}

mysqli_stmt_close($stmt);

header("Location: " . $destino);
exit();

?>
