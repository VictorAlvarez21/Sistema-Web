<?php
session_start();
// Verificar que exista una sesión
if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit();
}
// Verificar que sea administrador
if ($_SESSION["rol"] != "administrador") {

    header("Location: usuario.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
</head>
<body>
    <h1>Panel del Administrador</h1>
    <h2>
        Bienvenido:
        <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
    </h2>
    <h3>Administración de la tienda</h3>
    <ul>
        <li>Administrar productos</li>
        <li>Consultar usuarios</li>
        <li>Administrar inventario</li>
        <li>Consultar ventas</li>
    </ul>
    <a href="logout.php">Cerrar sesión</a>
    <a href="usuarios.php" class="btn btn-primary">
    Gestionar usuarios
    </a>
</body>
</html>