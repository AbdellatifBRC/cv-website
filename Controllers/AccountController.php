<?php

class AccountController{
    private $dbconnector;
    private $dbconn;

    public function __construct(){
        require_once "../Config/DbConnect.php";

        $this->dbconnector = new DbConnect();
        $this->dbconn = $this->dbconnector->connect();
    }

    public function performAction($action){
        switch($action){
            case "signup":
                $this->SignUp();
                break;
            case "confirmemail":
                $this->ConfirmEmail();
                break;
            default:
                die("default");
                break;
        }
    }

    public function SignUp(){
        // require the authentication library
        require '../Libraries/AuthLib/vendor/autoload.php';
        $auth = new \Delight\Auth\Auth($this->dbconn);

        // this array will be sent as a response to the client
        $response_array["already_loggedin"] = false;
        $response_array["email_error"] = "";
        $response_array["password_error"] = "";
        $response_array["requests_error"] = "";
        $response_array["signed_up"] = false;

        // check if the user is logged in
        if ($auth->isLoggedIn()) {
            $response_array["already_loggedin"] = true;
        } else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["psw"]) && isset($_POST["psw-repeat"])){
            // sanitize the user's input
            $email = new Input($_POST["email"]);
            $email->Sanitize();
            $password = new Input($_POST["psw"]);
            $password->Sanitize();
            $password_repeat = new Input($_POST["psw-repeat"]);
            $password_repeat->Sanitize();

            // ensure valid password length
            if(strlen($password->value) < 8 || strlen($password->value) > 255){
                $response_array["password_error"] = "Your password must be between 8 and 255 characters long<br><br>";
            }
            // ensure identical password fields
            else if(!empty($password->value) && $password->value !== $password_repeat->value){
                $response_array["password_error"] = "Your passwords do not match<br><br>";
            } else{
                try {
                    $userId = $auth->register($email->value, $password->value, null, function ($selector, $token) use ($email, &$response_array){
                        // prepare the necessary variables to send an email
                        $to = $email->value;
                        $subject = "CV Website Email Verification";
                        $message = " Thank you for choosing us! Please confirm your email using the following link: \n
                        http://localhost/cv-website/Views/EmailConfirmationView.html?selector=" . $selector . "&token=" . $token;
                        $headers = 'From:arthurmorganredemption28@gmail.com' . "\r\n"; 
                        // send the verification email to the user
                        mail($to, $subject, $message, $headers);

                        // indicate that the user has successfully signed yp
                        $response_array["signed_up"] = true;
                    });
                } catch (\Delight\Auth\InvalidEmailException $e) {
                    $response_array["email_error"] = "Invalid email address<br><br>";
                } catch (\Delight\Auth\InvalidPasswordException $e) {
                    $response_array["password_error"] = "Invalid password<br><br>";
                } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                    $response_array["email_error"] = "User already exists<br><br>";
                } catch (\Delight\Auth\TooManyRequestsException $e) {
                    $response_array["requests_error"] = "Too many requests<br><br>";
                }
            }
        }

        echo json_encode($response_array);
    }

    public function ConfirmEmail(){
        // require the authentication library
        require '../Libraries/AuthLib/vendor/autoload.php';
        $auth = new \Delight\Auth\Auth($this->dbconn);

        // this array will be sent as a response to the client
        $response_array["already_loggedin"] = false;
        $response_array["error"] = "";
        $response_array["email_confirmed"] = false;

        // check if the user is logged in
        if($auth->isLoggedIn()){
            $response_array["already_loggedin"] = true;
        } else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["token"]) && isset($_POST["selector"])){
            // sanitize the input
            $token = new Input($_POST["token"]);
            $token->Sanitize();
            $selector = new Input($_POST["selector"]);
            $selector->Sanitize();

            try {
                $auth->confirmEmail($selector->value, $token->value);
            
                $response_array["email_confirmed"] = true;
            } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
                $response_array["error"] = "Invalid token";
            } catch (\Delight\Auth\TokenExpiredException $e) {
                $response_array["error"] = "Token expired";
            } catch (\Delight\Auth\UserAlreadyExistsException $e) {
                $response_array["error"] = "Email address already exists";
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                $response_array["error"] = "Too many requests";
            }
        }

        echo json_encode($response_array);
    }
}

// a request has been sent from a view
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])){
    // require the input sanitizer
    require "InputController.php";

    // retrieve the action
    $action = new Input($_POST["action"]);
    $action->Sanitize();

    // perform the action
    $account = new AccountController();
    $account->performAction($action->value);
}

?>