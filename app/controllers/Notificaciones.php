<?php

class Notificaciones extends Controller
{
    public function __construct()
    {
        $this->notificar = $this->model('notificacion');
        $this->publicaciones = $this->model('publicar');
        $this->usuario = $this->model('usuario');
    }


    public function index()
    {
        if (isset($_SESSION['logeado'])) {

            $datosUsuario = $this->usuario->getUsuario($_SESSION['usuario']);
            $datosPerfil = $this->usuario->getPerfil($datosUsuario->idusuario);


            $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
            $notificaciones = $this->notificar->obtenerNotificaciones($_SESSION['logeado']);

            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

            $datos = [
                'perfil' => $datosPerfil,
                'usuario' => $datosUsuario,
                'misNotificaciones' => $misNotificaciones,
                'notificaciones' => $notificaciones,
                'misNotificacionesMensajes' => $misNotificacionesMensajes
            ];

            $this->view('pages/notificaciones/notificaciones', $datos);
        } else {
            redirection('/home');
        }
    }
    
    // Nuevo método para obtener notificaciones en formato JSON por el metodo AJAX
    public function obtenerNotificaciones()
    {
        if (isset($_SESSION['logeado'])) {
            $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);
            
            $datos = [
                'misNotificaciones' => $misNotificaciones,
                'misNotificacionesMensajes' => $misNotificacionesMensajes
            ];

            echo json_encode($datos);
        } else {
            echo json_encode(['error' => 'Usuario no autenticado']);
        }
    }

    public function eliminar($id)
    {
        if (isset($_SESSION['logeado'])) {
            if ($this->notificar->eliminarNotificacion($id)) {
                redirection('/notificaciones');
            } else {
                redirection('/notificaciones');
            }
        } else {
            redirection('/home');
        }
    }
}
