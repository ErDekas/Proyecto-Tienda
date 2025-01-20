<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="product">

    <?php
    if (isset($_SESSION['guardado'])):
    ?>
        <h2>Producto guardado con exito</h2>

    <?php else: ?>

        <h2>Añadir producto</h2>
        <form action="<?= BASE_URL ?>productos/crear" method="POST" enctype="multipart/form-data">


            <label for="nombre">Categoria :</label>
            <!-- Si el campo es correcto guarda el valor sino lo es muestra debajo un error  -->
            <select name="categoria" id="categoria">
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="nombre">Nombre: </label>
            <input type="text" name="nombre" id="nombre" value="<?= $_POST['nombre'] ?? '' ?>"><br><br>
            <?php if (isset($errores['nombre'])): ?>
                <p style="color:red;"><?php echo $errores['nombre']; ?></p>
            <?php endif; ?>

            <label for="descripcion">Descripción: </label>
            <textarea name="descripcion" id="descripcion"><?php echo isset($_POST['descripcion']) ? $_POST['descripcion'] : ''; ?></textarea><br><br>
            <?php if (isset($errores['descripcion'])): ?>
                <p style="color:red;"><?php echo $errores['descripcion']; ?></p>
            <?php endif; ?>

            <label for="precio">Precio: </label>
            <input type="number" name="precio" id="precio" value="<?= $_POST['precio'] ?? '' ?>"><br><br>
            <?php if (isset($errores['precio'])): ?>
                <p style="color:red;"><?php echo $errores['precio']; ?></p>
            <?php endif; ?>

            <label for="stock">Stock: </label>
            <input type="number" name="stock" id="stock" value="<?= $_POST['stock'] ?? '' ?>"><br><br>
            <?php if (isset($errores['stock'])): ?>
                <p style="color:red;"><?php echo $errores['stock']; ?></p>
            <?php endif; ?>

            <label for="oferta">Oferta: </label>
            <input type="text" name="oferta" id="oferta" value="<?= $_POST['oferta'] ?? '' ?>"><br><br>
            <?php if (isset($errores['oferta'])): ?>
                <p style="color:red;"><?php echo $errores['oferta']; ?></p>
            <?php endif; ?>

            <input type="hidden" name="fecha" value="<?= date("Y-m-d") ?>">
            <?php if (isset($errores['fecha'])): ?>
                <p style="color:red;"><?php echo $errores['fecha']; ?></p>
            <?php endif; ?>

            <label for="imagen">Subir imagen: </label>
            <input type="file" name="imagen" id="imagen" accept="image/*"><br><br>
            <?php if (isset($errores['imagen'])): ?>
                <p style="color:red;"><?php echo $errores['imagen']; ?></p>
            <?php endif; ?>

            <?php if (isset($errores['db'])): ?>
                <p style="color:red;"><?php echo $errores['db']; ?></p>
            <?php endif; ?>



            <input type="submit" value="Añadir Producto">


            <p><a href="<?= BASE_URL ?>productos/index">Volver atras</a></p>
        </form>
    <?php
    endif;
    ?>

</div>