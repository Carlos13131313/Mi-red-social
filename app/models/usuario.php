<?php

class usuario
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function getUsuario($usuario)
    {
        $this->db->query('SELECT * FROM usuarios WHERE usuario = :user');
        $this->db->bind(':user', $usuario);
        return $this->db->register();
    }

    public function getUsuarios()
    {
        $this->db->query('SELECT idusuario , usuario FROM usuarios');
        return $this->db->registers();
    }

    public function getPerfil($idusuario)
    {
        $this->db->query('SELECT * FROM perfil WHERE idusuario = :id');
        $this->db->bind(':id', $idusuario);
        return $this->db->register();
    }

    public function verificarContrasena($datosUsuario, $contrasena)
    {
        if (password_verify($contrasena, $datosUsuario->contrasena)) {
            return true;
        } else {
            return false;
        }
    }

    public function verificarUsuario($datosUsuario)
    {
        $this->db->query('SELECT usuario FROM usuarios WHERE usuario = :user');
        $this->db->bind(':user', $datosUsuario['usuario']);
        if ($this->db->rowCount()) {
            return false;
        } else {
            return true;
        }
    }

    public function register($datosUsuario)
    {
        $this->db->query('INSERT INTO usuarios (idPrivilegio , correo , usuario , contrasena) VALUES (:privilegio, :correo, :usuario, :contrasena)');
        $this->db->bind(':privilegio', $datosUsuario['privilegio']);
        $this->db->bind(':correo', $datosUsuario['email']);
        $this->db->bind(':usuario', $datosUsuario['usuario']);
        $this->db->bind(':contrasena', $datosUsuario['contrasena']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function insertarPerfil($datos)
    {
        $this->db->query('INSERT INTO perfil (idUsuario , fotoPerfil , nombreCompleto) VALUES (:id , :rutaFoto , :nombre)');
        $this->db->bind(':id', $datos['idusuario']);
        $this->db->bind(':rutaFoto', $datos['ruta']);
        $this->db->bind(':nombre', $datos['nombre']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllUsuarios()
    {
        $this->db->query('SELECT U.idusuario , U.usuario , P.fotoPerfil , P.nombreCompleto FROM usuarios U
        INNER JOIN perfil P ON P.idUsuario = U.usuario');
        return $this->db->registers();
    }

    public function getCantidadUsuarios()
    {
        $this->db->query('SELECT idusuario FROM usuarios');
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function buscar($busqueda)
    {
        $this->db->query('SELECT U.usuario , P.fotoPerfil , P.nombreCompleto FROM usuarios U
        INNER JOIN perfil P ON P.idUsuario = U.idusuario
        WHERE U.usuario LIKE :buscar ');
        $this->db->bind(':buscar' , $busqueda);
        return $this->db->registers();
    }
}
