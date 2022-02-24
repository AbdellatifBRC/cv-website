<?php

class AccountModel{
    private $dbconnector;
    private $dbconn;
    public $auth;

    public function __construct(){
        // connect to db
        require_once "../Config/DbConnect.php";
        $this->dbconnector = new DbConnect();
        $this->dbconn = $this->dbconnector->connect();

        // require the authentication library
        require '../Libraries/AuthLib/vendor/autoload.php';
        $this->auth = new \Delight\Auth\Auth($this->dbconn);
    }

    // delete an email confirmation request
    public function DeleteConfirmationReq($email, $selector, $token){
        $stmt = $this->dbconn->prepare("DELETE FROM users_confirmations WHERE email = :email AND selector != :selector AND token != :token");
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":selector", $selector);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
}

?>