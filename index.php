
<?php
error_reporting(0);
require  'vendor\autoload.php';
require  'config.php';
require  'src\Controllers\WhatsApp.php';

switch($_SERVER['REQUEST_METHOD'])
{  
    case 'POST':
         $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/');
         $env = $dotenv->load();
         $controllerWps = new WhatsAppController($env['PRIVATE_KEY']);
         $controllerWps->post(json_decode(file_get_contents('php://input')));
    break;
}

?>

