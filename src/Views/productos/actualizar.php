<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="product">
    <?php if (isset($_SESSION['actualizado'])): ?>
        <h2>Producto actualizado con éxito</h2>
        <p><a href="<?= BASE_URL ?>productos/index">Volver</a></p>
        <?php unset($_SESSION['actualizado']); ?>
        
    <?php elseif (isset($_SESSION['errores'])): ?>
        <h2>Los datos no se han enviado correctamente</h2>
        <p><a href="<?= BASE_URL ?>productos/index">Volver</a></p>
        
    <?php else: ?>

        <!-- Formulario para actualizar producto -->
        <h2>Actualizar Producto</h2>
        <form action="<?= BASE_URL ?>productos/actualizar" method="POST" enctype="multipart/form-data">

            <!-- Selección del producto -->
            <label for="productoSeleccionado">Producto:</label>
            <select name="productoSeleccionado" id="productoSeleccionado" required>
                <option value="" disabled selected>Selecciona un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= htmlspecialchars($producto["id"]) ?>" 
                        <?= isset($_POST['productoSeleccionado']) && $_POST['productoSeleccionado'] == $producto["id"] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($producto["nombre"]) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="categoria">Nueva categoría:</label>
            <select name="categoria" id="categoria">
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="nombre">Nuevo nombre:</label>
            <input type="text" name="nombre" id="nombre" 
                value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required>
            <?php if (isset($errores['nombre'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['nombre']) ?></p>
            <?php endif; ?>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" required><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
            <?php if (isset($errores['descripcion'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['descripcion']) ?></p>
            <?php endif; ?>

            <label for="precio">Precio:</label>
            <input type="number" name="precio" id="precio" step="0.01"
                value="<?= isset($_POST['precio']) ? htmlspecialchars($_POST['precio']) : '' ?>" required>
            <?php if (isset($errores['precio'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['precio']) ?></p>
            <?php endif; ?>

            <label for="stock">Stock:</label>
            <input type="number" name="stock" id="stock"
                value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '' ?>" required>
            <?php if (isset($errores['stock'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['stock']) ?></p>
            <?php endif; ?>

            <label for="oferta">Oferta:</label>
            <input type="text" name="oferta" id="oferta"
                value="<?= isset($_POST['oferta']) ? htmlspecialchars($_POST['oferta']) : '' ?>">
            <?php if (isset($errores['oferta'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['oferta']) ?></p>
            <?php endif; ?>

            <label for="imagen">Subir nueva imagen:</label>
            <input type="file" name="imagen" id="imagen" accept="image/*">
            <?php if (isset($errores['imagen'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['imagen']) ?></p>
            <?php endif; ?>

            <input type="hidden" name="fecha" value="<?= date("Y-m-d") ?>">
            <?php if (isset($errores['fecha'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errores['fecha']) ?></p>
            <?php endif; ?>

            <input type="submit" value="Actualizar Producto">

            <p><a href="<?= BASE_URL ?>productos/index">Volver atrás</a></p>
        </form>

    <?php endif; ?>
</div>
