<?php

include_once URL_APP . '/views/custom/header.php';

include_once URL_APP . '/views/custom/navbar.php';

//////////////////////////////////// Aqui mezcla los dos tipos de publicaciones Audio y Normales /////////////////////////////////

foreach ($datos['audios'] as $audio) {
    $audio->tipo = 'audio';
    $publicacionesCombinadas[] = $audio;
}

foreach ($datos['publicaciones'] as $publicacion) {
    $publicacion->tipo = 'normal';
    $publicacionesCombinadas[] = $publicacion;
}

usort($publicacionesCombinadas, function ($a, $b) {
    return strtotime($b->fechaPublicacion) - strtotime($a->fechaPublicacion);
});

$datos['publicacionesCombinadas'] = $publicacionesCombinadas;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Ordenar publicaciones normales por fecha (más nuevas arriba)
usort($datos['publicaciones'], function ($a, $b) {
    return strtotime($b->fechaPublicacion) - strtotime($a->fechaPublicacion);
});

// Esto debería hacerse una sola vez, al inicio de la vista (Para los likes).
$likesMap = [];
foreach ($datos['misLikes'] as $like) {
    $likesMap[$like->idPublicacion] = true;
}

///////////////////Para agrupar los comentarios de las publicaciones normales.//////////////////////////////////

// Para mejorar el manejo de los comentarios.
$comentariosAgrupados = [];
foreach ($datos['comentarios'] as $comentario) {
    $comentariosAgrupados[$comentario->idPublicacion][] = $comentario;
}

// Ordenar comentarios más recientes arriba para cada publicación.
foreach ($comentariosAgrupados as &$comentarios) {
    usort($comentarios, function ($a, $b) {
        return strtotime($b->fechaComentario) - strtotime($a->fechaComentario);
    });
}
unset($comentarios); // Evitar referencia accidental más adelante


///////////////////Para agrupar los comentarios de las publicaciones de Audio.//////////////////////////////////

// Para mejorar el manejo de los comentarios.
$comentariosAgrupadosAudio = [];
foreach ($datos['comentarioAudio'] as $comentario) {
    $comentariosAgrupadosAudio[$comentario->idPublicacion][] = $comentario;
}

// Ordenar comentarios más recientes arriba para cada publicación.
foreach ($comentariosAgrupadosAudio as &$comentarios) {
    usort($comentarios, function ($a, $b) {
        return strtotime($b->fechaComentario) - strtotime($a->fechaComentario);
    });
}
unset($comentarios); // Evitar referencia accidental más adelante

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//echo "<pre>";
//var_dump($datos['misNotificaciones']);
//echo "</pre>";

?>

<!-- /////////////////////////////////////////// AQUÍ COMIENZA EL HTML /////////////////////////////////////////// -->

<div class="container5">

    <div class="columna-home">

        <!-- Columna perfil Izquierda -->
        <div class="col-md-3">
            <div class="container-style-main2">
                <div class="perfil-usuario-main">

                    <!-- imagen de perfil -->
                    <a href="<?php echo URL_PROJECT ?>/perfil/<?php echo $datos['usuario']->usuario ?>">

                        <img src="<?php echo URL_PROJECT . '/' . $datos['perfil']->fotoPerfil ?>" class="image-border-usuario" alt="">

                    </a>

                    <div class="photo-separation"></div>

                    <!-- nombre de usuario que redirije a la pagina de Perfil -->
                    <a href="<?php echo URL_PROJECT ?>/perfil/<?php echo $datos['usuario']->usuario ?>" class="perfil-link">
                        <div class="text-center nombre-perfil"><?php echo $datos['perfil']->nombreCompleto ?></div>
                    </a>

                    <div class="photo-separation"></div>

                    <!-- Seccion donde muestra los likes y los comentarios -->
                    <div class="tabla-estadisticas">
                        <a href="#">Publicaciones <br> 0</a>
                        <a href="#">Me gustas<br> 0</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Timeline principal Centro -->
        <div class="container">
            <!-- contenedor para hacer publicaciones -->
            <div class="container-style-main-home">

                <div class="container-usuario-publicar">

                    <!-- imagen de perfil -->
                    <!-- <a href="<?php echo URL_PROJECT ?>/perfil/<?php echo $datos['usuario']->usuario ?>"></a> -->
                    <img src="<?php echo URL_PROJECT . '/' . $datos['perfil']->fotoPerfil ?>" class="image-border" alt="">


                    <form action="<?php echo URL_PROJECT ?>/publicaciones/publicar/<?php echo $datos['usuario']->idusuario ?>" method="POST" enctype="multipart/form-data" class="form-publicar">
                        <textarea name="contenido" id="contenido" class="published" name="post" placeholder="¿Qué estás pensando?"></textarea>

                        <!-- Botones para subir y publicar -->
                        <div class="image-upload-file">
                            <img src="<?php echo URL_PROJECT ?>/img/image.png" alt="" class="image-public">
                            <span class="btn-subirFoto">Subir foto</span>

                            <div class="input-file">
                                <input type="file" name="imagen" id="imagen">
                            </div>

                            <!-- script que habilita el boton Publicar -->
                            <button class="btn-publi" id="btn-publi" disabled>Publicar</button>
                        </div>
                    </form>

                </div>

            </div>








            <!-- ///////////////// ARREGLAR LA ELIMINACION DE COMENTARIOS DE LOS AUDIOS... SE DA LA OPCION DE ELIMINAR PERO NO LAS ELIMINA ///////////////// -->





            <?php foreach ($datos['publicacionesCombinadas'] as $datosPublicacion): ?>

                <!-- Publicaciones de los usuarios -->

                <?php if ($datosPublicacion->tipo === 'audio'): ?>

                    <!-- Publicaciones de audio -->
                    <div class="container-usuarios-publicaciones-audio">

                        <!-- Boton de eliminar si el usuario es el propietario -->
                        <?php if ($datosPublicacion->idusuario == $_SESSION['logeado']) : ?>
                            <div class="acciones-publicacion-usuario-audio">
                                <a href="<?php echo URL_PROJECT ?>/publicaciones/eliminarPublicacionAudio/<?php echo $datosPublicacion->idpublicacion ?>">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </div>
                        <?php endif ?>

                        <!-- Aqui muestra la informacion de usuario de quien publico -->
                        <div class="usuarios-publicaciones-audio-top">
                            <?php
                            $usuario = $datosPublicacion->usuario;
                            $fotoPerfil = $datosPublicacion->fotoPerfil;
                            $clasesImg = 'image-border-pub';
                            $clasesNombre = '';
                            ob_start(); // Captura la salida del nombre para colocarla dentro del h6
                            include URL_APP . '/views/Parciales/perfil-usuario.php';
                            $perfilHtml = ob_get_clean();
                            echo $perfilHtml;
                            ?>
                            <div class="informacion-usuario-audio-publico">
                                <!-- Ya se imprimió el nombre de usuario arriba -->
                                <span><?php echo $datosPublicacion->fechaPublicacion ?></span>
                            </div>
                        </div>

                        <!-- Muestra la publicacion de audio -->
                        <div class="contenido-publicacion-audio-usuario">
                            <audio controls>
                                <source src="data:audio/mpeg;base64,<?php echo base64_encode($datosPublicacion->audioPublicacion); ?>" type="audio/mpeg">
                                Tu navegador no soporta la reproducción de audio.
                            </audio>
                        </div>

                        <hr> <!---------------------------------------------- barra separadora ------------------------------------------------>

                        <!-- Aqui se muestran los likes y se pueden dar like a la publicacion -->
                        <div class="acciones-audio-usuario-publicar mt-2">

                            <?php $likeClass = isset($likesMap[$datosPublicacion->idpublicacion]) ? 'like-active' : ''; ?>

                            <a href="#" class="btn-like-audio <?php echo $likeClass; ?>"
                                data-idpublicacion="<?php echo $datosPublicacion->idpublicacion; ?>"
                                data-idusuario="<?php echo $_SESSION['logeado']; ?>"
                                data-idpropietario="<?php echo $datosPublicacion->idusuario; ?>">
                                <i class="fas fa-check"></i> Me gusta <span class="like-count"><?php echo $datosPublicacion->num_likes ?></span>
                            </a>

                        </div>

                        <hr> <!---------------------------------------------- barra separadora ------------------------------------------------>

                        <!-- Campo para comentar -->
                        <div class="formulario-comentarios-audio">

                            <!-- Aqui muestra la informacion de usuario actual -->

                            <img src="<?php echo URL_PROJECT . '/' . $datos['perfil']->fotoPerfil ?>" class="image-border" alt="">

                            <div class="acciones-formulario-comentario-audio">

                                <form class="form-comentar-audio">
                                    <input type="hidden" name="iduserPropietario" value="<?php echo $datosPublicacion->idusuario ?>">
                                    <input type="hidden" name="iduser" value="<?php echo $datos['usuario']->idusuario ?>">
                                    <input type="hidden" name="idpublicacion" value="<?php echo $datosPublicacion->idpublicacion ?>">
                                    <input type="text" name="comentarios" class="form-comentario-usuario" placeholder="Agregar comentario" required>

                                    <div class="btn-comentario-container-audio" style="float: right;">

                                        <button type="button" class="btn-purple" id="btn-comentar-audio">Comentar</button>

                                    </div>
                                </form>

                            </div>

                        </div>

                        <!-- Comentarios -->

                        <div id="comentarios-audio-<?php echo $datosPublicacion->idpublicacion ?>">

                            <?php foreach ($comentariosAgrupadosAudio[$datosPublicacion->idpublicacion] ?? [] as $datosComentarios): ?>

                                <!-- Separador de comentarios -->
                                <div class="separador-comentarios-del-audio mt-2"></div>

                                <!-- Comentarios de los usuarios -->
                                <div class="container-contenido-comentarios-audio">

                                    <!-- Aqui muestra la informacion del usuario que comento -->
                                    <?php
                                    $usuario = $datosComentarios->usuario;
                                    $fotoPerfil = $datosComentarios->fotoPerfil;
                                    $clasesImg = 'image-border mr-2';
                                    $clasesNombre = 'big mr-2';
                                    include URL_APP . '/views/Parciales/perfil-usuario.php';
                                    ?>

                                    <!-- Aqui se muestran los comentarios -->
                                    <div class="contenido-comentario-usuario-audio">

                                        <?php if ($datosComentarios->iduser == $_SESSION['logeado']) : ?>

                                            <!-- Boton de eliminar si el usuario es el propietario -->
                                            <a href="#" class="eliminar-comentario-audio float-right"
                                                data-idcomentario="<?php echo $datosComentarios->idcomentario ?>"
                                                data-idpublicacion="<?php echo $datosPublicacion->idpublicacion ?>">

                                                <i class="far fa-trash-alt"></i>

                                            </a>

                                        <?php endif ?>

                                        <span><?php echo $datosComentarios->fechaComentario ?></span>

                                        <p><?php echo htmlspecialchars($datosComentarios->contenidoComentario, ENT_QUOTES, 'UTF-8'); ?></p>

                                    </div>

                                </div>

                            <?php endforeach; ?>


                        </div>

                    </div>

                <?php else: ?>

                    <!--- Publicaciones de texto e/o imagen -->
                    <div class="container-usuarios-publicaciones">

                        <!-- Boton de eliminar si el usuario es el propietario -->
                        <?php if ($datosPublicacion->idusuario == $_SESSION['logeado']) : ?>
                            <div class="acciones-publicacion-usuario">
                                <a href="<?php echo URL_PROJECT ?>/publicaciones/eliminar/<?php echo $datosPublicacion->idpublicacion ?>">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </div>
                        <?php endif ?>

                        <!-- Aqui muestra la informacion de usuario de quien publico -->
                        <div class="usuarios-publicaciones-top">
                            <?php
                            $usuario = $datosPublicacion->usuario;
                            $fotoPerfil = $datosPublicacion->fotoPerfil;
                            $clasesImg = 'image-border-pub';
                            $clasesNombre = '';
                            ob_start(); // Captura la salida del nombre para colocarla dentro del h6
                            include URL_APP . '/views/Parciales/perfil-usuario.php';
                            $perfilHtml = ob_get_clean();
                            ?>
                            <?php echo $perfilHtml; ?>

                            <div class="informacion-usuario-publico">
                                <!-- Ya se imprimió el nombre de usuario arriba -->
                                <span><?php echo $datosPublicacion->fechaPublicacion ?></span>
                            </div>
                        </div>

                        <!-- Muestra la publicacion de texto y/o imagen -->
                        <div class="contenido-publicacion-usuario">
                            <p class="mb-1"><?php echo htmlspecialchars($datosPublicacion->contenidoPublicacion, ENT_QUOTES, 'UTF-8'); ?></p>
                            <img class="imagen-publicacion-usuario" src="data:image/jpg;base64,<?php echo base64_encode($datosPublicacion->fotoPublicacion); ?>" alt="">
                        </div>

                        <hr> <!---------------------------------------------- barra separadora ------------------------------------------------>

                        <!-- Aqui se muestran los likes y se pueden dar like a la publicacion -->
                        <div class="acciones-usuario-publicar mt-2">

                            <?php $likeClass = isset($likesMap[$datosPublicacion->idpublicacion]) ? 'like-active' : ''; ?>

                            <a href="<?php echo URL_PROJECT ?>/publicaciones/megusta/<?php echo $datosPublicacion->idpublicacion . "/" . $_SESSION['logeado'] . '/' . $datosPublicacion->idusuario ?>" class="<?php echo $likeClass; ?>">

                                <i class="fas fa-check"></i>Me gusta <span><?php echo $datosPublicacion->num_likes ?></span>

                            </a>

                        </div>

                        <hr> <!---------------------------------------------- barra separadora ------------------------------------------------>

                        <!-- Campo para comentarios -->
                        <div class="formulario-comentarios">

                            <!-- Aqui muestra la informacion de usuario actual -->

                            <img src="<?php echo URL_PROJECT . '/' . $datos['perfil']->fotoPerfil ?>" class="image-border" alt="">

                            <div class="acciones-formulario-comentario">
                                <form action="<?php echo URL_PROJECT ?>/publicaciones/comentar" method="POST">
                                    <input type="hidden" name="iduserPropietario" value="<?php echo $datosPublicacion->idusuario ?>">
                                    <input type="hidden" name="iduser" value="<?php echo $datos['usuario']->idusuario ?>">
                                    <input type="hidden" name="idpublicacion" value="<?php echo  $datosPublicacion->idpublicacion ?>">
                                    <input type="text" name="comentarios" class="form-comentario-usuario" placeholder="Agregar comentario" required>
                                    <div class="btn-comentario-container" style="float: right;">
                                        <button class="btn-purple">Comentar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php foreach ($comentariosAgrupados[$datosPublicacion->idpublicacion] ?? [] as $datosComentarios): ?>

                            <!-- Separador de comentarios -->
                            <div class="separador-comentarios mt-2"></div>

                            <div class="container-contenido-comentarios">

                                <!-- Aqui muestra la informacion del usuario que comento -->
                                <?php
                                $usuario = $datosComentarios->usuario;
                                $fotoPerfil = $datosComentarios->fotoPerfil;
                                $clasesImg = 'image-border mr-2';
                                $clasesNombre = 'big mr-2';
                                include URL_APP . '/views/Parciales/perfil-usuario.php';
                                ?>

                                <!-- Aqui se muestran los comentarios -->
                                <div class="contenido-comentario-usuario">
                                    <?php if ($datosComentarios->iduser == $_SESSION['logeado']) : ?>
                                        <!-- Boton de eliminar si el usuario es el propietario -->
                                        <a href="<?php echo URL_PROJECT ?>/publicaciones/eliminarComentario/<?php echo $datosComentarios->idcomentario ?>" class="float-right">
                                            <i class="far fa-trash-alt"></i>
                                        </a>
                                    <?php endif ?>
                                    <span><?php echo $datosComentarios->fechaComentario ?></span>
                                    <p><?php echo htmlspecialchars($datosComentarios->contenidoComentario, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            <?php endforeach; ?>










            <!-- Script para manejar los Ajax de los comentarios de las publicaciones de audio -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('.form-comentar-audio').forEach(form => {
                        const boton = form.querySelector('#btn-comentar-audio');
                        if (!boton) return;

                        boton.addEventListener('click', async (e) => {
                            e.preventDefault();

                            const inputComentario = form.querySelector('input[name="comentarios"]');
                            const texto = inputComentario.value.trim();
                            if (!texto) return;

                            const formData = new FormData(form);
                            const idPublicacion = form.querySelector('input[name="idpublicacion"]').value;
                            const contenedorComentarios = document.getElementById(`comentarios-audio-${idPublicacion}`);

                            try {
                                const response = await fetch('<?php echo URL_PROJECT ?>/publicaciones/comentarAudioAjax', {
                                    method: 'POST',
                                    body: formData
                                });

                                const nuevoComentarioHTML = await response.text();
                                // Cambio aquí: insertar al inicio
                                contenedorComentarios.insertAdjacentHTML('afterbegin', nuevoComentarioHTML);

                                inputComentario.value = '';
                            } catch (error) {
                                console.error('Error enviando comentario:', error);
                            }
                        });
                    });
                });
            </script>

            <!-- Script para manejar los Ajax del boton que elimina el comentario del comentario del audio -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {

                    document.addEventListener('click', async (e) => {
                        const target = e.target.closest('.eliminar-comentario-audio');

                        if (target) {
                            e.preventDefault();

                            const idComentario = target.dataset.idcomentario;
                            const idPublicacion = target.dataset.idpublicacion;

                            if (!idComentario || !idPublicacion) return;

                            const confirmacion = confirm('¿Estás seguro de que quieres eliminar este comentario?');
                            if (!confirmacion) return;

                            try {
                                const response = await fetch(`<?php echo URL_PROJECT ?>/publicaciones/eliminarComentarioDelAudio/${idComentario}`, {
                                    
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({}) // Enviamos un body vacío para que detecte correctamente POST

                                });

                                const resultado = await response.json();

                                if (resultado.success) {
                                    // Eliminar del DOM el contenedor del comentario
                                    const comentarioEliminado = target.closest('.container-contenido-comentarios-audio');
                                    if (comentarioEliminado) comentarioEliminado.remove();
                                } else {
                                    alert('No se pudo eliminar el comentario.');
                                }
                            } catch (error) {
                                console.error('Error al eliminar comentario:', error);
                            }
                        }
                    });

                });
            </script>









        </div>

        <!-- Columna eventos Derecha -->
        <div class="col-md-3">
            <div class="container-style-main2">

            </div>
        </div>

    </div>

</div>




<!-- Script para manejar los Ajax de los like de las publicaciones de audio -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-like-audio').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); // Evita la redirección

                const idpublicacion = this.dataset.idpublicacion;
                const idusuario = this.dataset.idusuario;
                const idpropietario = this.dataset.idpropietario;
                const likeCountSpan = this.querySelector('.like-count');
                const likeBtn = this;

                fetch('<?php echo URL_PROJECT ?>/publicaciones/megustaAudio', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `idpublicacion=${idpublicacion}&idusuario=${idusuario}&idpropietario=${idpropietario}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        likeCountSpan.textContent = data.likes;

                        if (data.status === 'liked') {
                            likeBtn.classList.add('like-active');
                        } else {
                            likeBtn.classList.remove('like-active');
                        }
                    })
                    .catch(error => console.error('Error AJAX:', error));
            });
        });
    });
</script>













<!-- Script que habilita el boton Publicar (lo habilita si hay texto escrito o una imagen cargada) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('contenido');
        const fileInput = document.getElementById('imagen');
        const submitButton = document.getElementById('btn-publi');

        function checkForm() {
            if (textarea.value.trim() !== '' || fileInput.files.length > 0) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        textarea.addEventListener('input', checkForm);
        fileInput.addEventListener('change', checkForm);
    });
</script>

<!-- Script que permite accionar el input del boton de subir foto -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btnSubirFoto = document.querySelector('.btn-subirFoto');
        var inputImagen = document.getElementById('imagen');

        btnSubirFoto.addEventListener('click', function() {
            inputImagen.click();
        });
    });
</script>



<?php

include_once URL_APP . '/views/custom/footer.php';

?>