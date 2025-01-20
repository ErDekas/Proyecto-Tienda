<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="guardarCategoria">


    <!-- Formulario para registrarse -->
    <h2>Añadir Categoria</h2>
    <form action="<?= BASE_URL ?>categorias/crear" method="POST">
        <label for="nombre">Nombre :</label>
        <!-- Si el campo es correcto guarda el valor sino lo es muestra debajo un error  -->
        <input type="text" name="categorias[nombre]" id="nombre" value="<?= (isset($categorias)) ? $categorias->getNombre() : "" ?>"><br><br>
        <?php if (isset($errores['nombre'])): ?>
            <p style="color:red;"><?php echo $errores['nombre']; ?></p>
        <?php endif; ?>

        <?php if (isset($errores['db'])): ?>
            <p style="color:red;"><?php echo $errores['db']; ?></p>
        <?php endif; ?>

        <input type="submit" value="Añadir">

        <p><a href="<?= BASE_URL ?>categorias/index">Volver atras</a></p>
    </form>

</div>