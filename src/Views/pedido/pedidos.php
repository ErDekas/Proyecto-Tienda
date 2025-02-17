<ul class="pedidos">
    <?php if(empty($orders)):?>
        <h2>No hay pedidos</h2>
    <?php else: ?>
    <?php foreach ($orders as $order): ?>
        <li class="pedido">
            <table border="1" class="detallesPedidos">
                <tr>
                    <th>Número de pedido</th>
                    <th>Coste</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Dirección</th>
                </tr>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['coste'] ?></td>
                    <td><?= $order['estado'] ?></td>
                    <td><?= $order['fecha'] ?></td>
                    <td><?= $order['direccion'] ?></td>
                </tr>
            </table>
            <h2>Lineas del pedido</h2>
            
                    <table border="1" class="lineasPedidos">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                        </tr>
                        <?php foreach ($ordersLine as $line):
                                foreach($line as $singerline):
                                    if ($singerline['pedido_id'] == $order['id']):
                        ?>       
                        <tr>
                            <td>
                                <?php 
                                    $productId = $singerline['producto_id'];
                                    echo isset($_SESSION['productsOrders'][$productId]) 
                                        ? $_SESSION['productsOrders'][$productId] 
                                        : 'Producto desconocido'; 
                                ?>
                            </td>
                            <td><?= $singerline['unidades'] ?></td>
                        </tr>
                        <?php endif; endforeach; endforeach;?>
                    </table>
                    <?php if ($admin): ?>
                        <a href="<?= BASE_URL?>pedido/actualizar/<?= htmlspecialchars($order['id']) ?>" class="botonesProductos">Actualizar estado</a>
                    <?php endif; ?>
        </li>
    <?php endforeach; ?>
    <?php endif;?>
</ul>