<?php

// require the parent class
require "CvSectionController.php";

class HobbySectionController extends CvSectionController{
    // save a hobby
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["hobby_name"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newHobbyName = new Input($_POST["hobby_name"]);
                    $newHobbyName->Sanitize();

                    // check to see if the user already saved this hobby
                    $oldHobbyName = new Input("");
                    if(isset($_POST["old_hobby_name"])){
                        $oldHobbyName->value = $_POST["old_hobby_name"];
                        $oldHobbyName->Sanitize();
                    } else{
                        $oldHobbyName->value = null;
                    }

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // save the hobby
                    $this->sectionModel->InsertData(array("user_id" => $userId, "new_hobby_name" => $newHobbyName->value, "old_hobby_name" => $oldHobbyName->value));

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

    // delete a hobby
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_hobby_name"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $oldHobbyName = new Input($_POST["old_hobby_name"]);
                    $oldHobbyName->Sanitize();

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete the hobby
                    $this->sectionModel->DeleteData(array("user_id" => $userId, "old_hobby_name" => $oldHobbyName->value));

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

    // add another hobby subsection
    public function AddSubsecToSec(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{
                // sanitize the user's input
                $hobbiesNumber = new Input($_POST["subsecs_in_section"]);
                $hobbiesNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $hobbiesNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='hobby' id='hobby_" . strval($hobbiesNumber->value + 1) . "'>
                        <form id='save_hobby_" . strval($hobbiesNumber->value + 1) . "_section_form'>
                            <input type='text' name='hobby_name' placeholder='Hobby'>
                            <button type='submit' onclick=" . '"'. "ModifySection('hobby_" . strval($hobbiesNumber->value + 1) . "', 'save', 'HobbySectionController')" . '"'. ">Save Hobby</button>
                        </form>
                        <form id='delete_hobby_" . strval($hobbiesNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('hobby_" . strval($hobbiesNumber->value + 1) . "', 'delete', 'HobbySectionController')" . '"' . ">Delete Hobby</button>
                        </form>
                    </div>";

                    // indicate that the action has completed
                    $response_array["action_completed"] = true;
                } else{
                    $response_array["error"] = "Invalid data";
                }
            } catch (Exception $e) {
                $response_array["error"] = $e->getMessage();
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
    $account = new HobbySectionController("HobbySectionModel");
    $account->performAction($action->value);
}

?>