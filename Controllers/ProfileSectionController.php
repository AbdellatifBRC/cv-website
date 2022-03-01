<?php

// require the parent class
require "CvSectionController.php";

class ProfileSectionController extends CvSectionController{
    // insert data to the profile_section table
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["profile_description"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $profileDescription = new Input($_POST["profile_description"]);
                    $profileDescription->Sanitize();

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // insert data
                    $this->sectionModel->InsertData(array("user_id" => $userId, "description" => $profileDescription->value));

                    // indicate that the action has completed
                    $response_array["action_completed"] = true;
                } else{
                    $response_array["logged_in"] = false;
                }
            } catch (Exception $e) {
                $response_array["error"] = $e->getMessage();
            }
        }

        echo json_encode($response_array);
    }

    // delete data from the profile_section table
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete data
                    $this->sectionModel->DeleteData(array("user_id" => $userId));

                    // indicate that the action has completed
                    $response_array["action_completed"] = true;
                } else{
                    $response_array["logged_in"] = false;
                }
            } catch (Exception $e) {
                $response_array["error"] = $e->getMessage();
            }
        }

        echo json_encode($response_array);
    }

    // users can not another profile section, so just display an error
    public function AddSubsecToSec(){
        echo json_encode(array("error" => "You can not add another profile section"));
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
    $account = new ProfileSectionController("ProfileSectionModel");
    $account->performAction($action->value);
}

?>