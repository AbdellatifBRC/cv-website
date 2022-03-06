<?php

// require the parent class
require "CvSectionController.php";

class CustomSectionController extends CvSectionController{
    // save a custom section
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["custom_section_title"]) && isset($_POST["custom_section_description"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newCustomSectionTitle = new Input($_POST["custom_section_title"]);
                    $newCustomSectionTitle->Sanitize();
                    $newCustomSectionDescription = new Input($_POST["custom_section_description"]);
                    $newCustomSectionDescription->Sanitize();

                    // check to see if the user already saved this custom section
                    $oldCustomSectionTitle = new Input("");
                    if(isset($_POST["old_custom_section_title"])){
                        $oldCustomSectionTitle->value = $_POST["old_custom_section_title"];
                        $oldCustomSectionTitle->Sanitize();
                    } else{
                        $oldCustomSectionTitle->value = null;
                    }


                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // save the custom section
                    $this->sectionModel->InsertData(array("user_id" => $userId, "new_title" => $newCustomSectionTitle->value, "new_description" => $newCustomSectionDescription->value, "old_title" => $oldCustomSectionTitle->value));

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

    // delete a custom section
    public function DeleteData(){
        
    }

    // add another custom ection
    public function AddSubsecToSec(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{

                // sanitize the user's input
                $customSectionNumber = new Input($_POST["subsecs_in_section"]);
                $customSectionNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $customSectionNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='card-header'>
                        <a class='btn' data-bs-toggle='collapse' href='#collapsecustom" . strval($customSectionNumber->value + 1) . "'>
                            <input type='text' placeholder='Custom Section Title' name='custom_section_title' form='save_custom_section_" . strval($customSectionNumber->value + 1) . "_section_form'>
                        </a>
                    </div>
                    <div id='collapsecustom" . strval($customSectionNumber->value + 1) . "' class='collapse show' data-bs-parent='#accordion'>
                        <div class='card-body'>
                            <div class='custom' id='customs_" . strval($customSectionNumber->value + 1) . "'>
                                <div class='row' id='custom_section_" . strval($customSectionNumber->value + 1) . "'>
                                    <form id='save_custom_section_" . strval($customSectionNumber->value + 1) . "_section_form'>
                                        <div class='col-sm-12'>
                                            <label for='custom-description-" . strval($customSectionNumber->value + 1) . "' class='form-label'>Description</label>
                                            <textarea class='form-control' rows='3' placeholder='' name='custom_section_description' id='custom-description-" . strval($customSectionNumber->value + 1) . "'></textarea>
                                        </div>
                                        <div class='col-sm-8'>
                                            <button type='submit' onclick=" . '"' . "ModifySection('custom_section_" . strval($customSectionNumber->value + 1) . "', 'save', 'CustomSectionController')" . '"' . ">Save Section</button>
                                        </div>
                                    </form>
                                    <form id='delete_custom_section_" . strval($customSectionNumber->value + 1) . "_section_form'>
                                        <button type='submit' onclick=" . '"' . "ModifySection('custom_section_" . strval($customSectionNumber->value + 1) . "', 'delete', 'CustomSectionController')" . '"' . ">Delete Section</button>
                                    </form>
                                </div>
                                <br>
                            </div>
                        </div>
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
    $account = new CustomSectionController("CustomSectionModel");
    $account->performAction($action->value);
}

?>