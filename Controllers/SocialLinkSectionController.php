<?php

// require the parent class
require "CvSectionController.php";

class SocialLinkSectionController extends CvSectionController{
    // save a social link
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["website_name"]) && isset($_POST["website_link"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newSocialLinkName = new Input($_POST["website_name"]);
                    $newSocialLinkName->Sanitize();
                    $newSocialLink = new Input($_POST["website_link"]);
                    $newSocialLink->Sanitize();

                    // ensure a valid link
                    if(filter_var($newSocialLink->value, FILTER_VALIDATE_URL)){
                        // check to see if the user already saved this social link
                        $oldSocialLinkName = new Input("");
                        if(isset($_POST["old_website_name"])){
                            $oldSocialLinkName->value = $_POST["old_website_name"];
                            $oldSocialLinkName->Sanitize();
                        } else{
                            $oldSocialLinkName->value = null;
                        }


                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // save the social link
                        $this->sectionModel->InsertData(array("user_id" => $userId, "new_website_name" => $newSocialLinkName->value, "new_link" => $newSocialLink->value, "old_website_name" => $oldSocialLinkName->value));

                        // indicate that the action has completed
                        $response_array["action_completed"] = true;
                    } else{
                        $response_array["error"] = "Invalid Link";
                    }
                } else{
                    $response_array["logged_in"] = false;
                }
            } catch (Exception $e) {
                $response_array["error"] = $e->getMessage();
            }
        }

        echo json_encode($response_array);
    }

    // delete a social link
    public function DeleteData(){
        
    }

    // add another social link subsection
    public function AddSubsecToSec(){
        
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
    $account = new SocialLinkSectionController("SocialLinkSectionModel");
    $account->performAction($action->value);
}

?>