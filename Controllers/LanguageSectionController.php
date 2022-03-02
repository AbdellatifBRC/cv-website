<?php

// require the parent class
require "CvSectionController.php";

class LanguageSectionController extends CvSectionController{
    // save a language
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["language_name"]) && isset($_POST["language_level"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newLanguageName = new Input($_POST["language_name"]);
                    $newLanguageName->Sanitize();
                    $newLanguageLevel = new Input($_POST["language_level"]);
                    $newLanguageLevel->Sanitize();

                    // check to see if the user already saved this language
                    $oldLanguageName = new Input("");
                    $oldLanguageLevel = new Input("");
                    if(isset($_POST["old_language_name"]) && isset($_POST["old_language_level"])){
                        $oldLanguageName->value = $_POST["old_language_name"];
                        $oldLanguageName->Sanitize();
                        $oldLanguageLevel->value = $_POST["old_language_level"];
                        $oldLanguageLevel->Sanitize();
                    } else{
                        $oldLanguageName->value = null;
                        $oldLanguageLevel->value = null;
                    }

                    // ensure a valid level format
                    if(preg_match("/^(0|[1-9][0-9]|100)$/", $newLanguageLevel->value) && (preg_match("/^(0|[1-9][0-9]|100)$/", $oldLanguageLevel->value) || $oldLanguageLevel->value == null)){
                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // save the language
                        $this->sectionModel->InsertData(array("user_id" => $userId, "new_language_name" => $newLanguageName->value, "new_language_level" => $newLanguageLevel->value, "old_language_name" => $oldLanguageName->value, "old_language_level" => $oldLanguageLevel->value));

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

    // delete a language
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_language_name"]) && isset($_POST["old_language_level"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $oldLanguageName = new Input($_POST["old_language_name"]);
                    $oldLanguageName->Sanitize();
                    $oldLanguageLevel = new Input($_POST["old_language_level"]);
                    $oldLanguageLevel->Sanitize();

                    // ensure a valid level format
                    if(preg_match("/^(0|[1-9][0-9]|100|)$/", $oldLanguageLevel->value)){
                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // delete the language
                        $this->sectionModel->DeleteData(array("user_id" => $userId, "old_language_name" => $oldLanguageName->value, "old_language_level" => $oldLanguageLevel->value));

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

    // add another language subsection
    public function AddSubsecToSec(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{

                // sanitize the user's input
                $languagesNumber = new Input($_POST["subsecs_in_section"]);
                $languagesNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $languagesNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='language' id='language_" . strval($languagesNumber->value + 1) . "'>
                        <form id='save_language_" . strval($languagesNumber->value + 1) . "_section_form'>
                            <input type='text' name='language_name' placeholder='Language'>
                            <input type='text' name='language_level' placeholder='Language Level'>
                            <button type='submit' onclick=" . '"'. "ModifySection('language_" . strval($languagesNumber->value + 1) . "', 'save', 'LanguageSectionController')" . '"'. ">Save Language</button>
                        </form>
                        <form id='delete_language_" . strval($languagesNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('language_" . strval($languagesNumber->value + 1) . "', 'delete', 'LanguageSectionController')" . '"' . ">Delete Language</button>
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
    $account = new LanguageSectionController("LanguageSectionModel");
    $account->performAction($action->value);
}

?>