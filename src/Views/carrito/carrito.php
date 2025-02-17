<div id="cart">
    <?php if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])): ?>
        <h2>El carrito esta vacio</h2>
    <?php else: ?>
        <table id="carrito">
            <tr>
                <th></th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th></th>
            </tr>

            <?php foreach ($_SESSION['carrito'] as $cart): ?>
                <tr>
                    <td class="imageCartItem">
                        <img src="<?= BASE_URL ?>../../public/IMG/<?= htmlspecialchars($cart['imagen']) ?>" 
                             alt="<?= htmlspecialchars($cart['nombre']) ?>" 
                             class="imageCart">
                    </td>

                    <td class="nameCartItem">
                        <a href="<?= BASE_URL ?>productos/productoInfo/<?= htmlspecialchars($cart['id']) ?>">
                            <?= htmlspecialchars($cart["nombre"]) ?>
                        </a>
                    </td>

                    <td class="priceCartItem">
                        <?= number_format($cart["precio"], 2) ?> €
                    </td>

                    <td class="amountCartItem">
                        <a href="<?= BASE_URL ?>carrito/bajarMonto/<?= $cart['id'] ?>" 
                           class="cartOperation"
                           title="Reducir cantidad">-</a>

                        <span class="cantidad"><?= htmlspecialchars($cart["cantidad"]) ?></span>

                        <a href="<?= BASE_URL ?>carrito/aumentarMonto/<?= $cart['id'] ?>" 
                           class="cartOperation"
                           title="Aumentar cantidad"
                           <?= $cart["cantidad"] >= $cart["stock"] ? 'style="opacity: 0.5; pointer-events: none;"' : '' ?>>+</a>

                        <?php if (isset($error)): ?>
                            <span class="error"><?= htmlspecialchars($error) ?></span>
                        <?php endif; ?>

                        <?php if ($cart["cantidad"] >= $cart["stock"]): ?>
                            <small class="stock-warning">Stock máximo alcanzado</small>
                        <?php endif; ?>
                    </td>

                    <td class="removeCartItem">
                        <a href="<?= BASE_URL ?>carrito/eliminarProducto/<?= $cart['id'] ?>" 
                           class="botonesProductos"
                           title="Eliminar del carrito">Eliminar producto</a>
                           
                        <?php if (isset($errorRemove)): ?>
                            <span class="error"><?= htmlspecialchars($errorRemove) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="inforCart">
            <h2 id="totalPrice">Precio total: <?= number_format($_SESSION['precio'], 2) ?> €</h2>

            <a href="<?= BASE_URL ?>carrito/limpiarCarrito" 
               class="botonesProductos"
               title="Vaciar el carrito">Vaciar carrito</a>

            <a href="<?= BASE_URL ?>pedido/autenticarPedido" 
               class="botonesProductos"
               title="Proceder con el pedido">Confirmar pedido</a>
        </div>
    <?php endif; ?>
</div>

<style>
.error {
    color: #dc3545;
    font-size: 0.85em;
    display: block;
    margin-top: 5px;
}

.stock-warning {
    color: #ffc107;
    font-size: 0.85em;
    display: block;
    margin-top: 5px;
}

.cantidad {
    margin: 0 8px;
    font-weight: bold;
}

.cartOperation {
    text-decoration: none;
    padding: 0 8px;
    font-weight: bold;
}

.cartOperation:hover {
    opacity: 0.8;
}
</style>