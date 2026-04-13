<?php

include_once URL_APP . '/views/custom/header.php';

?>

<div class="contenedor-centro2 centro">
    <div class="formulario">
        <h1>Registro</h2>
        <form action="<?php echo URL_PROJECT ?>/home/register" method="POST">
            <div class="input-contenedor2">
                <input type="email" name="email" required>
                <label for="correo">Correo Electrónico:</label>
            </div>

            <div class="input-contenedor2">
                <input type="text" name="usuario" required>
                <label for="nombre">Nombre:</label>
            </div>

            <div class="input-contenedor2">
                <input type="password" name="contrasena" required>
                <label for="#">Contraseña:</label>
            </div>

            <button class="boton2" type="submit" value="Registrar">Registrarme</button>
        </form>
        
        <?php if (isset($_SESSION['usuarioError'])) : ?>

            <div class="alert alert-danger alert-dismissible fade show mt-2 mb-2" role="alert">
                <?php echo$_SESSION['usuarioError'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        <?php unset($_SESSION['usuarioError']); endif ?>

        <div class="iniciar">
            <p class="iniciar-sesion">¿Ya tienes una cuenta? <a href="<?php echo URL_PROJECT ?>/home/login">Iniciar sesión</a>
            </p>
        </div>



    </div>

</div>

<?php

include_once URL_APP . '/views/custom/footer.php';

?>