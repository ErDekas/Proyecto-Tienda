<ul class="pedidos">
    <?php if (empty($pedidos)): ?>
        <h2>No hay pedidos</h2>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
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
                <h2>Lineas del pedido</h2>

                <table border="1" class="lineasPedidos">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                    <?php foreach ($lineaDePedidos as $linea):
                        foreach ($linea as $lineaAMostrar):
                            if ($lineaAMostrar['pedido_id'] == $pedido['id']):
                    ?>
                                <tr>
                                    <td>
                                        <?php
                                        $productId = $lineaAMostrar['producto_id'];
                                        echo isset($_SESSION['productosPedidos'][$productId])
                                            ? $_SESSION['productosPedidos'][$productId]
                                            : 'Producto desconocido';
                                        ?>
                                    </td>
                                    <td><?= $lineaAMostrar['unidades'] ?></td>
                                </tr>
                    <?php endif;
                        endforeach;
                    endforeach; ?>
                </table>
                <br><br><br>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>