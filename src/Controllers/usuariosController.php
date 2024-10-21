
<?php

require './config.php';
require './model/usuarios.php';



class UsuariosController  {

    private $model;

    public function __construct() {
         $this->model = new UsuariosModel();
    }

    public function index() {
        if(empty($_GET))
        {
                // obtenemos todos los usuarios
                $response = $this->model->getAllUsers();
                $response !== false ? Api::message(201,"success",$response) : Api::message(400,"failed : Hubo un problema al obtener usuarios",null) ;
        }
        else if(isset($_GET['userId']))
        {
                // obtener usuario en especifico
                $userId = (int) $_GET['userId'];
                $response = $this->model->getUser($userId);
                $response !== false ? Api::message(201,"success",$response) : Api::message(400,"failed : Hubo un problema al obtener usuarios",null) ;
        } 
        else
        {
                Api::message(400,"failed : Parámetros no válidos o faltantes",null);
        }
    }

    public function post($datos) {
        //metodo que guarda un nuevo usuario
        if(isset($datos->usuario) && isset($datos->cont)) //validamos si existe propiedad
        {
                    $response = $this->model->InsertUser($datos->usuario,$datos->cont);
                    $response === true ? Api::message(200,"success : Usuario creado correctamente",null) : Api::message(400,"failed : Hubo un problema al guardar el usuario",null);
        }
        else
        {
            Api::message(400,"failed : Verifica las propiedades usuario",null);
        }
    }
}

?>

