<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="inicioSesion">
    <h2>Formulario de Inicio de Sesión</h2>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <p class="mensaje-exito"><?php echo $_SESSION['mensaje']; ?></p>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>usuarios/iniciarSesion" method="POST">
        <label for="correo">Correo Electrónico:</label>
        <input type="email" name="correo" id="correo" value="<?php echo $_POST['correo'] ?? ''; ?>"><br><br>
        <?php if (isset($errores['correo'])): ?>
            <p class="error"><?php echo $errores['correo']; ?></p>
        <?php endif; ?>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password"><br><br>
        <?php if (isset($errores['password'])): ?>
            <p class="error"><?php echo $errores['password']; ?></p>
        <?php endif; ?>

        <?php if (isset($errores['login'])): ?>
            <p class="error"><?php echo $errores['login']; ?></p>
        <?php endif; ?>

        <input type="submit" value="Iniciar Sesión">

        <p>¿No tienes una cuenta? <a href="<?= BASE_URL ?>usuarios/registrar">Regístrate</a></p>
        <p><a href="<?php echo BASE_URL; ?>">Volver a inicio</a></p>
    </form>
</div>

<style>
.mensaje-exito {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.mensaje-error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.error {
    color: red;
    margin-top: -10px;
    margin-bottom: 10px;
}
</style>