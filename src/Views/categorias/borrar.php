<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="guardarCategoria">
    <?php
    if (isset($_SESSION['borrado'])):
    ?>
        <h2>Categoria borrada con exito</h2>
        <p><a href="<?= BASE_URL ?>categorias/index">Volver</a></p>

    <?php elseif (isset($_SESSION['errores']) && $_SESSION['errores'] === 'fallo'): ?>
        <h2>Los datos no se han enviado correctamente</h2>
        <p><a href="<?= BASE_URL ?>categorias/index">Volver</a></p>

    <?php else: ?>

        <!-- Formulario para borrar categoría -->
        <h2>Borrar Categoria</h2>
        <form action="<?= BASE_URL ?>categorias/borrar" method="POST">
            <label for="categoriaSeleccionada">Categoria:</label>
            <select name="categoriaSeleccionada" id="categoriaSeleccionada">
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <!-- Campo oculto para enviar los datos que espera el controlador -->
            <input type="hidden" name="categorias[nombre]" value="">

            <?php if (isset($errores['id'])): ?>
                <p style="color:red;"><?php echo $errores['id']; ?></p>
            <?php endif; ?>

            <?php if (isset($errores['db'])): ?>
                <p style="color:red;"><?php echo $errores['db']; ?></p>
            <?php endif; ?>

            <input type="submit" value="Borrar">

            <p><a href="<?= BASE_URL ?>/categorias/index">Volver atras</a></p>
        </form>

    <?php
    endif;
    ?>
</div>