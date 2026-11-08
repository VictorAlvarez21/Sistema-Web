<?php
session_start();

// Verificar sesión
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Verificar que sea administrador
if ($_SESSION["rol"] !== "administrador") {
    header("Location: usuario.php");
    exit();
}

// Crear token de seguridad
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once "conexion.php";

// Procesar eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar"])) {

    // Validar token
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        header("Location: usuarios.php?resultado=error");
        exit();
    }

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    // Impedir que el administrador se elimine a sí mismo
    if (!$id || $id == $_SESSION["id"]) {
        header("Location: usuarios.php?resultado=protegido");
        exit();
    }

    try {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$id]);

        if ($consulta->rowCount() > 0) {
            header("Location: usuarios.php?resultado=eliminado");
        } else {
            header("Location: usuarios.php?resultado=no_encontrado");
        }

        exit();

    } catch (PDOException $e) {
        header("Location: usuarios.php?resultado=error");
        exit();
    }
}

// Consultar usuarios
$sql = "SELECT id, nombre, correo, rol
        FROM usuarios
        ORDER BY id ASC";

$consulta = $conexion->query($sql);
$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

$resultado = $_GET["resultado"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios - ZAZALUNY SHOP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h1 class="mb-4">Gestión de usuarios</h1>

    <p>
        Bienvenido, administrador:
        <strong><?php echo htmlspecialchars($_SESSION["nombre"]); ?></strong>
    </p>

    <!-- Mensajes -->
    <?php if ($resultado == "eliminado"): ?>
        <div class="alert alert-success">
            Usuario eliminado correctamente.
        </div>
    <?php elseif ($resultado == "protegido"): ?>
        <div class="alert alert-warning">
            No puedes eliminar tu propia cuenta.
        </div>
    <?php elseif ($resultado == "no_encontrado"): ?>
        <div class="alert alert-warning">
            El usuario seleccionado no existe.
        </div>
    <?php elseif ($resultado == "error"): ?>
        <div class="alert alert-danger">
            Ocurrió un error al intentar eliminar el usuario.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">
            Usuarios registrados
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($usuarios) > 0): ?>

                        <?php foreach ($usuarios as $usuario): ?>

                            <tr>
                                <td>
                                    <?php echo $usuario["id"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($usuario["nombre"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($usuario["correo"]); ?>
                                </td>

                                <td>
                                    <?php if ($usuario["rol"] == "administrador"): ?>
                                        <span class="badge bg-danger">
                                            Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">
                                            Usuario
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>

                                    <?php if ($usuario["id"] != $_SESSION["id"]): ?>

                                        <form method="POST"
                                              onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">

                                            <input type="hidden"
                                                   name="id"
                                                   value="<?php echo $usuario["id"]; ?>">

                                            <input type="hidden"
                                                   name="csrf_token"
                                                   value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>">

                                            <button type="submit"
                                                    name="eliminar"
                                                    class="btn btn-danger btn-sm">

                                                Eliminar

                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            Cuenta actual
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="text-center">
                                No hay usuarios registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <p class="text-muted">
                Total de usuarios: <?php echo count($usuarios); ?>
            </p>

            <a href="administrador.php" class="btn btn-secondary">
                Regresar al panel
            </a>

        </div>
    </div>

</div>

</body>
</html>