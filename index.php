
<?php
require  'vendor\autoload.php';
require  'config.php';
require  'src\Controllers\WhatsApp.php';

switch($_SERVER['REQUEST_METHOD'])
{  
    case 'POST':
         $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/');
         $env = $dotenv->load();
         $controllerWps = new WhatsAppController($env['PRIVATE_TEST']);
         $controllerWps->post(json_decode(file_get_contents('php://input')));
    break;
}

?>

