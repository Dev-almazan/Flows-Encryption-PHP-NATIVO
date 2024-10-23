
<?php

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;

class WhatsAppController   {

   private $privateKey;

    public function __construct() {
         $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/');
         $env = $dotenv->load();
         $this->privateKey = $env['PRIVATE_TEST'];
    }

    public function post($datos) {
          /*Obtenemos private key */
               $privateKey = $this->privateKey;
          /*1- Desencryptamos AES KEY*/
               $aesKey = $this->decryptedAesKey($datos,$privateKey);
          /*2- Desencryptamos Flow Data*/
               $flowData = $this->decryptFlowData($datos,$aesKey);
               var_dump($flowData);
     
    }

     private function decryptedAesKey($datos,$privateKey) 
     { 
          $encryptedAesKey = isset($datos->encrypted_aes_key) ? base64_decode($datos->encrypted_aes_key) :  Api::message(421,"encrypted_aes_key: null","error");
          // Desencryptamos AES KEY creada por el cliente
          $rsa = RSA::load($privateKey)
          ->withPadding(RSA::ENCRYPTION_OAEP)
          ->withHash('sha256')
          ->withMGFHash('sha256');
          $decryptedAesKey = $rsa->decrypt($encryptedAesKey);
          if (!$decryptedAesKey) {
               throw new Exception('Decryption of AES key failed.');
          }
          return $decryptedAesKey;
     }

     private function crearParKeysRsa()
     {
          $private = RSA::createKey(2048);
          $public = $private->getPublicKey();
          return $private." ".$public;
     }


     private function decryptFlowData($datos,$aesKey) {
     
          $encryptedFlowData = isset($datos->encrypted_flow_data) ? base64_decode($datos->encrypted_flow_data) :  Api::message(421,"encrypted_flow_data: null","error");
          $initialVector = isset($datos->initial_vector) ? base64_decode($datos->initial_vector) :  Api::message(421,"initial_vector: null","error");
          // Create AES object using GCM cipher
          $aes = new AES('gcm');
          // Set key, IV, and tag length
          $aes->setKey($aesKey);
          $aes->setNonce($initialVector);
          $tagLength = 16;
          // Extract encrypted body and tag
          $encryptedFlowDataBody = substr($encryptedFlowData, 0, -$tagLength);
          $encryptedFlowDataTag = substr($encryptedFlowData, -$tagLength);
          // Set tag on AES object
          $aes->setTag($encryptedFlowDataTag);
          // Decrypt flow data
          $decrypted = $aes->decrypt($encryptedFlowDataBody);
          // Check if decryption was successful
          if (!$decrypted) {
               throw new Exception('Decryption of flow data failed.');
          }

          // Return decrypted data and context
          return [
               'decryptedBody' => json_decode($decrypted, true),
               'aesKeyBuffer' => $aesKey,
               'initialVectorBuffer' => $initialVector,
          ];
     }

  
     
}

?>

