<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="product">
    <?php
    if (isset($_SESSION['borrado'])):
    ?>
        <h2>Producto borrado con exito</h2>
        <p><a href="<?= BASE_URL ?>productos/index">Volver</a></p>

    <?php elseif (isset($_SESSION['errores']) && $_SESSION['errores'] === 'fallo'): ?>
        <h2>Los datos no se han enviado correctamente</h2>
        <p><a href="<?= BASE_URL ?>productos/index">Volver</a></p>

    <?php else: ?>

        <!-- Formulario para borrar categoría -->
        <h2>Borrar producto</h2>
        <form action="<?= BASE_URL ?>productos/borrar" method="POST">
            <label for="productoSeleccionado">producto:</label>
            <select name="productoSeleccionado" id="productoSeleccionado">
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= htmlspecialchars($producto["id"]) ?>"><?= htmlspecialchars($producto["nombre"]) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <!-- Campo oculto para enviar los datos que espera el controlador -->
            <input type="hidden" name="productos[nombre]" value="">

            <?php if (isset($errores['id'])): ?>
                <p style="color:red;"><?php echo $errores['id']; ?></p>
            <?php endif; ?>

            <?php if (isset($errores['db'])): ?>
                <p style="color:red;"><?php echo $errores['db']; ?></p>
            <?php endif; ?>

            <input type="submit" value="Borrar">

            <p><a href="<?= BASE_URL ?>/productos/index">Volver atras</a></p>
        </form>

    <?php
    endif;
    ?>
</div>