<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<div id="product">

<?php if (isset($_SESSION['pedido'])): ?>
    <h2>Pedido realizado</h2>
    <?php unset($_SESSION['pedido']); ?>
<?php else: ?>

    <h2>Realizar pedido</h2>
    <form action="<?= BASE_URL ?>pedido/guardarPedido" method="POST">

        <label for="provincia">Provincia:</label>
        <input type="text" name="provincia" id="provincia" value="<?= htmlspecialchars($_POST['provincia'] ?? '') ?>">
        <?php if (isset($errores['provincia'])): ?>
            <p style="color:red;"><?= htmlspecialchars($errores['provincia']) ?></p>
        <?php endif; ?>

        <label for="localidad">Localidad:</label>
        <input type="text" name="localidad" id="localidad" value="<?= htmlspecialchars($_POST['localidad'] ?? '') ?>">
        <?php if (isset($errores['localidad'])): ?>
            <p style="color:red;"><?= htmlspecialchars($errores['localidad']) ?></p>
        <?php endif; ?>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
        <?php if (isset($errores['direccion'])): ?>
            <p style="color:red;"><?= htmlspecialchars($errores['direccion']) ?></p>
        <?php endif; ?>

        <?php if (isset($errores['db'])): ?>
            <p style="color:red;"><?= htmlspecialchars($errores['db']) ?></p>
        <?php endif; ?>

        <input type="submit" value="Realizar pedido">

        <p><a href="<?= BASE_URL ?>">Volver a inicio</a></p>
    </form>

<?php endif; ?>

</div>