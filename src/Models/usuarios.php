
<?php

require './model/db.php';

 class UsuariosModel extends DataBase{


    public function getAllUsers()  
    {
         $result= $this->query("SELECT * FROM users");
         if($result->num_rows > 0) 
         {
            
            while($row = $result->fetch_array()) 
            {
                   $datos[] = [
                     'id' => $row[0],
                     'nombre' => $row[1],
                     'password' => $row[2]
                  ];
            }
            return $datos;
         }
         else
         {
            return false;
         } 
         $this->db->close();
    }

   public function getUser($userId)  
   {
         $result= $this->query("SELECT * FROM users WHERE id_usuario = '$userId' limit 1");
         if($result->num_rows > 0) 
         {
            
            while($row = $result->fetch_array()) 
            {
                   $datos[] = [
                     'id' => $row[0],
                     'nombre' => $row[1],
                     'password' => $row[2]
                  ];
            }
            return $datos;
         }
         else
         {
            return false;
         } 
         $this->db->close();
    }



   public function InsertUser($usuario,$password)  
    {
         $result= $this->query("INSERT INTO users VALUES ('','$usuario','$password','')");
         return $result;   
         $this->db->close();
    }


 }

?>

