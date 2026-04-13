<?php

class Publicaciones extends Controller
{
    public function __construct()
    {
        $this->publicar = $this->model('publicar');
    }

    // funciones de texto e/o imagen

    //Esta funcion guarda las imagenes de las publicaciones directo en la base de datos como un BLOB en lugar de guardarlos en una carpeta.
    public function publicar($idUsuario)
    {
        // Define el array $datos inicialmente con los campos de texto, permitiendo que 'contenido' sea opcional
        $datos = [
            'iduser' => trim($idUsuario),
            'contenido' => isset($_POST['contenido']) ? trim($_POST['contenido']) : null,
            'foto' => null  // Establece null inicialmente para el campo de foto
        ];

        // Verifica si hay una imagen cargada
        if ($_FILES['imagen']['tmp_name']) {
            // Lee el contenido del archivo de la imagen
            $fotoPublicacion = file_get_contents($_FILES['imagen']['tmp_name']);
            $datos['foto'] = $fotoPublicacion;
        }

        // Llama al método publicar con los datos correctamente definidos
        if ($this->publicar->publicar($datos)) {
            redirection('/home');
        } else {
            echo 'Algo ocurrió';
        }
    }

    public function publicartextoenboard($idUsuario)
    {
        // Define el array $datos inicialmente con los campos de texto, permitiendo que 'contenido' sea opcional
        $datos = [
            'iduser' => trim($idUsuario),
            'contenido' => isset($_POST['contenido']) ? trim($_POST['contenido']) : null,
            'foto' => null  // Establece null inicialmente para el campo de foto
        ];

        // Verifica si hay una imagen cargada
        if ($_FILES['imagen']['tmp_name']) {
            // Lee el contenido del archivo de la imagen
            $fotoPublicacion = file_get_contents($_FILES['imagen']['tmp_name']);
            $datos['foto'] = $fotoPublicacion;
        }

        // Llama al método publicar con los datos correctamente definidos
        if ($this->publicar->publicar($datos)) {
            redirection('pages/dashboard/dashboard');
        } else {
            echo 'Algo ocurrió';
        }
    }

    public function eliminar($idpublicacion)
    {
        // Obtén la información de la publicación
        $publicacion = $this->publicar->getPublicacion($idpublicacion);

        if ($publicacion) {
            // Elimina la publicación de la base de datos
            if ($this->publicar->eliminarPublicacion($publicacion)) {
                redirection('/home');
            } else {
                echo 'No se pudo eliminar la publicación.';
            }
        } else {
            // Manejo de errores en caso de que la publicación no exista
            echo 'La publicación no existe.';
            redirection('/home');
        }
    }

    public function megusta($idpublicacion, $idusuario, $idusuarioPropietario)
    {
        $datos = [
            'idpublicacion' => $idpublicacion,
            'idusuario' => $idusuario,
            'idusuarioPropietario' => $idusuarioPropietario
        ];

        $datosPublicacion = $this->publicar->getPublicacion($idpublicacion);

        if ($this->publicar->rowLikes($datos)) {
            if ($this->publicar->eliminarLike($datos)) {
                $this->publicar->deleteLikeCount($datosPublicacion);
            }
            redirection('/home');
        } else {
            if ($this->publicar->agregarLike($datos)) {
                $this->publicar->addLikeCount($datosPublicacion);
                $this->publicar->addNotificacionLike($datos);
            }
            redirection('/home');
        }
    }

    public function comentar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'iduserPropietario' => trim($_POST['iduserPropietario']),
                'iduser' => trim($_POST['iduser']),
                'idpublicacion' => trim($_POST['idpublicacion']),
                'comentario' => trim($_POST['comentarios'])
            ];

            if ($this->publicar->publicarComentario($datos)) {
                $this->publicar->addNotificacionComentario($datos);
                redirection('/home');
            } else {
                redirection('/home');
            }
        } else {
            redirection('/home');
        }
    }

    public function eliminarComentario($id)
    {
        if ($this->publicar->eliminarComentarioUsuario($id)) {
            redirection('/home');
        } else {
            redirection('/home');
        }
    }





    ////////////////////////////////////////////////////////////////////////////////////////////////

    // funciones de Audio

    //Esta funcion guarda el audio directo en la base de datos como un BLOB. (desde Dashboard).
    public function publicarAudioenBoard($idUsuario)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['audioBlob']) && $_FILES['audioBlob']['error'] == 0) {
                $audioBlob = file_get_contents($_FILES['audioBlob']['tmp_name']);

                $datos = [
                    'iduser' => $idUsuario,
                    'audioPub' => $audioBlob
                ];

                if ($this->publicar->publicarGrabacion($datos)) {
                    // Enviar solo HTML vacío
                    echo 'Error al guardar el audio.';
                    exit;
                } else {
                    // También puedes retornar HTML vacío para evitar errores JS
                    echo 'No se recibió el archivo de audio.';
                    exit;
                }
            } else {
                echo 'Método no permitido.';
                exit;
            }
        }
    }

    // Muestra publicaciones con audio (se pueden llamar desede la vista).
    public function audiosPublicados()
    {
        $audios = $this->publicar->getPublicacionesAudios();

        $datos = [
            'audios' => $audios
        ];

        $this->view('pages/audio/audiospublicados', $datos);
    }

    public function eliminarPublicacionAudio($idpublicacion)
    {
        // Obtén la información de la publicación
        $publicacion = $this->publicar->getPublicacionAudio($idpublicacion);

        if ($publicacion) {
            // Elimina la publicación de la base de datos
            if ($this->publicar->eliminarPublicacionDeAudio($publicacion)) {
                redirection('/home');
            } else {
                echo 'No se pudo eliminar la publicación.';
            }
        } else {
            // Manejo de errores en caso de que la publicación no exista
            echo 'La publicación no existe.';
            redirection('/home');
        }
    }

    public function megustaAudio()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'idpublicacion' => $_POST['idpublicacion'],
                'idusuario' => $_POST['idusuario'],
                'idusuarioPropietario' => $_POST['idpropietario']
            ];

            $datosPublicacion = $this->publicar->getPublicacionAudio($datos['idpublicacion']);

            if ($this->publicar->rowLikesAudio($datos)) {
                // Ya existe el like → eliminar
                if ($this->publicar->eliminarLikeAudio($datos)) {
                    $this->publicar->deleteLikeCountAudio($datosPublicacion);
                }
                $nuevoConteo = $this->publicar->getTotalLikesAudio($datos['idpublicacion']);
                echo json_encode(['status' => 'unliked', 'likes' => $nuevoConteo]);
            } else {
                // No existe like → agregar
                if ($this->publicar->agregarLikeAudio($datos)) {
                    $this->publicar->addLikeCountAudio($datosPublicacion);
                    $this->publicar->addNotificacionLike($datos);
                }
                $nuevoConteo = $this->publicar->getTotalLikesAudio($datos['idpublicacion']);
                echo json_encode(['status' => 'liked', 'likes' => $nuevoConteo]);
            }
        }
    }

    public function comentarAudioAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $iduser = $_POST['iduser'];
            $iduserPropietario = $_POST['iduserPropietario'];
            $idpublicacion = $_POST['idpublicacion'];
            $comentario = trim($_POST['comentarios']); // asegúrate de que coincida con el campo del formulario

            $datos = [
                'iduser' => $iduser,
                'iduserPropietario' => $iduserPropietario,
                'idpublicacion' => $idpublicacion,
                'comentario' => $comentario  // 👈 asegúrate que sea 'comentario', no 'comentarios'
            ];

            if ($this->publicar->publicarComentarioAudio($datos)) {
                $comentarioReciente = $this->publicar->getUltimoComentarioAudio($idpublicacion);

                ob_start();
                include URL_APP . '/views/Parciales/comentario-audio.php'; // Este archivo debe imprimir el HTML del comentario
                $html = ob_get_clean();

                echo $html; // Enviar solo el HTML, no JSON
            } else {
                echo '<p class="error">Error al comentar.</p>';
            }
        } else {
            redirection('/home');
        }
    }











    public function eliminarComentarioDelAudio($idComentario)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        header('Content-Type: application/json');
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['logeado'])) {
                echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
                exit;
            }

            $idUsuario = $_SESSION['logeado'];

            $comentario = $this->public->getInformacionComentariosAudio($idComentario);

            if ($comentario && $comentario->iduser == $idUsuario) {
                $this->public->eliminarComentarioDelAudioUsuario($idComentario);
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'error' => 'No autorizado']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }
    }
}
