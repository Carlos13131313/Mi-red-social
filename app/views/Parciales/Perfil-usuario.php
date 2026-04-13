<?php

// Este es un arhcivo parcial para evitar duplicaciones en el codigo donde se muestre el nombre de usuario con
// enlaces al perfil y donde se muestra la imagen de perfil del usuario.


// Variables necesarias:
// $usuario: nombre de usuario
// $fotoPerfil: ruta de la imagen del perfil
// $clasesImg (opcional): clases extra para la imagen
// $clasesNombre (opcional): clases extra para el nombre
// $linkExtra (opcional): contenido HTML adicional después del nombre

if (!isset($clasesImg)) $clasesImg = '';
if (!isset($clasesNombre)) $clasesNombre = '';
if (!isset($linkExtra)) $linkExtra = '';
?>

<a href="<?php echo URL_PROJECT ?>/perfil/<?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>">
    <img src="<?php echo URL_PROJECT . '/' . $fotoPerfil ?>" alt="Perfil de <?php echo htmlspecialchars($usuario) ?>" class="<?php echo $clasesImg ?>">
</a>
<a href="<?php echo URL_PROJECT ?>/perfil/<?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>" class="<?php echo $clasesNombre ?>">
    <?php echo ucwords(htmlspecialchars($usuario)) ?>
</a>
<?php echo $linkExtra ?>