<?php

session_start();

require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    // Buscar al usuario por correo
    $sql = "SELECT * FROM usuarios WHERE correo = ?";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([$correo]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // Verificar contraseña
    if ($usuario && password_verify(
        $password,
        $usuario["password"]
    )) {

        // Regenerar identificador de sesión
        session_regenerate_id(true);

        // Guardar datos en sesión
        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["rol"] = $usuario["rol"];

        // Redireccionar según el rol
        if ($usuario["rol"] == "administrador") {

            header("Location: administrador.php");
            exit();

        } else {

            header("Location: usuario.php");
            exit();

        }

    } else {

        $mensaje = "Correo o contraseña incorrectos.";

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login ZAZALUNY SHOP</title>
</head>

<body>

    <h1>Iniciar sesión</h1>

    <form method="POST">

        <label>Correo electrónico:</label>
        <input type="email" name="correo" required>

        <br><br>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Ingresar</button>

    </form>

    <p>
        <?php echo htmlspecialchars($mensaje); ?>
    </p>

    <a href="registro.php">¿No tienes cuenta? Regístrate</a>

</body>
</html>