<ul class="pedidos">
    <?php if(empty($pedidos)):?>
        <h2>No hay pedidos</h2>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
            <h2>Pedido</h2>
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
                        <td><?= $pedido['id'] ?></td>
                        <td><?= $pedido['coste'] ?></td>
                        <td><?= $pedido['estado'] ?></td>
                        <td><?= $pedido['fecha'] ?></td>
                        <td><?= $pedido['direccion'] ?></td>
                    </tr>
                </table>
                <p>
                <h2>Lineas del pedido</h2>
                <table border="1" class="lineasPedidos">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                    <?php if (isset($lineaDePedidos[$pedido['id']]) && !empty($lineaDePedidos[$pedido['id']])): ?>
                        <?php foreach ($lineaDePedidos[$pedido['id']] as $linea): ?>
                            <tr>
                                <td>
                                    <?= $linea['producto_nombre'] ?? 'Producto desconocido' ?>
                                </td>
                                <td><?= $linea['unidades'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No hay líneas para este pedido</td>
                        </tr>
                    <?php endif; ?>
                </table>
                
                <?php if ($admin === true): ?>
                    <a href="<?= BASE_URL ?>pedido/formularioDeActualizacionDePedido/<?= htmlspecialchars($pedido['id']) ?>" class="botonesProductos">
                        Actualizar estado
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>