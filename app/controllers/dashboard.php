<?php

class Dashboard extends Controller
{
    public function __construct()
    {
        $this->perfil = $this->model('perfilUsuario');
        $this->usuario = $this->model('usuario');
        $this->publicaciones = $this->model('publicar');
    }

    public function index($usuario)
    {
        if (isset($_SESSION['logeado'])) {
            // Obtener datos del usuario y del perfil
            $datosUsuario = $this->usuario->getUsuario($usuario);
            if (!$datosUsuario) {
                // Manejar el caso en que no se encuentre el usuario
                // Redirigir a una página de error o mostrar un mensaje adecuado
                $this->view('errors/usuario_no_encontrado');
                return;
            }

            $datosPerfil = $this->usuario->getPerfil($datosUsuario->idusuario);

            // Obtener notificaciones y mensajes del usuario logeado
            $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
            $misMensajes = $this->publicaciones->getMensajes($_SESSION['logeado']);
            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

            // Preparar datos para pasar a la vista
            $datos = [
                'perfil' => $datosPerfil,
                'usuario' => $datosUsuario,
                'misNotificaciones' => $misNotificaciones,
                'misMensajes' => $misMensajes,
                'misNotificacionesMensajes' => $misNotificacionesMensajes
            ];

            // Cargar la vista con los datos
            $this->view('pages/dashboard/dashboard', $datos);
        } else {
            // Redirigir a la página de login si no está logeado
            $this->view('pages/login');
        }
    }
}
