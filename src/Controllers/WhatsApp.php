
<?php

use phpseclib3\Crypt\RSA;


class WhatsAppController  {

   private $publickey, $privateKey;

    public function __construct() {
         $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/');
         $env = $dotenv->load();
         $this->publicKey = $env['PUBLIC_KEY'];
         $this->privateKey = $env['PRIVATE_KEY'];
    }

    public function post($datos) {
     /*Obtenemos private key */
     $publicKey = $this->publicKey;
     $this->decryptRequest($datos,$publicKey);
    }

     public function decryptRequest($datos,$publicKey) 
     {
          /* Verificamos parametros obligatorios */     
          $encrypted_flow_data = isset($datos->encrypted_flow_data) ? base64_decode($datos->encrypted_flow_data) :  Api::message(421,"encrypted_flow_data: null","error");
          $encryptedAesKey = isset($datos->encrypted_aes_key) ? base64_decode($datos->encrypted_aes_key) :  Api::message(421,"encrypted_aes_key: null","error");
          $initial_vector = isset($datos-> initial_vector) ? base64_decode($datos->initial_vector) :  Api::message(421,"initial_vector: null","error");
          
          
          // mandamos a llamar clave privcada
          $privatePem = "file://".__DIR__.DIRECTORY_SEPARATOR."keys\private.pem";
          $privateKey = openssl_get_privatekey(file_get_contents($privatePem), 'ealmazan');

}


  
     
}

?>

