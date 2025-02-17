<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/CSS/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <title>Deka's Shop</title>
</head>

<body>
    <header>
        <h1 id="logo"><a href="<?= BASE_URL ?>">Deka's Shop</a></h1>
        <nav class="menu">
            <ul>
                <?php if (isset($_SESSION['usuario'])):  ?>
                    <li id="nombreUsuario"><?= $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellidos'] ?></li>
                    <li><a href="<?= BASE_URL ?>pedido/pedidos">Tus Pedidos</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL ?>categorias/index">Categorias</a></li>
                <li><a href="<?= BASE_URL ?>productos/index">Productos</a></li>
                <li><a href="<?= BASE_URL ?>Cart/loadCart">Ver Carrito</a></li>
                <?php if (!isset($_SESSION['usuario'])):  ?>
                    <li><a href="<?= BASE_URL ?>usuarios/registrar">Registrarse</a></li>
                    <li><a href="<?= BASE_URL ?>usuarios/iniciarSesion">Iniciar Sesión</a></li>
                <?php else: ?>


                    <?php if (isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === "admin"): ?>
                        <li><a href="<?= BASE_URL ?>usuarios/registrar">Registrar Usuarios</a></li>
                    <?php endif; ?>

                    <li><a href="<?= BASE_URL ?>usuarios/datosUsuario">Ver tus datos</a></li>

                    <li><a href="<?= BASE_URL ?>usuarios/cerrarSesion">Cerrar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div id="tienda">