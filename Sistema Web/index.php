<?php
session_start();
require_once __DIR__ . "/LOGIN/conexion.php";
$mensaje = "";
$sesionIniciada = isset($_SESSION["id"]);
// PROCESAR LOGIN
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$sesionIniciada) {
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";
    if (empty($correo) || empty($password)) {
        $mensaje = "Ingresa tu correo y contraseña.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$correo]);
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($usuario && password_verify($password, $usuario["password"])) {
            session_regenerate_id(true);
            $_SESSION["id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["rol"] = $usuario["rol"];
            header("Location: index.php");
            exit();
        } else {
            $mensaje = "Correo o contraseña incorrectos.";
        }
    }
}
// DATOS DE SESIÓN
$sesionIniciada = isset($_SESSION["id"]);
$esAdministrador = $sesionIniciada && $_SESSION["rol"] === "administrador";
$nombreUsuario = $sesionIniciada ? htmlspecialchars($_SESSION["nombre"], ENT_QUOTES, "UTF-8") : "";
// CONEXIÓN A PRODUCTOS
$productos = [];
$productosPorMarca = [];
$errorProductos = "";
$marcaSeleccionada = $_GET["marca"] ?? "todas";
if ($sesionIniciada) {
    try {
        $rutaProductos = __DIR__ . "/db_productos/Productos_reconstruida.db";
        $conexionProductos = new PDO("sqlite:" . $rutaProductos);
        $conexionProductos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqlProductos = "SELECT p.id, p.producto, p.precio, m.nombre AS marca FROM productos p INNER JOIN marcas m ON p.marca_id = m.id ORDER BY m.id, p.id";
        $consultaProductos = $conexionProductos->query($sqlProductos);
        $productos = $consultaProductos->fetchAll(PDO::FETCH_ASSOC);
        // AGRUPAR PRODUCTOS
        foreach ($productos as $producto) {
            $productosPorMarca[$producto["marca"]][] = $producto;
        }
        // FILTRAR POR MARCA
        if ($marcaSeleccionada !== "todas") {
        $productosPorMarca = array_filter($productosPorMarca, function ($listaProductos, $marca) use 
        ($marcaSeleccionada) {
            $idMarca = strtolower($marca);
            $idMarca = str_replace([" ", "ñ"], ["-", "n"], $idMarca);
            return $idMarca === $marcaSeleccionada;
        }, ARRAY_FILTER_USE_BOTH);
        }
    } catch (PDOException $e) {
        $errorProductos = "No fue posible cargar los productos. " . $e->getMessage();
    }
}
// CONTAR PRODUCTOS VISIBLES
$totalProductos = 0;
foreach ($productosPorMarca as $listaProductos) {
    $totalProductos += count($listaProductos);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZAZALUNY SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="LOGIN/estilos.css" rel="stylesheet">
</head>
<body>
<?php if (!$sesionIniciada): ?>
<!-- INICIO DE SESIÓN -->
<header class="header-custom py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">ZAZALUNY SHOP</h1>
            <p class="lead fw-normal mb-0">TIENDA DE ABARROTES</p>
        </div>
    </div>
</header>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle display-3 text-dark"></i>
                        <h2 class="fw-bold mt-3">Iniciar sesión</h2>
                        <p class="text-muted">Ingresa tus datos para acceder a la tienda.</p>
                    </div>
                    <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8"); ?>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="index.php">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control form-control-lg" id="correo" name="correo" placeholder="usuario@correo.com" value="<?php echo htmlspecialchars($_POST["correo"] ?? "", ENT_QUOTES, "UTF-8"); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-lg w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                        </button>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <p class="mb-0">¿No tienes una cuenta?</p>
                        <a href="LOGIN/registro.php" class="fw-bold text-decoration-none">Regístrate aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="py-4 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">GRACIAS POR SU PREFERENCIA</p>
    </div>
</footer>
<?php else: ?>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand fw-bold" href="index.php">ZAZALUNY SHOP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- MENÚ DE PRODUCTOS -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Productos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?marca=todas#productos">TODOS LOS PRODUCTOS</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?marca=coca-cola#productos">COCA COLA</a></li>
                        <li><a class="dropdown-item" href="index.php?marca=bimbo#productos">BIMBO</a></li>
                        <li><a class="dropdown-item" href="index.php?marca=marinela#productos">MARINELA</a></li>
                        <li><a class="dropdown-item" href="index.php?marca=sabritas#productos">SABRITAS</a></li>
                        <li><a class="dropdown-item" href="index.php?marca=la-costena#productos">LA COSTEÑA</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#comentarios">Comentarios</a>
                </li>
            </ul>
            <!-- OPCIONES SEGÚN EL ROL -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="navbar-text me-2"><i class="bi bi-person-circle"></i> Hola, <?php echo $nombreUsuario; ?></span>
                <?php if ($esAdministrador): ?>
                <a href="LOGIN/administrador.php" class="btn btn-outline-primary"><i class="bi bi-speedometer2"></i> Administrador</a>
                <a href="LOGIN/usuarios.php" class="btn btn-outline-dark"><i class="bi bi-people"></i> Usuarios</a>
                <?php else: ?>
                <a href="LOGIN/usuario.php" class="btn btn-outline-primary"><i class="bi bi-person"></i> Mi cuenta</a>
                <?php endif; ?>
                <a href="LOGIN/logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </div>
        </div>
    </div>
</nav>
<!-- ENCABEZADO -->
<header class="header-custom py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">ZAZALUNY SHOP</h1>
            <p class="lead fw-normal mb-0">TIENDA DE ABARROTES</p>
            <p class="mt-3">Bienvenido, <strong><?php echo $nombreUsuario; ?></strong></p>
        </div>
    </div>
</header>
<main>
    <!-- CATÁLOGO DE PRODUCTOS -->
    <section class="py-5" id="productos">
        <div class="container px-4 px-lg-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nuestro catálogo</h2>
                <p class="text-muted">Explora nuestros productos disponibles.</p>
                <span class="badge bg-dark fs-6"><?php echo $totalProductos; ?> productos disponibles</span>
                <?php if ($marcaSeleccionada !== "todas"): ?>
                <p class="mt-3 mb-0">
                    <a href="index.php?marca=todas#productos" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Ver todas las marcas
                    </a>
                </p>
                <?php endif; ?>
            </div>
            <!-- MENSAJE DE ERROR -->
            <?php if (!empty($errorProductos)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorProductos, ENT_QUOTES, "UTF-8"); ?></div>
            <?php endif; ?>
            <!-- PRODUCTOS AGRUPADOS POR MARCA -->
            <?php if (!empty($productosPorMarca)): ?>
                <?php foreach ($productosPorMarca as $marca => $listaProductos): ?>
                <?php $idMarca = strtolower($marca);
                    $idMarca = str_replace([" ", "ñ"], ["-", "n"], $idMarca); ?>
                <div class="mb-5" id="marca-<?php echo htmlspecialchars($idMarca, ENT_QUOTES, "UTF-8"); ?>">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-4">
                        <h3 class="fw-bold mb-0"><?php echo htmlspecialchars($marca, ENT_QUOTES, "UTF-8"); ?></h3>
                        <span class="badge bg-secondary"><?php echo count($listaProductos); ?> productos</span>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($listaProductos as $producto): ?>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="text-center bg-light py-4">
                                    <i class="bi bi-basket2 display-3 text-secondary"></i>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-secondary align-self-start mb-2"><?php echo htmlspecialchars($producto["marca"], ENT_QUOTES, "UTF-8"); ?></span>
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($producto["producto"], ENT_QUOTES, "UTF-8"); ?></h5>
                                    <div class="mt-auto pt-3">
                                        <p class="text-success fw-bold fs-4 mb-0">$<?php echo number_format((float)$producto["precio"], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php elseif (empty($errorProductos)): ?>
            <div class="alert alert-info text-center">
                <?php echo $marcaSeleccionada !== "todas" ? "No se encontraron productos para esta marca." : "No hay productos registrados actualmente."; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
<!-- COMENTARIOS -->
<section class="py-5" id="comentarios">
    <div class="container">
        <div class="comment-container">
            <h2>Déjanos tu comentario</h2>
            <p>Queremos conocer tu opinión.</p>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. Ana García" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                </div>
                <div class="form-group">
                    <label for="comentario">Comentario</label>
                    <textarea id="comentario" name="comentario" placeholder="Escribe tu opinión aquí..." required></textarea>
                </div>
                <button type="submit" class="btn-submit">Enviar comentario</button>
            </form>
        </div>
    </div>
</section>
</main>
<!-- FOOTER -->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">GRACIAS POR SU PREFERENCIA</p>
    </div>
</footer>
<?php endif; ?>
<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>