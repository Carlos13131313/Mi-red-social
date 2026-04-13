<?php

include_once URL_APP . '/views/custom/header.php';

include_once URL_APP . '/views/custom/navbar.php';

//echo "<pre>";
//var_dump($datos['usuario']);
//echo "</pre>";

?>

<div class="perfil-container-usuario">

    <!-- Imagen de portada en el perfil del usuario -->
    <div class="imagen-header-perfil-usuario">
        <img src="<?php echo URL_PROJECT ?>/img/imagenesPerfil/imagenes-portada-perfil/cover.jpg" class="imagen-portada-perfil" alt="">
    </div>

    <div class="container-perfil">
        <div class="conteiner-perfil row">
            <!-- Columna perfil -->
            <!-- datos-perfil-usuario -->
            <div class="col-md-4">

                <img src="<?php echo URL_PROJECT ?>/<?php echo $datos['perfil']->fotoPerfil ?>" class="imagen-perfil-usuario" alt="">

                <?php if ($datos['usuario']->idusuario == $_SESSION['logeado']) : ?>

                    <form class="fotos" action="<?php echo URL_PROJECT ?>/perfil/cambiarimagen" method="POST" enctype="multipart/form-data">
                        <i class="fas fa-camera"></i>

                        <div class="input-file">
                            <input type="hidden" name="id_user" value="<?php echo $_SESSION['logeado'] ?>">
                            <input type="file" name="imagen" id="imagen">
                        </div>

                        <div class="editar-perfil">
                            <button class="btn-change-image">Editar</button>
                        </div>

                    </form>


                <?php endif ?>

                <div class="photo-separation"></div>

                <div class="datos-personales-usuario">

                    <h3><?php echo ucwords($datos['usuario']->usuario) ?></h3>

                    <div class="descripcion-usuario">

                        <span><?php echo $datos['perfil']->nombreCompleto ?></span>

                    </div>
                </div>
            </div>


            <div class="col-md-5">

                <!-- contenedor para hacer publicaciones -->
                <div class="container-style-main-perfil">

                    <?php if ($datos['usuario']->idusuario == $_SESSION['logeado']) : ?>

                        <div class="container-usuario-publicar-perfil">

                            <!-- imagen de perfil -->
                            <a href="<?php echo URL_PROJECT ?>/perfil/<?php echo $datos['usuario']->usuario ?>"><img src="<?php echo URL_PROJECT . '/' .
                            $datos['perfil']->fotoPerfil ?>" class="image-border-perfil" alt=""></a>

                            <form action="" class="form-publicar-perfil">

                                <textarea name="" id="" class="published-perfil" name="post" placeholder="¿Que estas pensando?" required></textarea>

                                <!-- Botones para subir y publicar -->
                                <div class="image-upload-file">

                                    <img src="<?php echo URL_PROJECT ?>/img/image.png" alt="" class="image-public-perfil">

                                    <span class="btn-subirFoto-perfil">Subir foto</span>

                                    <div class="input-file">
                                        <input type="file" name="imagen" id="imagen">
                                    </div>

                                    <button class="btn-publi3">Publicar</button>

                                </div>

                            </form>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            

        </div>
    </div>

</div>

<!-- Script que permite accionar el input del boton de subir foto -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btnSubirFoto = document.querySelector('.btn-subirFoto-perfil');
        var inputImagen = document.getElementById('imagen');

        btnSubirFoto.addEventListener('click', function() {
            inputImagen.click();
        });
    });
</script>

<!-- Script que permite accionar el input del boton de <i class="fas fa-camera"></i> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var cameraIcon = document.querySelector('.fa-camera');
        var inputImagen = document.querySelector('.input-file input[type="file"]');

        cameraIcon.addEventListener('click', function() {
            inputImagen.click();
        });
    });
</script>


<?php
include_once URL_APP . '/views/custom/footer.php';

?>