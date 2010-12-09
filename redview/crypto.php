<?php
/**
    RedView Encryption. Provides two-way encryption using DES3.
*/
class RedView_Crypto {

  public $enabled = false;
  public $password='1234';
  public $initVector='qwertyuiopasdfghjklzxcvbnm';

  /**
      Encrypt some text
      @param string $text to encrypt
  */
  public function encrypt ($text) {
    $r = openssl_encrypt($text, 'DES3', $this->password, false, $this->initVector);
    return $r;
  }
  
  /**
      Decrypt base64 encoded encrypted text
      @param string $text base64-encoded string to decrypt
  */
  public function decrypt ($text) {
    $r = openssl_decrypt($text, 'DES3', $this->password, false, $this->initVector);
    return $r;
  }
  
}

