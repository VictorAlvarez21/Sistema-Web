<?php

require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    // Validar campos
    if ($nombre == "" || $correo == "" || $password == "") {

        $mensaje = "Todos los campos son obligatorios.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo no es válido.";

    } elseif (strlen($password) < 8) {

        $mensaje = "La contraseña debe tener mínimo 8 caracteres.";

    } else {

        // Encriptar mediante hash la contraseña
        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {

            // Insertar usuario en la base de datos
            $sql = "INSERT INTO usuarios
                    (nombre, correo, password, rol)
                    VALUES (?, ?, ?, 'usuario')";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $nombre,
                $correo,
                $password_hash
            ]);

            $mensaje = "Cuenta creada correctamente.";

        } catch (PDOException $e) {
            $mensaje = "El correo ya está registrado o ocurrió un error.";
        }

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro ZAZALUNY SHOP</title>
</head>

<body>

    <h1>Crear cuenta</h1>

    <form method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <br><br>

        <label>Correo electrónico:</label>
        <input type="email" name="correo" required>

        <br><br>

        <label>Contraseña:</label>
        <input type="password" name="password"
               minlength="8" required>

        <br><br>

        <button type="submit">Registrarme</button>

    </form>

    <p>
        <?php echo htmlspecialchars($mensaje); ?>
    </p>

    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>

</body>
</html>