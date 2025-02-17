<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div id="registrar">

<?php 
    if(isset($_SESSION['contraseña'])): 
?>
<h2>Contraseña cambiado con exito</h2>
<?php unset($_SESSION['contraseña'])?>
<?php elseif(isset($_SESSION['falloDatos'])): ?>

<h2>Los datos no se han enviado correctamente</h2>
<p><a href="<?= BASE_URL ?>usuarios/cambiarContra">Volver</a></p>
<?php unset($_SESSION['falloDatos'])?>
<?php else: ?>

<!-- Formulario para registrarse -->
<h2>Formulario de cambio de contraseña</h2>
<form action="<?= BASE_URL ?>usuarios/cambiarContra?token=<?= $token ?>" method="POST">

    <label for="contrasena">Contraseña:</label>
    <input type="password" name="data[contrasena]" id="contrasena"><br><br>
    <?php if (isset($errores['contrasena'])): ?>
        <p style="color:red;"><?php echo $errores['contrasena']; ?></p>
    <?php endif; ?>

    <label for="confirmar_contrasena">Confirmar Contraseña:</label>
    <input type="password" name="data[confirmar_contrasena]" id="confirmar_contrasena"><br><br>
    <?php if (isset($errores['confirmar_contrasena'])): ?>
        <p style="color:red;"><?php echo $errores['confirmar_contrasena']; ?></p>
    <?php endif; ?>

    <input type="submit" value="Cambiar Contrasñea">

    
    <p><a href="<?php echo BASE_URL; ?>">Volver a inicio</a></p>
</form>
<?php 
    endif;
?>

</div>