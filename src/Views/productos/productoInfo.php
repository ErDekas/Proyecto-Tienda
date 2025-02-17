<div id="productDetail">

    <?php foreach ($details as $detail): ?>

        <div class="cardDetail">
            <div class="imageDetail">
                <img src="<?= BASE_URL ?>../../public/IMG/<?= htmlspecialchars($detail["imagen"]) ?>" alt="producto">
            </div>
            <div class="bodyDetail">
                <div class="titleDetail"><?= htmlspecialchars($detail["nombre"]) ?></div>
                <div class="descriptionDetail"><?= htmlspecialchars($detail["descripcion"]) ?></div>
                <div class="priceDetail">Precio: <?= htmlspecialchars($detail["precio"]) ?>€</div>
                <div class="stockDetail">Número de Unidades:
                    <?php
                    if ($detail["stock"] != 0) {
                        echo htmlspecialchars($detail["stock"]);
                    } else {
                        echo "Agotado";
                    }
                    ?></div>
                <div class="ofertDetail">Oferta de <?= htmlspecialchars($detail["oferta"]) ?>%</div>
                <?php if ($detail["stock"] != 0): ?>
                    <div class="buttonCart">
                        <a href="<?= BASE_URL ?>Cart/addProduct/<?= htmlspecialchars($detail["id"]) ?>" class="botonesProductos">Añadir al carrito</a>
                    </div>
                <?php endif; ?>
            </div>


        </div>

    <?php endforeach; ?>


</div>