
<?php


class DataBase{

    private $ip = "localhost",$nameDb ="personajes",$user="root",$pass="";
    protected $db;
    public function __construct() {
         $mysqli = new mysqli(hostname: $this->ip,username: $this->user,password: $this->pass,database: $this->nameDb) ;
        if ($mysqli->connect_errno) {
            echo($mysqli->connect_errno);
            exit();
        }
        $this->db = $mysqli;
    }

    public function query($sql) {
        $result = $this->db->query($sql);
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $this->db->error);
        }
        return $result;
    }


}

?>

