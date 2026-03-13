<?php 
  class User{
    private $fullName;
    private $email;
    private $phoneNumber;
    private $passwordHash;
    private $preferredLanguage;

    // getters
    function getFullName() { return $this->fullName; }
    function getEmail() { return $this->email; }
    function getPhoneNumber() { private $this->phoneNumber; }
    function getPasswordHash() { private $this->passwordHash; }
    function getPreferredLanguage() { private $this->preferredLanguage; }
    //setters
    function setFullName($fullName){ $this->fullName = $fullName; }
    function setEmail($email){ $this->email = $email; }
    function setPhoneNumber($phoneNumber){ $this->phoneNumber = $phoneNumber; }
    function setPasswordHash($passwordHash){ $this->passwordHash = $passwordHash; }
    function setPreferredLanguage($preferredLanguage){ $this->preferredLanguage = $preferredLanguage; }

  }
?>
