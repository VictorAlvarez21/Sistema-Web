<?php

session_start();

// Eliminar las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Regresar al login
header("Location: login.php");

exit();

?>