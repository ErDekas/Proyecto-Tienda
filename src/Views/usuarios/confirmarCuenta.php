<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="confirmarCuenta">
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

<style>
.mensaje-container {
    max-width: 600px;
    margin: 50px auto;
    text-align: center;
}

.mensaje-exito,
.mensaje-error,
.mensaje-info {
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.mensaje-exito {
    background-color: #d4edda;
    color: #155724;
}

.mensaje-error {
    background-color: #f8d7da;
    color: #721c24;
}

.mensaje-info {
    background-color: #e2e3e5;
    color: #383d41;
}

.boton {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 15px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.boton:hover {
    background-color: #0056b3;
}
</style>