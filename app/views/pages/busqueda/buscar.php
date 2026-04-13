<?php

include_once URL_APP . '/views/custom/header.php';

include_once URL_APP . '/views/custom/navbar.php';

?>

<div class="container mt-3">
    <div class="container notificaciones-usuarios">
        <h4>Resultados de la busqueda</h4>
        <hr>

        <div class="lista-de-usuarios-registrados">
            <?php foreach ($datos['resultado'] as $usuariosRegistrados) : ?>
                <div class="elemento-usuario-registrado">
                    <img src="<?php echo URL_PROJECT . '/' . $usuariosRegistrados->fotoPerfil ?>" alt="image-big-user mr-2">
                    <div class="nombre-usuario-registrado">
                        <?php echo $usuariosRegistrados->nombreCompleto ?> - <a href="<?php echo URL_PROJECT ?>/perfil/<?php echo $usuariosRegistrados->usuarios ?>"><?php
                        echo $usuariosRegistrados->usuario ?></a>
                    </div>
                </div>
                <hr>
            <?php endforeach ?>
        </div>
    </div>
</div>

<?php

include_once URL_APP . '/views/custom/footer.php';

?>