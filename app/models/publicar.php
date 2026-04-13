<?php

class publicar
{

    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    // funciones de texto e/o imagen

    public function publicar($datos)
    {
        $this->db->query('INSERT INTO publicaciones (idUserPublico, contenidoPublicacion, fotoPublicacion) VALUES (:iduser, :contenido, :foto)');
        $this->db->bind(':iduser', $datos['iduser']);

        // Maneja el caso de contenido null
        if ($datos['contenido'] !== null) {
            $this->db->bind(':contenido', $datos['contenido']);
        } else {
            $this->db->bind(':contenido', null, PDO::PARAM_NULL);
        }

        // Maneja el caso de foto null
        if ($datos['foto'] !== null) {
            $this->db->bind(':foto', $datos['foto'], PDO::PARAM_LOB);
        } else {
            $this->db->bind(':foto', null, PDO::PARAM_NULL);
        }

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getPublicaciones()
    {
        $this->db->query('SELECT P.idpublicacion , P.contenidoPublicacion , P.fotoPublicacion , P.fechaPublicacion , P.num_likes , U.usuario , U.idusuario , 
        Per.fotoPerfil FROM publicaciones P
        INNER JOIN usuarios U ON U.idusuario = P.idUserPublico
        INNER JOIN perfil Per On Per.idUsuario = P.idUserPublico');
        return $this->db->registers();
    }

    public function getPublicacion($id)
    {
        $this->db->query('SELECT * FROM publicaciones WHERE idpublicacion = :id');
        $this->db->bind(':id', $id);
        return $this->db->register();
    }

    public function rowLikes($datos)
    {
        $this->db->query('SELECT * FROM likes WHERE idPublicacion = :publicacion AND idUser = :iduser');
        $this->db->bind(':publicacion', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['idusuario']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function eliminarLike($datos)
    {
        $this->db->query('DELETE FROM likes WHERE idPublicacion = :publicacion AND idUser = :iduser');
        $this->db->bind(':publicacion', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['idusuario']);
        $this->db->execute();
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteLikeCount($datos)
    {
        $this->db->query('UPDATE publicaciones SET num_likes = :countLike WHERE idpublicacion = :idPublicacion');
        $this->db->bind(':countLike', ($datos->num_likes - 1));
        $this->db->bind(':idPublicacion', $datos->idpublicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function agregarLike($datos)
    {
        $this->db->query('INSERT INTO likes (idPublicacion , idUser) VALUES (:publicacion , :iduser)');
        $this->db->bind(':publicacion', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['idusuario']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function addLikeCount($datos)
    {
        $this->db->query('UPDATE publicaciones SET num_likes = :countLike WHERE idpublicacion = :idPublicacion');
        $this->db->bind(':countLike', ($datos->num_likes + 1));
        $this->db->bind(':idPublicacion', $datos->idpublicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Esta funcion se usa tanto para publicaciones normales como en las publicaciones de Audio. (se usa para avisar al usuario que alguien dio like su publicacaión)
    public function addNotificacionLike($datos)
    {
        $this->db->query('INSERT INTO notificaciones (idUsuario , usuarioAccion , tipoNotificaion) VALUES (:idusuario , :usuarioAccion , :tipo)');
        $this->db->bind('idusuario', $datos['idusuarioPropietario']);
        $this->db->bind('usuarioAccion', $datos['idusuario']);
        $this->db->bind('tipo', 1);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Estos son los likes que se muestran en el icono de notificacion
    public function misLikes($user)
    {
        $this->db->query('SELECT * FROM likes WHERE idUser = :id');
        $this->db->bind(':id', $user);
        return $this->db->registers();
    }

    public function publicarComentario($datos)
    {
        $this->db->query('INSERT INTO comentarios (idPublicacion , idUser , contenidoComentario	) VALUES (:idpubli , :iduser , :cometario)');
        $this->db->bind(':idpubli', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['iduser']);
        $this->db->bind(':cometario', $datos['comentario']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getComentarios()
    {
        $this->db->query('SELECT * FROM comentarios');
        return $this->db->registers();
    }

    public function getInformacionComentarios($comentarios)
    {
        $this->db->query('SELECT C.idPublicacion , C.iduser , C.idcomentario , C.contenidoComentario , C.fechaComentario , P.fotoPerfil , U.usuario FROM comentarios C
        INNER JOIN perfil P ON P.idUsuario = C.idUser
        INNER JOIN usuarios U On U.idusuario = C.idUser');
        return $this->db->registers();
    }

    public function eliminarComentarioUsuario($id)
    {
        $this->db->query('DELETE FROM comentarios WHERE idcomentario = :id');
        $this->db->bind(':id', $id);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminarPublicacion($publicacion)
    {
        $this->db->query('DELETE FROM publicaciones WHERE idpublicacion = :id');
        $this->db->bind(':id', $publicacion->idpublicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Esta funcion se usa tanto para publicaciones normales como en las publicaciones de Audio. (se usa para avisar al usuario que alguien comento su publicacaión)
    public function addNotificacionComentario($datos)
    {
        $this->db->query('INSERT INTO notificaciones (idUsuario , usuarioAccion , tipoNotificaion) VALUES (:idusuario , :usuarioAccion , :tipo)');
        $this->db->bind('idusuario', $datos['iduserPropietario']);
        $this->db->bind('usuarioAccion', $datos['iduser']);
        $this->db->bind('tipo', 2);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getNotificaciones($id)
    {
        $this->db->query('SELECT idnotificacion FROM notificaciones WHERE idUsuario = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getNotificacionesMensajes($id)
    {
        $this->db->query('SELECT idmensaje FROM mensajes WHERE usuarios_idusuario = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getMensajes()
    {
        $this->db->query('SELECT idmensaje FROM mensajes');
        $this->db->execute();
        return $this->db->registers();
    }












    ////////////////////////////////////////////////////////////////////////////////////////////////

    // funciones de Audio

    public function publicarGrabacion($datos)
    {
        $this->db->query('INSERT INTO publicacionesdeaudio (idUserPublico, audioPublicacion) VALUES (:iduser, :audioPub)');
        $this->db->bind(':iduser', $datos['iduser']);
        $this->db->bind(':audioPub', $datos['audioPub'], PDO::PARAM_LOB);

        return $this->db->execute();
    }

    public function getPublicacionesAudios()
    {
        $this->db->query('SELECT P.idpublicacion, P.audioPublicacion , P.fechaPublicacion , P.num_likes , U.usuario , U.idusuario , 
        Per.fotoPerfil FROM publicacionesdeaudio P
        INNER JOIN usuarios U ON U.idusuario = P.idUserPublico
        INNER JOIN perfil Per On Per.idUsuario = P.idUserPublico');
        $audios = $this->db->registers();

        // Convertimos los BLOB a base64 directamente para usarlos en la vista
        foreach ($audios as $audio) {
            $audio->audioBase64 = base64_encode($audio->audioPublicacion);
        }

        return $audios;
    }

    public function getPublicacionAudio($id)
    {
        $this->db->query('SELECT * FROM publicacionesdeaudio WHERE idpublicacion = :id');
        $this->db->bind(':id', $id);
        $audio = $this->db->register();

        if ($audio) {
            $audio->audioBase64 = base64_encode($audio->audioPublicacion);
        }

        return $audio;
    }

    public function eliminarPublicacionDeAudio($publicacion)
    {
        $this->db->query('DELETE FROM publicacionesdeaudio WHERE idpublicacion = :id');
        $this->db->bind(':id', $publicacion->idpublicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Consulta los likes de la publicacion de Audio.
    public function rowLikesAudio($datos)
    {
        $this->db->query('SELECT * FROM likesaudio WHERE idPublicacion = :publicacion AND idUser = :iduser');
        $this->db->bind(':publicacion', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['idusuario']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Esta funcion es para el Ajax de los likes.
    public function getTotalLikesAudio($idpublicacion)
    {
        $this->db->query("SELECT num_likes FROM publicacionesdeaudio WHERE idpublicacion = :idpublicacion");
        $this->db->bind(':idpublicacion', $idpublicacion);
        $row = $this->db->register();
        return $row ? $row->num_likes : 0;
    }


    public function eliminarLikeAudio($datos)
    {
        $this->db->query('DELETE FROM likesaudio WHERE idPublicacion = :publicacion AND idUser = :iduser');
        $this->db->bind(':publicacion', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['idusuario']);
        $this->db->execute();
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteLikeCountAudio($datos)
    {
        $this->db->query('UPDATE publicacionesdeaudio SET num_likes = :countLike WHERE idpublicacion = :idPublicacion');
        $this->db->bind(':countLike', ($datos->num_likes - 1));
        $this->db->bind(':idPublicacion', $datos->idpublicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function agregarLikeAudio($datos)
    {
        $this->db->query('INSERT INTO likesaudio (idPublicacion , idUser) VALUES (:publicacion , :iduser)');
        $this->db->bind(':publicacion', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['idusuario']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function addLikeCountAudio($datos)
    {
        $this->db->query('UPDATE publicacionesdeaudio SET num_likes = :countLike WHERE idpublicacion = :idPublicacion');
        $this->db->bind(':countLike', ($datos->num_likes + 1));
        $this->db->bind(':idPublicacion', $datos->idpublicacion);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function publicarComentarioAudio($datos)
    {
        $this->db->query('INSERT INTO comentariosaudio (idPublicacion , idUser , contenidoComentario	) VALUES (:idpubli , :iduser , :cometario)');
        $this->db->bind(':idpubli', $datos['idpublicacion']);
        $this->db->bind(':iduser', $datos['iduser']);
        $this->db->bind(':cometario', $datos['comentario']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getComentariosAudio()
    {
        $this->db->query('SELECT * FROM comentariosaudio');
        return $this->db->registers();
    }

    public function getInformacionComentariosAudio($idComentario)
    {
        $this->db->query('SELECT C.idPublicacion , C.iduser , C.idcomentario , C.contenidoComentario , C.fechaComentario , P.fotoPerfil , U.usuario FROM comentariosaudio C
        INNER JOIN perfil P ON P.idUsuario = C.idUser
        INNER JOIN usuarios U On U.idusuario = C.idUser');
        return $this->db->registers();
    }






    //public function obtenerComentarioDelAudioPorId($idComentario)
    //{
    //    $this->db->query('SELECT C.* FROM comentariosaudio C WHERE C.idcomentario = :id');
    //    $this->db->bind(':id', $idComentario);
    //    return $this->db->register();
    //}

    public function eliminarComentarioDelAudioUsuario($id)
    {
        $this->db->query('DELETE FROM comentariosaudio WHERE idcomentario = :id');
        $this->db->bind(':id', $id);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }







    public function getUltimoComentarioAudio($idpublicacion)
    {
        $this->db->query('SELECT C.idPublicacion, C.iduser, C.idcomentario, C.contenidoComentario, C.fechaComentario, P.fotoPerfil, U.usuario FROM comentariosaudio C
        INNER JOIN perfil P ON P.idUsuario = C.idUser
        INNER JOIN usuarios U ON U.idusuario = C.idUser
        WHERE C.idPublicacion = :idpublicacion
        ORDER BY C.idcomentario DESC
        LIMIT 1');
        $this->db->bind(':idpublicacion', $idpublicacion);
        return $this->db->register();
    }
}
