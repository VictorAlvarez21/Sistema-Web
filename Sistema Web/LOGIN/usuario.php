<?php

session_start();

// Verificar sesión
if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit();

}

// Verificar rol
if ($_SESSION["rol"] != "usuario") {

    header("Location: administrador.php");
    exit();

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Usuario</title>
</head>

<body>

    <h1>Bienvenido a ZAZALUNY SHOP</h1>

    <h2>
        Hola:
        <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
    </h2>

    <h3>Mi cuenta</h3>

    <ul>
        <li>Consultar productos</li>
        <li>Agregar productos al carrito</li>
        <li>Realizar compras</li>
        <li>Consultar mis pedidos</li>
    </ul>

    <a href="logout.php">Cerrar sesión</a>

</body>
</html>