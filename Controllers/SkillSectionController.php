<?php

// require the parent class
require "CvSectionController.php";

class SkillSectionController extends CvSectionController{
    // save a skill
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["skill_name"]) && isset($_POST["skill_level"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newSkillName = new Input($_POST["skill_name"]);
                    $newSkillName->Sanitize();
                    $newSkillLevel = new Input($_POST["skill_level"]);
                    $newSkillLevel->Sanitize();

                    // check to see if the user already saved this skill
                    $oldSkillName = new Input("");
                    $oldSkillLevel = new Input("");
                    if(isset($_POST["old_skill_name"]) && isset($_POST["old_skill_level"])){
                        $oldSkillName->value = $_POST["old_skill_name"];
                        $oldSkillName->Sanitize();
                        $oldSkillLevel->value = $_POST["old_skill_level"];
                        $oldSkillLevel->Sanitize();
                    } else{
                        $oldSkillName->value = null;
                        $oldSkillLevel->value = null;
                    }

                    // ensure a valid level format
                    if(preg_match("/^(0|[1-9][0-9]|100)$/", $newSkillLevel->value) && (preg_match("/^(0|[1-9][0-9]|100)$/", $oldSkillLevel->value) || $oldSkillLevel->value == null)){
                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // save the skill
                        $this->sectionModel->InsertData(array("user_id" => $userId, "new_skill_name" => $newSkillName->value, "new_skill_level" => $newSkillLevel->value, "old_skill_name" => $oldSkillName->value, "old_skill_level" => $oldSkillLevel->value));

                        // indicate that the action has completed
                        $response_array["action_completed"] = true;
                    } else{
                        $response_array["error"] = "Invalid level";
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

    // delete a skill
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_skill_name"]) && isset($_POST["old_skill_level"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $oldSkillName = new Input($_POST["old_skill_name"]);
                    $oldSkillName->Sanitize();
                    $oldSkillLevel = new Input($_POST["old_skill_level"]);
                    $oldSkillLevel->Sanitize();

                    // ensure a valid level format
                    if(preg_match("/^(0|[1-9][0-9]|100|)$/", $oldSkillLevel->value)){
                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // delete the skill
                        $this->sectionModel->DeleteData(array("user_id" => $userId, "old_skill_name" => $oldSkillName->value, "old_skill_level" => $oldSkillLevel->value));

                        // indicate that the action has completed
                        $response_array["action_completed"] = true;
                    } else{
                        $response_array["error"] = "Invalid level";
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

    // add another skill subsection
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
    $account = new SkillSectionController("SkillSectionModel");
    $account->performAction($action->value);
}

?>