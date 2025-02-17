<div id="botones">
    <?php if (isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === "admin"): ?>

        <a href="<?= BASE_URL ?>categorias/index" class="botonesProductos">Ver las categorias</a></li>
        <a href="<?= BASE_URL ?>productos/crear" class="botonesProductos">Añadir producto</a>
        <a href="<?= BASE_URL ?>productos/actualizar" class="botonesProductos">Actualizar producto</a>
        <a href="<?= BASE_URL ?>productos/borrar" class="botonesProductos">Borrar producto</a>
    <?php endif ?>
    <?php if(isset($_SESSION['usuario']) && $admin): ?>
        <a href="<?= BASE_URL?>Order/seeAllOrders" class="botonesProductos">Ver todos los pedidos</a>
    <?php endif;?>
    <?php if(isset($_SESSION['usuario'])): ?>
        <a href="<?= BASE_URL?>Order/seeOrders" class="botonesProductos">Ver mis pedidos</a>
    <?php endif;?>

</div>

<div id="producto">



    <h2>Listado de Productos</h2>


    <ul id="listaProductos">
        <?php foreach ($productos as $producto): ?>

            <a href="<?= BASE_URL ?>productos/productoInfo<?= htmlspecialchars($producto['id']) ?>" class="itemProducto">
                <li>
                    <div class="card">
                        <div class="image">
                            <img src="<?= BASE_URL ?>../../public/IMG/<?= htmlspecialchars($producto["imagen"]) ?>" alt="producto">
                        </div>
                        <div class="cuerpo">
                            <div class="titulo"><?php echo htmlspecialchars($producto["nombre"]) ?></div>
                            <div class="descripcion"><?php echo htmlspecialchars($producto["descripcion"]) ?></div>
                            <div class="precio">Precio: <?php echo htmlspecialchars($producto["precio"]) ?>€</div>
                            <div class="stock">Número de Unidades: <?php echo htmlspecialchars($producto["stock"]) ?></div>
                            <div class="oferta">Oferta de <?php echo htmlspecialchars($producto["oferta"]) ?>%</div>
                        </div>
                    </div>
                </li>
            </a>

        <?php endforeach; ?>
    </ul>


</div>