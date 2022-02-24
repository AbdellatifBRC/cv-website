<?php

class AccountController{
    private $accountModel;

    public function __construct(){
        // create an account account model
        require "../Models/AccountModel.php";
        $this->accountModel = new AccountModel();
    }

    public function performAction($action){
        switch($action){
            case "signup":
                $this->SignUp();
                break;
            case "confirmemail":
                $this->ConfirmEmail();
                break;
            case "resendconfirmationemail":
                $this->ResendConfirmationReq();
                break;
            case "signin":
                $this->SignIn();
                break;
            default:
                die("default");
                break;
        }
    }

    // sign up users
    public function SignUp(){
        // this array will be sent as a response to the client
        $response_array["already_loggedin"] = false;
        $response_array["email_error"] = "";
        $response_array["password_error"] = "";
        $response_array["requests_error"] = "";
        $response_array["signed_up"] = false;

        // check if the user is logged in
        if ($this->accountModel->auth->isLoggedIn()) {
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
                    $userId = $this->accountModel->auth->register($email->value, $password->value, null, function ($selector, $token) use ($email, &$response_array){
                        // prepare the necessary variables to send an email
                        $to = $email->value;
                        $subject = "CV Website Email Verification";
                        $message = " Thank you for choosing us! Please confirm your email using the following link: \n
                        http://localhost/cv-website/Views/EmailConfirmationView.html?selector=" . $selector . "&token=" . $token;
                        $headers = 'From:arthurmorganredemption28@gmail.com' . "\r\n"; 
                        // send the verification email to the user
                        mail($to, $subject, $message, $headers);

                        // indicate that the user has successfully signed up
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

    // confirm an email (activate an account)
    public function ConfirmEmail(){
        // this array will be sent as a response to the client
        $response_array["already_loggedin"] = false;
        $response_array["error"] = "";
        $response_array["email_confirmed"] = false;

        // check if the user is logged in
        if($this->accountModel->auth->isLoggedIn()){
            $response_array["already_loggedin"] = true;
        } else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["token"]) && isset($_POST["selector"])){
            // sanitize the input
            $token = new Input($_POST["token"]);
            $token->Sanitize();
            $selector = new Input($_POST["selector"]);
            $selector->Sanitize();

            try {
                $this->accountModel->auth->confirmEmail($selector->value, $token->value);
            
                // indicate that the email has been successfully confirmed
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

    // resend a confirmation email
    public function ResendConfirmationReq(){
        // this array will be sent as a response to the client
        $response_array["already_loggedin"] = false;
        $response_array["error"] = "";
        $response_array["confirmation_resent"] = false;

        // check if the user is logged in
        if($this->accountModel->auth->isLoggedIn()){
            $response_array["already_loggedin"] = true;
        } else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])){
            // sanitize the input
            $email = new Input($_POST["email"]);
            $email->Sanitize();

            try {
                $this->accountModel->auth->resendConfirmationForEmail($email->value, function ($selector, $token) use ($email, &$response_array) {
                    // delete the old request
                    $this->accountModel->DeleteConfirmationReq($email->value, $selector, $token);

                    // prepare the necessary variables to send an email
                    $to = $email->value;
                    $subject = "CV Website Email Verification";
                    $message = " Thank you for choosing us! As per your request, this a new link to confirm your email address: \n
                    http://localhost/cv-website/Views/EmailConfirmationView.html?selector=" . $selector . "&token=" . $token;
                    $headers = 'From:arthurmorganredemption28@gmail.com' . "\r\n"; 
                    // send the verification email to the user
                    mail($to, $subject, $message, $headers);

                    // indicate that the request has been successfully re-sent
                    $response_array["confirmation_resent"] = true;
                });
            } catch (\Delight\Auth\ConfirmationRequestNotFound $e) {
                $response_array["error"] = "No earlier request has been found that could be re-sent";
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                $response_array["error"] = "There have been too many requests -- try again later";
            } catch(PDOException $e) {
                $response_array["error"] = "Db error";
            }
        }

        echo json_encode($response_array);
    }

    // sign in users
    public function SignIn(){
        // this array will be sent as a response to the client
        $response_array["error"] = "";
        $response_array["signed_in"] = false;

        // check if the user is logged in
        if ($this->accountModel->auth->isLoggedIn()) {
            $response_array["signed_in"] = true;
        } else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["psw"])){
            // sanitize the user's input
            $email = new Input($_POST["email"]);
            $email->Sanitize();
            $password = new Input($_POST["psw"]);
            $password->Sanitize();

            try {
                $this->accountModel->auth->login($email->value, $password->value);
    
                // indicate that the user has successfully signed in
                $response_array["signed_in"] = true;
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                $response_array["error"] = "Your email and password don't match<br><br>";
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                $response_array["error"] = "Your email and password don't match<br><br>";
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                $response_array["error"] = "You have to confirm your email before you can sign in<br><br>";
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                $response_array["error"] = "Too many requests<br><br>";
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