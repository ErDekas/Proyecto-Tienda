<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div class="guardarCategoria">
<?php 
    if(isset($_SESSION['actualizado'])): 
?>
<h2>Pedido actualizada con exito</h2>
<p><a href="<?= BASE_URL ?>pedido/verTodos">Volver</a></p>
<?php unset($_SESSION['actualizado']) ?>

<?php elseif(isset($_SESSION['falloDatos'])): ?>

<h2>Los datos no se han enviado correctamente</h2>
<p><a href="<?= BASE_URL ?>pedido/verTodos">Volver</a></p>
<?php unset($_SESSION['falloDatos']) ?>
<?php else: ?>

<!-- Formulario para registrarse -->
<h2>Actualizar Pedido</h2>
<form action="<?= BASE_URL ?>pedido/verEstadoPedido/<?= $idPedido ?>" method="POST">

    <input type="hidden" name="idPedido" value="<?= $idPedido ?>">

    <label for="nombre">Pedido :</label>
    <!-- Si el campo es correcto guarda el valor sino lo es muestra debajo un error  -->
    <select name="orderState" id="categoriaSelect">
            <option value="Confirmado">Confirmado</option>
            <option value="Denegado">Denegado</option>
            <option value="Pagado">Pagado</option>
            <option value="Espera">En espera</option>
    </select><br><br>
    <?php if (isset($errores['estado'])): ?>
        <p style="color:red;"><?php echo $errores['estado']; ?></p>
    <?php endif; ?>

    <?php if (isset($errores['db'])): ?>
        <p style="color:red;"><?php echo $errores['db']; ?></p>
    <?php endif; ?>

    <input type="submit" value="Actualizar">

    <p><a href="<?= BASE_URL ?>pedido/verTodos">Volver atras</a></p>
</form>

<?php 
    endif;
?>

</div>