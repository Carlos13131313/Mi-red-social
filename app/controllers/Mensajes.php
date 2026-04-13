<?php

class Mensajes  extends Controller
{
    public function __construct()
    {
        $this->publicaciones = $this->model('publicar');
        $this->usuario = $this->model('usuario');
        $this->mensaje = $this->model('mensajesMod');
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $datosMensaje = [
                'idusermando' => trim($_POST['idusermando']),
                'enviar' => trim($_POST['enviar']),
                'mensaje' => trim($_POST['mensaje'])
            ];

            if ($this->mensaje->enviarMensaje($datosMensaje)) {
                redirection('/mensajes');
            } else {
                redirection('/mensajes');
            }
        } else {
            if (isset($_SESSION['logeado'])) {
                $datosUsuario = $this->usuario->getUsuario($_SESSION['usuario']);
                $datosPerfil = $this->usuario->getPerfil($datosUsuario->idusuario);
                $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
                $datosUsuarios = $this->usuario->getUsuarios();
                $misMensajes = $this->mensaje->getMensajes($_SESSION['logeado']);

                $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

                $datos = [
                    'perfil' => $datosPerfil,
                    'usuario' => $datosUsuario,
                    'misNotificaciones' => $misNotificaciones,
                    'usuarios' => $datosUsuarios,
                    'misMensajes' => $misMensajes,
                    'misNotificacionesMensajes' => $misNotificacionesMensajes
                ];

                $this->view('pages/mensajes/mensajes', $datos);
            } else {
                redirection('/home');
            }
        }
    }

    public function eliminarMensaje($id)
    {
        if ($this->mensaje->eliminarMensaje($id)) {
            redirection('/mensajes');
        } else {
            redirection('/mensajes');
        }
    }

    // Nuevo método para obtener mensajes en formato JSON por el metodo AJAX
    public function obtenerNotificacionesMensajes()
    {
        if (isset($_SESSION['logeado'])) {
            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);
            echo json_encode(['misNotificacionesMensajes' => $misNotificacionesMensajes]);
        } else {
            echo json_encode(['error' => 'Usuario no logeado']);
        }
    }
}
