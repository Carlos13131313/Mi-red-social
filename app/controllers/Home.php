<?php

class Home extends Controller
{
    public function __construct()
    {
        $this->usuario = $this->model('usuario');
        $this->publicaciones = $this->model('publicar');
    }

    public function index()
    {
        if (isset($_SESSION['logeado'])) {

            $datosUsuario = $this->usuario->getUsuario($_SESSION['usuario']);
            $datosPerfil = $this->usuario->getPerfil($_SESSION['logeado']);

            $datosPublicaciones = $this->publicaciones->getPublicaciones();

            $audios = $this->publicaciones->getPublicacionesAudios();

            $verificarLike = $this->publicaciones->misLikes($_SESSION['logeado']);

            $comentarios = $this->publicaciones->getComentarios();

            $informacionComentarios = $this->publicaciones->getInformacionComentarios($comentarios);

            $comentariosAudio = $this->publicaciones->getComentariosAudio();

            $informacionComentariosAudio = $this->publicaciones->getInformacionComentariosAudio($comentariosAudio);

            $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);

            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

            $publicacionesCombinadas = [];

            if ($datosPerfil) {

                $datosRed = [
                    'usuario' => $datosUsuario,
                    'perfil' => $datosPerfil,
                    'publicaciones' => $datosPublicaciones,
                    'audios' => $audios,
                    'misLikes' => $verificarLike,
                    'comentarios' => $informacionComentarios,
                    'comentarioAudio' => $informacionComentariosAudio,
                    'misNotificaciones' => $misNotificaciones,
                    'misNotificacionesMensajes' => $misNotificacionesMensajes
                ];

                $this->view('pages/home', $datosRed);

            } else {

                $this->view('pages/perfil/comrpletaPerfil', $_SESSION['logeado']);

            }

        } else {

            redirection('home/login');
            
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datosLogin = [
                'usuario' => trim($_POST['usuario']),
                'contrasena' => trim($_POST['contrasena'])
            ];

            $datosUsuario = $this->usuario->getUsuario($datosLogin['usuario']);

            //var_dump($datosUsuario);

            if ($this->usuario->verificarContrasena($datosUsuario, $datosLogin['contrasena'])) {
                $_SESSION['logeado'] = $datosUsuario->idusuario;
                $_SESSION['usuario'] = $datosUsuario->usuario;
                redirection('/home');
            } else {
                $_SESSION['errorLogin'] = 'El usuario o la contraseña son incorrectos';
                redirection('/home');
            }
        } else {
            if (isset($_SESSION['logeado'])) {
                redirection('/home');
            } else {
                $this->view('pages/login');
            }
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datosRegistro = [
                'privilegio' => '2',
                'email' => trim($_POST['email']),
                'usuario' => trim($_POST['usuario']),
                'contrasena' => password_hash(trim($_POST['contrasena']), PASSWORD_DEFAULT)
            ];

            if ($this->usuario->verificarUsuario($datosRegistro)) {
                if ($this->usuario->register($datosRegistro)) {
                    $_SESSION['LoginComplete'] = 'Tu registro fue satisfactorio, ahora puedes ingresar';
                    redirection('/home');
                } else {
                    // Manejar en caso de que falla el registro
                    $_SESSION['errorRegistro'] = 'Hubo un error al registrar el usuario';
                    redirection('/pages/register');
                }
            } else {
                // El correo electrónico ya existe, mostrar mensaje de error
                $_SESSION['usuarioError'] = 'Uusario ya registrado, intenta con uno diferente.';
                redirection('/home/register');
            }
        } else {
            if (isset($_SESSION['logeado'])) {
                redirection('/home');
            } else {
                $this->view('pages/register');
            }
        }
    }

    public function insertarRegistrosPerfil()
    {
        $carpeta = 'C:/xampp/htdocs/Proyecto2/public/img/imagenesPerfil/';
        opendir($carpeta);
        $rutaImagen = '/img/imagenesPerfil/' . $_FILES['imagen']['name'];
        $ruta = $carpeta . $_FILES['imagen']['name'];
        copy($_FILES['imagen']['tmp_name'], $ruta);

        $datos = [
            'idusuario' => trim($_POST['id_user']),
            'nombre' => trim($_POST['nombre']),
            'ruta' => $rutaImagen
        ];

        if ($this->usuario->insertarPerfil($datos)) {
            redirection('/home');
        } else {
            echo 'el perfil no se ha guardado';
        }
    }

    public function logout()
    {
        session_start();

        $_SESSION = [];

        session_destroy();

        redirection('/home/login');
    }

    public function usuarios()
    {
        if (isset($_SESSION['logeado'])) {

            $datosUsuario = $this->usuario->getUsuario($_SESSION['usuario']);
            $datosPerfil = $this->usuario->getPerfil($_SESSION['logeado']);
            $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
            $misMensajes = $this->publicaciones->getMensajes($_SESSION['logeado']);
            $usuariosRegistrados = $this->usuario->getAllUsuarios();
            $cantidadUsuarios = $this->usuario->getCantidadUsuarios();

            $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

            if ($datosPerfil) {
                $datosRed = [
                    'usuario' => $datosUsuario,
                    'perfil' => $datosPerfil,
                    'misNotificaciones' => $misNotificaciones,
                    'misMensajes' => $misMensajes,
                    'allUsuarios' => $usuariosRegistrados,
                    'cantidadUsuarios' => $cantidadUsuarios,
                    'misNotificacionesMensajes' => $misNotificacionesMensajes
                ];
                $this->view('pages/usuarios/usuarios', $datosRed);
            } else {
                redirection('/home');
            }
        }
    }

    public function buscar()
    {
        if (isset($_SESSION['logeado'])) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $busqueda = '%' . trim($_POST['buscar']) . '%';
                $datosBusqueda = $this->usuario->buscar($busqueda);

                $datosUsuario = $this->usuario->getUsuario($_SESSION['usuario']);
                $datosPerfil = $this->usuario->getPerfil($_SESSION['logeado']);
                $misNotificaciones = $this->publicaciones->getNotificaciones($_SESSION['logeado']);
                $misMensajes = $this->publicaciones->getMensajes($_SESSION['logeado']);

                $misNotificacionesMensajes = $this->publicaciones->getNotificacionesMensajes($_SESSION['logeado']);

                if ($datosPerfil) {
                    $datosRed = [
                        'usuario' => $datosUsuario,
                        'perfil' => $datosPerfil,
                        'misNotificaciones' => $misNotificaciones,
                        'misMensajes' => $misMensajes,
                        'resultado' => $datosBusqueda,
                        'misNotificacionesMensajes' => $misNotificacionesMensajes
                    ];


                    if ($datosBusqueda) {
                        $this->view('/pages/busqueda/buscar', $datosRed);
                    } else {
                        redirection('home');
                    }
                } else {
                    redirection('home');
                }
            } else {
                redirection('/home');
            }
        }
    }
}
