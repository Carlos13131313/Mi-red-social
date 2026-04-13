<?php

class Perfil extends Controller
{
    public function __construct()
    {
        $this->perfil = $this->model('perfilUsuario');
        $this->usuario = $this->model('usuario');
        $this->publicaciones = $this->model('publicar');

    }

    public function index($user)
    {
        if (isset($_SESSION['logeado'])) {

            $datosUsuario = $this->usuario->getUsuario($user);
            $datosPerfil = $this->usuario->getPerfil($datosUsuario->idusuario);
            $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
            $misMensajes = $this->publicaciones->getMensajes($_SESSION['logeado']);

            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

            $datos = [
                'perfil' => $datosPerfil,
                'usuario' => $datosUsuario,
                'misNotificaciones' => $misNotificaciones,
                'misMensajes' => $misMensajes,
                'misNotificacionesMensajes' => $misNotificacionesMensajes
            ];

            $this->view('pages/perfil/perfil', $datos);
        }
    }

    public function cambiarImagen()
    {
        $carpeta = 'C:/xampp/htdocs/Proyecto2/public/img/imagenesPerfil/';
        opendir($carpeta);
        $rutaImagen = '/img/imagenesPerfil/' . $_FILES['imagen']['name'];
        $ruta = $carpeta . $_FILES['imagen']['name'];
        copy($_FILES['imagen']['tmp_name'], $ruta);

        $datos = [
            'idusuario' => trim($_POST['id_user']),
            'ruta' => $rutaImagen
        ];

        $imagenActual = $this->usuario->getPerfil($datos['idusuario']);

        unlink('C:/xampp/htdocs/Proyecto2/public/' . $imagenActual->fotoPerfil);

        if ($this->perfil->editarFoto($datos)) {
            redirection('/perfil');
        } else {
            echo 'el perfil no se ha guardado';
        }
    }
}