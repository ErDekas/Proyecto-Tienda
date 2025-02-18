<div id="inicioSesion">
    <?php
    if (isset($_SESSION['cambio'])):
    ?>
        <h2>Se ha enviado el correo para cambiar la contraseña</h2>
        <?php unset($_SESSION['cambio']) ?>

    <?php elseif (isset($_SESSION['falloDatos'])): ?>

        <h2>Los datos no se han enviado correctamente</h2>
        <?php //unset($_SESSION['falloDatos']);
        ?>
        <p><a href="<?= BASE_URL ?>usuarios/recuperar">Volver</a></p>


    <?php else: ?>
        <h2>Recuperar contraseña</h2>
        <form action="<?= BASE_URL ?>usuarios/recuperar" method="POST">
            <label for="correo">Correo Electrónico:</label>
            <!-- Si hay errores se muestran debajo y se guarda el valor si el campo es correcto -->
            <input type="email" name="correo" id="correo" value="<?php echo $_POST['correo'] ?? ''; ?>"><br><br>
            <?php if (isset($errores['correo'])): ?>
                <p style="color:red;"><?php echo $errores['correo']; ?></p>
            <?php endif; ?>

            <?php if (isset($errores['db'])): ?>
                <p style="color:red;"><?php echo $errores['db']; ?></p>
            <?php endif; ?>

            <input type="submit" value="Recuperar Contraseña">

            <p><a href="<?php echo BASE_URL; ?>">Volver a inicio</a></p>


        </form>

    <?php
    endif;
    ?>

</div>