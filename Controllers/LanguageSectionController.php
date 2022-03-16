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
                    if(preg_match("/^[0-5]$/", $newLanguageLevel->value) && (preg_match("/^[0-5]$/", $oldLanguageLevel->value) || $oldLanguageLevel->value == null)){
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
                    if(preg_match("/^[0-5]$/", $oldLanguageLevel->value)){
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

    // display the language section
    public function DisplayData(){
        $languagesAreSaved= false;
        // assign the session data sent from the curl request
        session_decode($_POST["session_data"]);

        // the default form to display
        $languageSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapseseven'>
                <h5>Languages</h5>
            </a>
        </div>
        <div id='collapseseven' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='languages' id='languages'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved languages
            $languagesDetails = $this->sectionModel->RetrieveData();

            // display the saved languages only if they exist
            if(!empty($languagesDetails)){
                $languagesAreSaved= true;
                foreach($languagesDetails as $language){
                    $languageSectionHtml .= "
                    <div class='row language' id='language_" . $language["id"] . "'>
                        <form id='save_language_" . $language["id"] . "_section_form'>
                            <div class='col-sm-12'>
                                <label for='language-" . $language["id"] . "' class='form-label'>Langue</label>
                                <input type='text' class='form-control' placeholder='' name='language_name' id='language-" . $language["id"] . "' value='" . $language["language_name"] . "'>
                                <br>
                                <label for='range1-" . $language["id"] . "' class='form-label'>Niveau</label>
                                <input type='range' class='form-range' step='1' id='range1-" . $language["id"] . "' name='language_level' min='0' max='5' value='" . $language["language_level"] . "'>
                                <button type='submit' onclick=" . '"' . "ModifySection('language_" . $language["id"] . "', 'save', 'LanguageSectionController')".'"' . ">Save Language</button>
                            </div>
                        </form>
                        <form id='delete_language_" . $language["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('language_" . $language["id"] . "', 'delete', 'LanguageSectionController')".'"' . ">Delete Language</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($languagesAreSaved=== false){
            $languageSectionHtml .= "
                    <div class='row language' id='language_1'>
                        <form id='save_language_1_section_form'>
                            <div class='col-sm-12'>
                                <label for='language-1' class='form-label'>Langue</label>
                                <input type='text' class='form-control' placeholder='' name='language_name' id='language-1'>
                                <br>
                                <label for='range1-1' class='form-label'>Niveau</label>
                                <input type='range' class='form-range' step='1' id='range1-1' name='language_level' min='0' max='5' value='1'>
                                <button type='submit' onclick=" . '"' . "ModifySection('language_1', 'save', 'LanguageSectionController')".'"' . ">Save Language</button>
                            </div>
                        </form>
                        <form id='delete_language_1_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('language_1', 'delete', 'LanguageSectionController')".'"' . ">Delete Language</button>
                            </div>
                        </form>
                    </div>
                    <br>";
        }

        $languageSectionHtml .= "
                </div>
                <form id='addsubsec_language_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('languages', 'language', 'addsubsec', 'LanguageSectionController')" . '"' . ">ajouter une langue</button>
                </form>
            </div>
        </div>";

        echo $languageSectionHtml;
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