<!-- ESTA VISTA AYUDA A RENDERIZAR LOS COMENTARIOS QUE LOS USUARIOS HACEN EN LAS PUBLICACIONES DE AUDIO -->

<div class="separador-comentarios-del-audio mt-2"></div>

<div class="container-contenido-comentarios-audio d-flex" id="comentario-<?php echo $comentarioReciente->idcomentario ?>">

    <?php
    $usuario = $comentarioReciente->usuario;
    $fotoPerfil = $comentarioReciente->fotoPerfil;
    $clasesImg = 'image-border mr-2';
    $clasesNombre = 'big mr-2';
    include URL_APP . '/views/Parciales/perfil-usuario.php';
    ?>

    <div class="contenido-comentario-usuario-audio flex-grow-1">

        <div class="d-flex justify-content-between align-items-center">

            <span class="fecha-comentario"><?php echo date('d M Y H:i', strtotime($comentarioReciente->fechaComentario)) ?></span>

            <?php if ($comentarioReciente->iduser == $_SESSION['logeado']) : ?>

                <a href="<?php echo URL_PROJECT ?>/publicaciones/eliminarComentarioAudio/<?php echo $comentarioReciente->idcomentario ?>" class="btn-eliminar-comentario text-danger" data-id="<?php echo $comentarioReciente->idcomentario ?>">
                    
                <i class="far fa-trash-alt"></i>

                </a>

            <?php endif ?>

        </div>

        <p><?php echo nl2br(htmlspecialchars($comentarioReciente->contenidoComentario, ENT_QUOTES, 'UTF-8')); ?></p>
    
    </div>

</div>