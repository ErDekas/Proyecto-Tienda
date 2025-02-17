<div id="cart">

    <?php if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])): ?>
        <h2>El carrito esta vacio</h2>
    <?php else: ?>
        <table id="carrito">
            <th></th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th></th>

            <?php foreach ($_SESSION['carrito'] as $cart): ?>

                <tr>

                    <td class="imageCartItem">
                        <img src="<?= BASE_URL ?>../../public/IMG/<?= $cart['imagen'] ?>" alt="producto" class="imageCart">
                    </td>

                    <td class="nameCartItem">
                        <a href="<?= BASE_URL ?>productos/productoInfo<?= htmlspecialchars($cart['id']) ?>"><?= htmlspecialchars($cart["nombre"]) ?></a>
                    </td>
                    <td class="priceCartItem">
                        <?= htmlspecialchars($cart["precio"]) ?> €
                    </td>
                    <td class="amountCartItem">
                        <a href="<?= BASE_URL ?>carrito/bajarMonto/<?= $cart['id'] ?>" class="cartOperation"> -</a>
                        <?= htmlspecialchars($cart["cantidad"]) ?>
                        <a href="<?= BASE_URL ?>carrito/aumentarMonto/<?= $cart['id'] ?>" class="cartOperation"> + </a>
                        <?php if (isset($error)): ?>
                            <span><?= $error ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="removeCartItem">
                        <a href="<?= BASE_URL ?>carrito/eliminarProducto/<?= $cart['id'] ?>" class="botonesProductos">Eliminar producto</a>
                        <?php if (isset($errorRemove)): ?>
                            <span><?= $errorRemove ?></span>
                        <?php endif; ?>
                    </td>


</div>


</div>
</tr>

<?php endforeach; ?>


</table>

<div class="inforCart">

    <h2 id="totalPrice">Precio total: <?= $_SESSION['precio'] ?> €</h2>

    <a href="<?= BASE_URL ?>carrito/limpiarCarrito" class="botonesProductos">Vaciar carrito</a>

    <a href="<?= BASE_URL ?>pedido/autenticarPedido" class="botonesProductos">Confirmar pedido</a>

</div>



<?php endif; ?>



</div>