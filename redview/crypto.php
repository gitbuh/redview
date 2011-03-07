<?php
/**
    RedView Encryption. Provides two-way encryption using DES3.
*/
class RedView_Crypto {

  /**
   * Whether encryption is enabled.
   * 
   * FIXME: Things will currently break if encryption is not enabled.
   * 
   * @var bool
   */
  public $enabled = true;
  
  /**
   * Application-specific secret password. Override in app.ini. 
   * 
   * @var string
   */
  public $password='change_me';

  /**
   * Encrypt some text
   * 
   * @param string $text
   * 		Text to encrypt
   * 
   * @param string $initVector
   * 		Initialization vector
   * 
   * @return string
   * 		Base-64 encoded encrypted text
   */
  public function encrypt ($text, $initVector) {
    if (!$this->enabled) return $text;
    return openssl_encrypt($text, 'DES3', $this->password, false, $initVector);
  }
  
  /**
   * Decrypt base64 encoded encrypted text
   * 
   * @param string $text
   * 		Base64-encoded string to decrypt
   * 
   * @param string $initVector
   * 		Initialization vector
   * 
   * @return string
   * 		Decrypted text
   */
  public function decrypt ($text, $initVector) {
    if (!$this->enabled) return $text;
    return openssl_decrypt($text, 'DES3', $this->password, false, $initVector);
  }
  
  
}

