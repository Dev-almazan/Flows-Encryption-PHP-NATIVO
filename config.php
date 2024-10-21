
<?php



class Api{

    public static  function message($uno,$dos,$tres)
      {
	      header('Content-Type: application/json; charset=utf-8');
            http_response_code($uno); 
            $newobj = new stdClass(); 
            $newobj->http_response = $uno;
            $newobj->message =  $dos;
            $newobj->results=$tres;
            $new = (array)$newobj;
            echo json_encode($new);    
        
      } 
      
      

}

?>

