<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="registrar">
    <div class="mensaje-container">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-exito">
                <h2>¡Cuenta Confirmada!</h2>
                <p><?php echo $_SESSION['mensaje']; ?></p>
                <p><a href="<?= BASE_URL ?>usuarios/iniciarSesion" class="boton">Iniciar Sesión</a></p>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="mensaje-error">
                <h2>Error en la Confirmación</h2>
                <p><?php echo $_SESSION['error']; ?></p>
                <p><a href="<?= BASE_URL ?>usuarios/registrar" class="boton">Volver a Registrarse</a></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php else: ?>
            <div class="mensaje-info">
                <h2>Verificando...</h2>
                <p>Por favor, espere mientras verificamos su cuenta.</p>
            </div>
        <?php endif; ?>

    </div>
</div>