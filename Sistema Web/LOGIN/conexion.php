<?php
try {
    $ruta = __DIR__ . "/tienda.db";

    $conexion = new PDO("sqlite:" . $ruta);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL,
        correo TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        rol TEXT NOT NULL DEFAULT 'usuario'
    )";

    $conexion->exec($sql);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>