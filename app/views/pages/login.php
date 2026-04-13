<?php

include_once URL_APP . '/views/custom/header.php';

?>

<div class="contenedor-centro centro">
    <div class="formulario">
        <h2>Iniciar sesión</h2>
        <form action="<?php echo URL_PROJECT ?>/home/login" method="POST">
            <div class="input-contenedor">
                <!-- <i class="fa-solid fa-envelope"></i>  PENDIENTE -->
                <input type="text" name="usuario" required>
                <label for="#">Usuario</label>
            </div>

            <div class="input-contenedor">
                <!-- <i class="fa-solid fa-lock"></i> PENDIENTE -->
                <input type="password" name="contrasena" required>
                <label for="#">Contraseña</label>
            </div>
            
            <button class="boton">Iniciar Sesion</button>
        </form>

        <!-- error de login de usuario o contraseña incorrecta -->
        <?php if (isset($_SESSION['errorLogin'])) : ?>

            <div class="alert alert-danger alert-dismissible fade show mt-2 mb-2" role="alert">
                <?php echo $_SESSION['errorLogin'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        <?php unset($_SESSION['errorLogin']);
        endif ?>

        <!-- alerta cuando el registro se completó -->
        <?php if (isset($_SESSION['LoginComplete'])) : ?>

            <div class="alert alert-succes alert-dismissible fade show mt-2 mb-2" role="alert">
                <?php echo $_SESSION['LoginComplete'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        <?php unset($_SESSION['LoginComplete']);
        endif ?>

        <div class="registrar">
            <p class="crear-cuenta"> ¿No tienes una cuenta? <a href="<?php echo URL_PROJECT ?>/home/register">Registrarme</a>
            </p>
        </div>
    </div>

</div>

<?php

include_once URL_APP . '/views/custom/footer.php';

?>