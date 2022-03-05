<?php

// require the parent class
require "CvSectionController.php";

class SideProjetSectionController extends CvSectionController{
    // save a side project
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["side_project_title"]) && isset($_POST["side_project_description"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newSideProjetTitle = new Input($_POST["side_project_title"]);
                    $newSideProjetTitle->Sanitize();
                    $newSideProjetDescription = new Input($_POST["side_project_description"]);
                    $newSideProjetDescription->Sanitize();

                    // check to see if the user already saved this side project
                    $oldSideProjetTitle = new Input("");
                    if(isset($_POST["old_side_project_title"])){
                        $oldSideProjetTitle->value = $_POST["old_side_project_title"];
                        $oldSideProjetTitle->Sanitize();
                    } else{
                        $oldSideProjetTitle->value = null;
                    }

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // save the side project
                    $this->sectionModel->InsertData(array("user_id" => $userId, "new_title" => $newSideProjetTitle->value, "new_description" => $newSideProjetDescription->value, "old_title" => $oldSideProjetTitle->value));

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

    // delete a side project
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_side_project_title"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $oldSideProjectTitle = new Input($_POST["old_side_project_title"]);
                    $oldSideProjectTitle->Sanitize();

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete the side project
                    $this->sectionModel->DeleteData(array("user_id" => $userId, "old_title" => $oldSideProjectTitle->value));

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

    // add another side project subsection
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
    $account = new SideProjetSectionController("SideProjectSectionModel");
    $account->performAction($action->value);
}

?>