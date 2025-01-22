<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="registrar">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje-exito">
            <h2><?php echo $_SESSION['mensaje']; ?></h2>
            <p>Por favor, revisa tu correo electrónico para confirmar tu cuenta.</p>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php elseif (isset($_SESSION['falloDatos'])): ?>
        <div class="mensaje-error">
            <h2>Los datos no se han enviado correctamente</h2>
            <p><a href="<?= BASE_URL ?>usuarios/registrar">Volver a intentar</a></p>
        </div>
        <?php unset($_SESSION['falloDatos']); ?>
    <?php else: ?>
        <h2>Formulario de Registro</h2>
        <form action="<?= BASE_URL ?>usuarios/registrar" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="data[nombre]" id="nombre" 
                   value="<?= (isset($user)) ? $user->getNombre() : "" ?>"><br><br>
            <?php if (isset($errores['nombre'])): ?>
                <p class="error"><?php echo $errores['nombre']; ?></p>
            <?php endif; ?>

            <label for="apellidos">Apellidos:</label>
            <input type="text" name="data[apellidos]" id="apellidos" 
                   value="<?= (isset($user)) ? $user->getApellidos() : "" ?>"><br><br>
            <?php if (isset($errores['apellidos'])): ?>
                <p class="error"><?php echo $errores['apellidos']; ?></p>
            <?php endif; ?>

            <label for="email">Correo Electrónico:</label>
            <input type="text" name="data[email]" id="email" 
                   value="<?= (isset($user)) ? $user->getCorreo() : "" ?>"><br><br>
            <?php if (isset($errores['email'])): ?>
                <p class="error"><?php echo $errores['email']; ?></p>
            <?php endif; ?>

            <label for="password">Contraseña:</label>
            <input type="password" name="data[password]" id="password"><br><br>
            <?php if (isset($errores['password'])): ?>
                <p class="error"><?php echo $errores['password']; ?></p>
            <?php endif; ?>

            <label for="confirmar_password">Confirmar Contraseña:</label>
            <input type="password" name="data[confirmar_password]" id="confirmar_password"><br><br>
            <?php if (isset($errores['confirmar_password'])): ?>
                <p class="error"><?php echo $errores['confirmar_password']; ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === "admin"): ?>
                <label for="rol">Rol:</label>
                <input type="text" name="data[rol]" id="rol" 
                       value="<?= (isset($user)) ? $user->getRol() : "" ?>"><br><br>
                <?php if (isset($errores['rol'])): ?>
                    <p class="error"><?php echo $errores['rol']; ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <input type="submit" value="Registrar">

            <p>¿Ya tienes una cuenta? <a href="<?= BASE_URL ?>usuarios/iniciarSesion">Inicia Sesión</a></p>
            <p><a href="<?php echo BASE_URL; ?>">Volver a inicio</a></p>
        </form>
    <?php endif; ?>
</div>

<style>
.mensaje-exito {
    background-color: #d4edda;
    color: #155724;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
}

.mensaje-error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
}

.error {
    color: red;
    margin-top: -10px;
    margin-bottom: 10px;
}

form {
    max-width: 500px;
    margin: 0 auto;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}
</style>