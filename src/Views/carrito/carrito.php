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
                        <img src="<?= BASE_URL ?>../../public/IMG/<?= htmlspecialchars($cart['imagen']) ?>" 
                             alt="producto" 
                             class="imageCart">
                    </td>

                    <td class="nameCartItem">
                        <a href="<?= BASE_URL ?>productos/productoInfo<?= htmlspecialchars($cart['id']) ?>">
                            <?= htmlspecialchars($cart["nombre"]) ?>
                        </a>
                    </td>

                    <td class="priceCartItem">
                        <?= htmlspecialchars($cart["precio"]) ?> €
                    </td>

                    <td class="amountCartItem">
                        <form method="POST" action="<?= BASE_URL ?>carrito/bajarMonto/<?= $cart['id'] ?>" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="cartOperation">-</button>
                        </form>

                        <?= htmlspecialchars($cart["cantidad"]) ?>

                        <form method="POST" action="<?= BASE_URL ?>carrito/aumentarMonto/<?= $cart['id'] ?>" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="cartOperation" <?= $cart["cantidad"] >= $cart["stock"] ? 'disabled' : '' ?>>+</button>
                        </form>

                        <?php if (isset($error)): ?>
                            <span><?= htmlspecialchars($error) ?></span>
                        <?php endif; ?>
                    </td>

                    <td class="removeCartItem">
                        <form method="POST" action="<?= BASE_URL ?>carrito/eliminarProducto/<?= $cart['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="botonesProductos">Eliminar producto</button>
                        </form>

                        <?php if (isset($errorRemove)): ?>
                            <span><?= htmlspecialchars($errorRemove) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="inforCart">
            <h2 id="totalPrice">Precio total: <?= $_SESSION['precio'] ?> €</h2>

            <form method="POST" action="<?= BASE_URL ?>carrito/limpiarCarrito" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button type="submit" class="botonesProductos">Vaciar carrito</button>
            </form>

            <form method="POST" action="<?= BASE_URL ?>pedido/formularioDePedido" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button type="submit" class="botonesProductos">Confirmar pedido</button>
            </form>
        </div>
    <?php endif; ?>
</div>