<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div id="product">

<?php 
    if(isset($_SESSION['actualizado'])): 
?>
<h2>Producto actualizado con exito</h2>
<?php unset($_SESSION['actualizado']) ?>
<?php else: ?>

<h2>Actualizar producto</h2>
<form action="<?= BASE_URL ?>productos/actualizar/<?= $product[0]['id'] ?>" method="POSt" enctype="multipart/form-data">

    <input type="hidden" name="_method" value="PUT">

    <label for="nombre">Categoria :</label>
    <!-- Si el campo es correcto guarda el valor sino lo es muestra debajo un error  -->
    <select name="categoria" id="categoria">
        <?php foreach($categorias as $categoria): ?>
            <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
        <?php endforeach;?>
    </select><br><br>

    <label for="nombre">Nombre: </label>
    <input type="text" name="nombre" id="nombre" value="<?= isset($_POST['nombre']) ? $_POST['nombre'] : $product[0]['nombre'] ?>"><br><br>
    <?php if (isset($errores['nombre'])): ?>
        <p style="color:red;"><?php echo $errores['nombre']; ?></p>
    <?php endif; ?>

    <label for="descripcion">Descripción: </label>
    <textarea name="descripcion" id="descripcion"><?= isset($_POST['descripcion']) ? $_POST['descripcion'] : $product[0]['descripcion'] ?></textarea><br><br>
    <?php if (isset($errores['descripcion'])): ?>
        <p style="color:red;"><?php echo $errores['descripcion']; ?></p>
    <?php endif; ?>

    <label for="precio">Precio: </label>
    <input type="number" name="precio" id="precio" value="<?= isset($_POST['precio']) ? $_POST['precio'] : $product[0]['precio'] ?>"><br><br>
    <?php if (isset($errores['precio'])): ?>
        <p style="color:red;"><?php echo $errores['precio']; ?></p>
    <?php endif; ?>

    <label for="stock">Stock: </label>
    <input type="number" name="stock" id="stock" value="<?= isset($_POST['stock']) ? $_POST['stock'] : $product[0]['stock'] ?>"><br><br>
    <?php if (isset($errores['stock'])): ?>
        <p style="color:red;"><?php echo $errores['stock']; ?></p>
    <?php endif; ?>

    <label for="oferta">Oferta: </label>
    <input type="text" name="oferta" id="oferta" value="<?= isset($_POST['oferta']) ? $_POST['oferta'] : $product[0]['oferta'] ?>"><br><br>
    <?php if (isset($errores['oferta'])): ?>
        <p style="color:red;"><?php echo $errores['oferta']; ?></p>
    <?php endif; ?>

    <label for="imagen">Actualizar imagen: </label>
    <input type="file" name="imagen" id="imagen" accept="image/*"><br><br>
    <?php if (isset($errores['imagen'])): ?>
        <p style="color:red;"><?php echo $errores['imagen']; ?></p>
    <?php endif; ?>

    <?php if (isset($errores['db'])): ?>
        <p style="color:red;"><?php echo $errores['db']; ?></p>
    <?php endif; ?>

    

    <input type="submit" value="Actualizar Producto">

    

    <p><a href="<?= BASE_URL ?>productos/productoInfo/<?= $product[0]['id'] ?>">Volver atras</a></p>
</form>
<?php 
    endif;
?>

</div>