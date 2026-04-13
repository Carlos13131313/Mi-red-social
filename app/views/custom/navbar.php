<header>

    <nav class="navbar">

        <!-- Cuadro de busqueda -->
        <form action="<?php echo URL_PROJECT ?>/home/buscar" method="POST" class="tipe-form">
            <input id="text" type="text" name="buscar" class="form-style" placeholder="Buscar" />
            <button class="btn-form" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <!-- Contenedor con una lista de los iconos -->
        <div class="collapsenavbar-collapse" id="navbarSupportedContent">
            <ul>

                <!-- icono de home -->
                <a class="big" href="<?php echo URL_PROJECT ?>/home/">
                    <span class="big">
                        <i class="fas fa-home"></i>
                    </span>
                </a>

                <!-- icono de ojo -->
                <a class="big" href="<?php echo URL_PROJECT ?>/home/usuarios">
                    <span class="big">
                        <i class="fa-sharp fa-solid fa-eye"></i>
                    </span>
                </a>

                <!-- icono de usuario -->
                <a class="big" href="<?php echo URL_PROJECT ?>/perfil/<?php echo $datos['usuario']->usuario ?>">
                    <span class="big">
                        <i class="fas fa-user"></i>
                    </span>
                </a>

                <!-- icono de carta (mensajes) -->
                <a id="mensajes-link" href="<?php echo URL_PROJECT ?>/mensajes">
                    <span class="big">
                        <i class="far fa-envelope"></i>
                    </span>
                    <span class="mr-0 ml1"> </span>

                    <?php if ($datos['misNotificacionesMensajes'] > 0) : ?>
                        <div id="mensajes-count" class="bg-notificacion">
                            <?php echo $datos['misNotificacionesMensajes'] ?>
                        </div>
                    <?php endif; ?>
                </a>

                <!-- icono de campana (notificaciones/likes) -->
                <a id="notificaciones-link" href="<?php echo URL_PROJECT ?>/notificaciones">
                    <span class="big">
                        <i class="far fa-bell"></i>
                    </span>

                    <?php if ($datos['misNotificaciones'] > 0) : ?>
                        <div id="notificaciones-count" class="bg-notificacion">
                            <?php echo $datos['misNotificaciones'] ?>
                        </div>
                    <?php endif; ?>
                </a>

            </ul>

            <!-- Imagen de perfil con menu ara cerrar sesion -->
            <div class="dropdown">
                <span class="btn-radio dropdown-toggle" id="actionPerfil" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?php echo URL_PROJECT . '/' . $datos['perfil']->fotoPerfil ?>" alt="perfil" class="img-perfil" />
                    <!-- <?php echo ucwords($_SESSION['usuario']); ?> -->
                </span>

                <div class="dropdown-menu" aria-label="actionPerfil">
                    <!-- <a class="dropdown-item text-dark" href="">Action</a> -->
                    <!-- <a class="dropdown-item text-dark" href="">Another</a> -->
                    <a class="dropdown-item text-dark" href="<?php echo URL_PROJECT ?>/home/logout">Salir</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="circulo">
        <a href="<?php echo URL_PROJECT ?>/dashboard/<?php echo $datos['usuario']->usuario ?>" class="enlace">

            <div class="Logo">
            </div>

        </a>
    </div>

</header>

<!-- Los scripts para ejecutar el AJAX esta en footer.php -->

<!-- Script AJAX icono de campana (notificaciones/likes) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function actualizarNotificaciones() {
            fetch("<?php echo URL_PROJECT ?>/notificaciones/obtenerNotificaciones")
                .then(response => response.json())
                .then(datos => {
                    const notificacionesCount = document.getElementById("notificaciones-count");
                    if (datos.misNotificaciones > 0) {
                        if (!notificacionesCount) {
                            const div = document.createElement('div');
                            div.id = 'notificaciones-count';
                            div.className = 'bg-notificacion';
                            div.textContent = datos.misNotificaciones;
                            document.querySelector("#notificaciones-link").appendChild(div);
                        } else {
                            notificacionesCount.textContent = datos.misNotificaciones;
                        }
                    } else {
                        if (notificacionesCount) {
                            notificacionesCount.remove();
                        }
                    }
                })
                .catch(error => console.error('Error al cargar las notificaciones:', error));
        }

        setInterval(actualizarNotificaciones, 2000);
    });
</script>

<!-- Script AJAX icono de carta (mensajes) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function actualizarMensajes() {
            fetch("<?php echo URL_PROJECT ?>/mensajes/obtenerNotificacionesMensajes")
                .then(response => response.json())
                .then(datos => {
                    const mensajesCount = document.getElementById("mensajes-count");
                    if (datos.misNotificacionesMensajes > 0) {
                        if (!mensajesCount) {
                            const div = document.createElement('div');
                            div.id = 'mensajes-count';
                            div.className = 'bg-notificacion';
                            div.textContent = datos.misNotificacionesMensajes;
                            document.querySelector("#mensajes-link").appendChild(div);
                        } else {
                            mensajesCount.textContent = datos.misNotificacionesMensajes;
                        }
                    } else {
                        if (mensajesCount) {
                            mensajesCount.remove();
                        }
                    }
                })
                .catch(error => console.error('Error al cargar las notificaciones de mensajes:', error));
        }

        setInterval(actualizarMensajes, 2000);
    });
</script>

<!-- script para el menu dorpdown -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtiene el elemento del botón de menú
        var dropdownToggle = document.getElementById("actionPerfil");

        // Obtiene el menú desplegable
        var dropdownMenu = document.querySelector(".dropdown-menu");

        // Agrega un evento de clic al botón de menú
        dropdownToggle.addEventListener("click", function(event) {
            // Evita que el clic se propague al contenedor principal
            event.stopPropagation();

            // Alterna la visibilidad del menú desplegable
            if (dropdownMenu.style.display === "none" || dropdownMenu.style.display === "") {
                dropdownMenu.style.display = "block";
            } else {
                dropdownMenu.style.display = "none";
            }
        });

        // Agrega un evento de clic al documento para ocultar el menú cuando se hace clic fuera de él
        document.addEventListener("click", function() {
            dropdownMenu.style.display = "none";
        });
    });
</script>