
<?php
require  'vendor\autoload.php';
require  'src\Controllers\WhatsApp.php';

switch($_SERVER['REQUEST_METHOD'])
{  
    case 'POST':
         $controllerWps = new WhatsAppController();
         $controllerWps->post(json_decode(file_get_contents('php://input')));
    break;
}

?>

