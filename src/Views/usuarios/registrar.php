<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="registrar">

    <?php
    if (isset($_SESSION['registrado'])):
    ?>
        <h2>Usuario registardo con exito</h2>
    <?php elseif (isset($_SESSION['falloDatos'])): ?>

        <h2>Los datos no se han enviado correctamente</h2>
        <p><a href="<?= BASE_URL ?>usuarios/registrar">Volver</a></p>

    <?php else: ?>

        <!-- Formulario para registrarse -->
        <h2>Formulario de Registro</h2>
        <form action="<?= BASE_URL ?>usuarios/registrar" method="POST">
            <label for="nombre">Nombre :</label>
            <!-- Si el campo es correcto guarda el valor sino lo es muestra debajo un error  -->
            <input type="text" name="data[nombre]" id="nombre" value="<?= (isset($user)) ? $user->getNombre() : "" ?>"><br><br>
            <?php if (isset($errores['nombre'])): ?>
                <p style="color:red;"><?php echo $errores['nombre']; ?></p>
            <?php endif; ?>

            <label for="apellidos">Apellidos:</label>
            <input type="text" name="data[apellidos]" id="apellidos" value="<?= (isset($user)) ? $user->getApellidos() : "" ?>"><br><br>
            <?php if (isset($errores['apellidos'])): ?>
                <p style="color:red;"><?php echo $errores['apellidos']; ?></p>
            <?php endif; ?>

            <label for="email">Correo Electrónico:</label>
            <input type="text" name="data[email]" id="email" value="<?= (isset($user)) ? $user->getCorreo() : "" ?>"><br><br>
            <?php if (isset($errores['email'])): ?>
                <p style="color:red;"><?php echo $errores['email']; ?></p>
            <?php endif; ?>


            <label for="password">Contraseña:</label>
            <input type="password" name="data[password]" id="password"><br><br>
            <?php if (isset($errores['password'])): ?>
                <p style="color:red;"><?php echo $errores['password']; ?></p>
            <?php endif; ?>

            <label for="confirmar_password">Confirmar Contraseña:</label>
            <input type="password" name="data[confirmar_password]" id="confirmar_password"><br><br>
            <?php if (isset($errores['confirmar_password'])): ?>
                <p style="color:red;"><?php echo $errores['confirmar_password']; ?></p>
            <?php endif; ?>


            <?php if (isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === "admin"): ?>
                <label for="rol">Rol:</label>
                <input type="text" name="data[rol]" id="rol" value="<?= (isset($user)) ? $user->getRol() : "" ?>"><br><br>
                <?php if (isset($errores['rol'])): ?>
                    <p style="color:red;"><?php echo $errores['rol']; ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <input type="submit" value="Registrar">


            <p>Si ya tienes una cuenta creada <a href="<?= BASE_URL ?>usuarios/iniciarSesion">Inicia Sesión</a></p>


            <p><a href="<?php echo BASE_URL; ?>">Volver a inicio</a></p>
        </form>
    <?php
    endif;
    ?>

</div>