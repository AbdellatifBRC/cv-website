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
                    if(preg_match("/^[0-5]$/", $newSkillLevel->value) && (preg_match("/^[0-5]$/", $oldSkillLevel->value) || $oldSkillLevel->value == null)){
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
                    if(preg_match("/^[0-5]$/", $oldSkillLevel->value)){
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
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{
                // sanitize the user's input
                $skillsNumber = new Input($_POST["subsecs_in_section"]);
                $skillsNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $skillsNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='skill' id='skill_" . strval($skillsNumber->value + 1) . "'>
                        <form id='save_skill_" . strval($skillsNumber->value + 1) . "_section_form'>
                            <input type='text' name='skill_name' placeholder='Skill'>
                            <input type='text' name='skill_level' placeholder='Skill Level'>
                            <button type='submit' onclick=" . '"'. "ModifySection('skill_" . strval($skillsNumber->value + 1) . "', 'save', 'SkillSectionController')" . '"'. ">Save Skill</button>
                        </form>
                        <form id='delete_skill_" . strval($skillsNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('skill_" . strval($skillsNumber->value + 1) . "', 'delete', 'SkillSectionController')" . '"' . ">Delete Skill</button>
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

    // display the skill section
    public function DisplayData(){
        $skillsAreSaved= false;
        // assign the session data sent from the curl request
        session_decode($_POST["session_data"]);

        // the default form to display
        $skillSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapsefive'>
                <h5>Compétences</h5>
            </a>
        </div>
        <div id='collapsefive' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='skills' id='skills'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved skills
            $skillsDetails = $this->sectionModel->RetrieveData();

            // display the saved skills only if they exist
            if(!empty($skillsDetails)){
                $skillsAreSaved= true;
                foreach($skillsDetails as $skill){
                    $skillSectionHtml .= "
                    <div class='row skill' id='skill_" . $skill["id"] . "'>
                        <form id='save_skill_" . $skill["id"] . "_section_form'>
                            <div class='col-sm-12'>
                                <label for='skill-" . $skill["id"] . "' class='form-label'>Compétence</label>
                                <input type='text' class='form-control' placeholder='' id='skill-" . $skill["id"] . "' name='skill_name' value='" . str_replace("'", "&apos;", str_replace('"', "&quot;", $skill["skill_name"])) . "'>
                                <br>
                                <label for='range-" . $skill["id"] . "' class='form-label'>Niveau</label>
                                <input type='range' class='form-range' step='1' id='range-" . $skill["id"] . "' name='skill_level' min='0' max='5' value='" . $skill["skill_level"] . "'>
                                <button type='submit' onclick=" . '"' . "ModifySection('skill_" . $skill["id"] . "', 'save', 'SkillSectionController')".'"' . ">Save Skill</button>
                            </div>
                        </form>
                        <form id='delete_skill_" . $skill["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('skill_" . $skill["id"] . "', 'delete', 'SkillSectionController')".'"' . ">Delete Skill</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($skillsAreSaved=== false){
            $skillSectionHtml .= "
                <div class='row skill' id='skill_1'>
                    <form id='save_skill_1_section_form'>
                        <div class='col-sm-12'>
                            <label for='skill-1' class='form-label'>Compétence</label>
                            <input type='text' class='form-control' placeholder='' id='skill-1' name='skill_name'>
                            <br>
                            <label for='range-1' class='form-label'>Niveau</label>
                            <input type='range' class='form-range' step='1' id='range-1' name='skill_level' min='0' max='5' value='1'>
                            <button type='submit' onclick=" . '"' . "ModifySection('skill_1', 'save', 'SkillSectionController')".'"' . ">Save Skill</button>
                        </div>
                    </form>
                    <form id='delete_skill_1_section_form'>
                        <div class='col-sm-9'>
                            <button type='submit' onclick=" . '"' . "ModifySection('skill_1', 'delete', 'SkillSectionController')".'"' . ">Delete Skill</button>
                        </div>
                    </form>
                </div>
                <br>";
        }

        $skillSectionHtml .= "
                </div>
                <form id='addsubsec_skill_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('skills', 'skill', 'addsubsec', 'SkillSectionController')" . '"' . ">ajouter une formation</button>
                </form>
            </div>
        </div>";

        echo $skillSectionHtml;
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