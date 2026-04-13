<?php

include_once URL_APP . '/views/custom/header.php';

var_dump($datos);

?>

<div class="completarPerfil">
    <div class="containerCompletarPerfil">
            <h2 class="text-center">Completa tu perfil</h2>
            <h6 class="text-center">Antes de continuar deberas completar tu perfil</h6>
            <hr>
            <div class="content-completar-perfil center">
                <form action="<?php echo URL_PROJECT ?>/home/insertarRegistrosPerfil" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_user" value="<?php echo $_SESSION['logeado'] ?>">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" require>
                    </div>
                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="imagen" id="imagen" required>
                            <label class="custom-file-label" for="imagen">Seleccionar foto</label>
                        </div>
                    </div>
                    <button class="btn-purple btn-black">Registrar datos</button>
                </form>
            </div>
    </div>
</div>

<?php

include_once URL_APP . '/views/custom/footer.php';

?>