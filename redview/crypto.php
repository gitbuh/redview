<?php
/**
    RedView Encryption. Provides two-way encryption using DES3.
*/
class RedView_Crypto {

  public $enabled = true;
  public $password='1234';

  /**
      Encrypt some text
      @param string $text to encrypt
  */
  public function encrypt ($text, $initVector='12345678') {
    if (!$this->enabled) return $text;
    return openssl_encrypt($text, 'DES3', $this->password, false, $initVector);
  }
  
  /**
      Decrypt base64 encoded encrypted text
      @param string $text base64-encoded string to decrypt
  */
  public function decrypt ($text, $initVector='12345678') {
    if (!$this->enabled) return $text;
    return openssl_decrypt($text, 'DES3', $this->password, false, $initVector);
  }
  
  
}

