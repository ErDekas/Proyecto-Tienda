<div id="listadoCategorias">

    <?php if ($admin): ?>
        <div id="botones">
            <a href="<?= BASE_URL ?>categorias/crear" class="botonesCategorias">Crear una categoria</a>
            <a href="<?= BASE_URL ?>categorias/actualizar" class="botonesCategorias">Editar una categoria</a>
            <a href="<?= BASE_URL ?>categorias/borrar" class="botonesCategorias">Borrar una categoria</a>
        </div>
    <?php endif; ?>

    <h2>Categorias</h2>

    <ul id="lista">
        <?php foreach ($categorias as $categoria): ?>
            <?php if ($admin || $categoria["nombre"] !== "Sin Existencias"): ?>
                <a class="enlace" href="<?= BASE_URL ?>productos/index/<?= htmlspecialchars($categoria["id"]) ?>">
                    <li class="categoriaLista">
                        <?= htmlspecialchars($categoria["nombre"]) ?>
                    </li>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

</div>