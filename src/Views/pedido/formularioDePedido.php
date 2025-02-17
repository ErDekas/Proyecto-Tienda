<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div id="product">

<?php 
    if(isset($_SESSION['order'])): 
?>
<h2>Pedido realizado</h2>
<?php unset($_SESSION['order'])?>

<?php elseif(isset($errores['payment'])):?>
<h2><?= $errores['payment'] ?></h2>
<?php else: ?>

<h2>Realizar pedido</h2>
<form action="<?= BASE_URL ?>pedido/guardarPedido" method="POST">


    <label for="provincia">Pronvincia: </label>
    <input type="text" name="provincia" id="provincia" value="<?= $_POST['provincia'] ?? '' ?>"><br><br>
    <?php if (isset($errores['provincia'])): ?>
        <p style="color:red;"><?= $errores['provincia'] ?></p>
    <?php endif; ?>

    <label for="localidad">Localidad: </label>
    <input type="text" name="localidad" id="localidad" value="<?= $_POST['localidad'] ?? '' ?>"><br><br>
    <?php if (isset($errores['localidad'])): ?>
        <p style="color:red;"><?= $errores['localidad'] ?></p>
    <?php endif; ?>

    <label for="direccion">Dirección: </label>
    <input type="text" name="direccion" id="direccion" value="<?= $_POST['direccion'] ?? '' ?>"><br><br>
    <?php if (isset($errores['direccion'])): ?>
        <p style="color:red;"><?= $errores['direccion'] ?></p>
    <?php endif; ?>

    <?php if (isset($errores['db'])): ?>
        <p style="color:red;"><?= $errores['db'] ?></p>
    <?php endif; ?>

    

    <input type="submit" value="Realizar pedido">

    

    <p><a href="<?= BASE_URL ?>">Volver a inicio</a></p>
</form>
<?php 
    endif;
?>

</div>