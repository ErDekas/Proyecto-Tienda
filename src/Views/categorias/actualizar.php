<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="guardarCategoria">
    <?php
    if (isset($_SESSION['actualizado'])):
    ?>
        <h2>Categoria actualizada con exito</h2>
        <p><a href="<?= BASE_URL ?>categorias/index">Volver</a></p>
    <?php elseif (isset($_SESSION['errores'])): ?>

        <h2>Los datos no se han enviado correctamente</h2>
        <p><a href="<?= BASE_URL ?>categorias/index">Volver</a></p>

    <?php else: ?>

        <!-- Formulario para registrarse -->
        <h2>Actualizar Categoria</h2>
        <form action="<?= BASE_URL ?>categorias/actualizar" method="POST">

            <label for="nombre">Categoria :</label>
            <!-- Si el campo es correcto guarda el valor sino lo es muestra debajo un error  -->
            <select name="categoriaSeleccionada" id="categoriaSeleccionada">
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="nombre">Nuevo nombre:</label>
            <input type="text"
                name="categorias[nombre]"
                id="nombre"
                value="<?= isset($_POST['categorias']['nombre']) ? htmlspecialchars($_POST['categorias']['nombre']) : '' ?>">

            <?php if (isset($errores['nombre'])): ?>
                <p style="color:red;"><?php echo htmlspecialchars($errores['nombre']); ?></p>
            <?php endif; ?>

            <?php if (isset($errores['db'])): ?>
                <p style="color:red;"><?php echo htmlspecialchars($errores['db']); ?></p>
            <?php endif; ?>

            <input type="submit" value="Actualizar">

            <p><a href="<?= BASE_URL ?>categorias/index">Volver atras</a></p>
        </form>

    <?php
    endif;
    ?>
</div>