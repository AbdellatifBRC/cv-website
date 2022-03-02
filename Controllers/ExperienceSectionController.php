<?php

// require the parent class
require "CvSectionController.php";

class ExperienceSectionController extends CvSectionController{
    // save an experience
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";

        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["position"]) && isset($_POST["company_name"]) && isset($_POST["company_location"]) && isset($_POST["position_start_date"]) && isset($_POST["position_end_date"]) && isset($_POST["experience_description"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // sanitize the user's input
                    $newPosition = new Input($_POST["position"]);
                    $newPosition->Sanitize();
                    $newCompanyName = new Input($_POST["company_name"]);
                    $newCompanyName->Sanitize();
                    $newCompanyLocation = new Input($_POST["company_location"]);
                    $newCompanyLocation->Sanitize();
                    $newStartDate = new Input($_POST["position_start_date"]);
                    $newStartDate->Sanitize();
                    $newEndDate = new Input($_POST["position_end_date"]);
                    $newEndDate->Sanitize();
                    $newDescription = new Input($_POST["experience_description"]);
                    $newPosition->Sanitize();

                    // check to see if the user already saved this experience
                    $oldPosition = new Input("");
                    $oldCompanyName = new Input("");
                    $oldCompanyLocation = new Input("");
                    if(isset($_POST["old_position"]) && isset($_POST["old_company_name"]) && isset($_POST["old_company_location"])){
                        // sanitize the input
                        $oldPosition->value = $_POST["old_position"];
                        $oldPosition->Sanitize();
                        $oldCompanyName->value = $_POST["old_company_name"];
                        $oldCompanyName->Sanitize();
                        $oldCompanyLocation->value = $_POST["old_company_location"];
                        $oldCompanyLocation->Sanitize();
                    } else{
                        $oldPosition->value = null;
                        $oldCompanyName->value = null;
                        $oldCompanyLocation->value = null;
                    }

                    // check if the dates are valid
                    if(date("Y-m-d", strtotime($newStartDate->value)) == date($newStartDate->value) && date("Y-m-d", strtotime($newEndDate->value)) == date($newEndDate->value)){
                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // save the experience
                        $this->sectionModel->InsertData(array("user_id" => $userId, "new_position" => $newPosition->value, "new_company_name" => $newCompanyName->value, "new_company_location" => $newCompanyLocation->value, "new_start_date" => $newStartDate->value, "new_end_date" => $newEndDate->value, "new_description" => $newDescription->value, "old_position" => $oldPosition->value, "old_company_name" => $oldCompanyName->value, "old_company_location" => $oldCompanyLocation->value));

                        // indicate that the action has completed
                        $response_array["action_completed"] = true;
                    } else{
                        $response_array["error"] = "Invalid dates";
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

    // delete an experience
    public function DeleteData(){

    }

    // add another experience subsection
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
    $account = new ExperienceSectionController("ExperienceSectionModel");
    $account->performAction($action->value);
}

?>