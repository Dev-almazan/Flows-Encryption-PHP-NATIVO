
<?php

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;

class WhatsAppController {

   private $privateKey;

    public function __construct($private) {
         $this->privateKey = $private;
    }

    public function post($datos) {
          /*Obtenemos private key */
               $privateKey = $this->privateKey;
          /*1- Desencryptamos AES KEY*/
               $aesKey = $this->decryptedAesKey($datos,$privateKey);
          /*2- Desencryptamos Flow Data*/
               $flowData = $this->decryptFlowData($datos,$aesKey);

           /*2- Encryptamos respuesta incluyendo la pantalla a mostrar*/         
               $screen = [
               "screen" => "SCREEN_NAME",
               "data" => [
                    "some_key" => "some_value"
               ]
               ];
               $resBody = $this->encryptResponse($screen,$flowData['aesKeyBuffer'],$flowData['initialVectorBuffer']);
          /*3- Devolvemos respuesta encryptada en texto plano*/  
               echo $resBody;
               http_response_code(200);
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
                    Api::message(421,"Decryption of AES key failed.","error");
          }
          return $decryptedAesKey;
     }

     private function crearParKeysRsa()
     {
          $private = RSA::createKey(2048);
          $public = $private->getPublicKey();
          return $private." ".$public; //guardar valores en archivo env
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

     private function encryptResponse($response, $aesKeyBuffer, $initialVectorBuffer)
     {
          // Flip the initialization vector
          $flipped_iv = ~$initialVectorBuffer;
          // Encrypt the response data
          $cipher = openssl_encrypt(json_encode($response), 'aes-128-gcm', $aesKeyBuffer, OPENSSL_RAW_DATA, $flipped_iv, $tag);
          return base64_encode($cipher.$tag);
     }

  
     
}

?>

