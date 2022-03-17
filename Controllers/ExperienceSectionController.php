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
                    $newDescription->Sanitize();

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
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_position"]) && isset($_POST["old_company_name"]) && isset($_POST["old_company_location"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the input
                    $oldPosition = new Input($_POST["old_position"]);
                    $oldPosition->Sanitize();
                    $oldCompanyName = new Input($_POST["old_company_name"]);
                    $oldCompanyName->Sanitize();
                    $oldCompanyLocation = new Input($_POST["old_company_location"]);
                    $oldCompanyLocation->Sanitize();

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete the experience
                    $this->sectionModel->DeleteData(array("user_id" => $userId, "old_position" => $oldPosition->value, "old_company_name" => $oldCompanyName->value, "old_company_location" => $oldCompanyLocation->value));

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

    // add another experience subsection
    public function AddSubsecToSec(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{

                // sanitize the user's input
                $experiencesNumber = new Input($_POST["subsecs_in_section"]);
                $experiencesNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $experiencesNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='experience' id='experience_" . strval($experiencesNumber->value + 1) . "'>
                        <form id='save_experience_" . strval($experiencesNumber->value + 1) . "_section_form'>
                        <input type='text' name='position' placeholder='Position'>
                        <input type='text' name='company_name' placeholder='Company Name'>
                        <input type='text' name='company_location' placeholder='Company Location'>
                        <input type='date' class='form-control' name='position_start_date' placeholder=''>
                        <input type='date' class='form-control' name='position_end_date' placeholder=''>
                        <textarea name='experience_description' class='form-control' cols='30' rows='5' ></textarea>
                            <button type='submit' onclick=" . '"'. "ModifySection('experience_" . strval($experiencesNumber->value + 1) . "', 'save', 'ExperienceSectionController')" . '"'. ">Save Experience</button>
                        </form>
                        <form id='delete_experience_" . strval($experiencesNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('experience_" . strval($experiencesNumber->value + 1) . "', 'delete', 'ExperienceSectionController')" . '"' . ">Delete Experience</button>
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

    // display the experience section
    public function DisplayData(){
        $experiencesAreSaved = false;
        // assign the session data sent from the curl request
        session_decode($_POST["session_data"]);

        // the default form to display
        $experienceSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapsethree'>
                <h5>Experiences professionelles</h5>
            </a>
        </div>
        <div id='collapsethree' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='exp-pro' id='exp-pro'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved experiences
            $experiencesDetails = $this->sectionModel->RetrieveData();

            // display the saved experiences only if they exist
            if(!empty($experiencesDetails)){
                $experiencesAreSaved = true;
                foreach($experiencesDetails as $experience){
                    $experienceSectionHtml .= "
                    <div class='row experience' id='experience_" . $experience["id"] . "'>
                        <form id='save_experience_" . $experience["id"] . "_section_form'>
                            <div class='col-sm-3'>
                                <label for='position-" . $experience["id"] . "' class='form-label'>Position</label>
                                <input type='text' class='form-control' id='position-" . $experience["id"] . "' name='position' value='" . str_replace("'", "&apos;", str_replace('"', "&quot;", $experience["position"])) . "'>
                                <br>
                                <label for='company-name-" . $experience["id"] . "' class='form-label'>Company Name</label>
                                <input type='text' class='form-control' id='company-name-" . $experience["id"] . "' name='company_name' value='" . str_replace("'", "&apos;", str_replace('"', "&quot;", $experience["company_name"])) . "'>
                                <br>
                                <label for='company-location-" . $experience["id"] . "' class='form-label'>Company Location</label>
                                <input type='text' class='form-control' id='company-location-" . $experience["id"] . "' name='company_location' value='" . str_replace("'", "&apos;", str_replace('"', "&quot;", $experience["company_location"])) . "'>
                            </div>
                            <div class='col-sm-3'>
                                <label for='exp-date-debut-" . $experience["id"] . "' class='form-label'>Date début l'experience</label>
                                <input type='date' class='form-control' id='exp-date-debut-" . $experience["id"] . "' name='position_start_date' value='" . $experience["start_date"] . "'>
                                <br>
                                <label for='exp-date-fin-" . $experience["id"] . "' class='form-label'>Date fin l'experience</label>
                                <input type='date' class='form-control' id='exp-date-fin-" . $experience["id"] . "' name='position_end_date' value='" . $experience["end_date"] . "'>
                            </div>
                            <div class='col-sm-8'>
                                <label for='exp-" . $experience["id"] . "' class='form-label'>Description</label>
                                <textarea class='form-control' rows='3' id='exp-" . $experience["id"] . "' name='experience_description' placeholder='Ex : ingénieur en systemes embarquées' name='text' >" . $experience["description"] . "</textarea>
                                <button onclick=" . '"' . "ModifySection('experience_" . $experience["id"] . "', 'save', 'ExperienceSectionController')".'"' . ">Save Experience</button>
                                <br>
                            </div>
                        </form>
                        <form id='delete_experience_" . $experience["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('experience_" . $experience["id"] . "', 'delete', 'ExperienceSectionController')".'"' . ">Delete Experience</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($experiencesAreSaved === false){
            $experienceSectionHtml .= "
                    <div class='row experience' id='experience_1'>
                        <form id='save_experience_1_section_form'>
                            <div class='col-sm-3'>
                                <label for='position-1' class='form-label'>Position</label>
                                <input type='text' class='form-control' id='position-1' name='position'>
                                <br>
                                <label for='company-name-1' class='form-label'>Company Name</label>
                                <input type='text' class='form-control' id='company-name-1' name='company_name'>
                                <br>
                                <label for='company-location-1' class='form-label'>Company Location</label>
                                <input type='text' class='form-control' id='company-location-1' name='company_location'>
                            </div>
                            <div class='col-sm-3'>
                                <label for='exp-date-debut-1' class='form-label'>Date début l'experience</label>
                                <input type='date' class='form-control' id='exp-date-debut-1' name='position_start_date'>
                                <br>
                                <label for='exp-date-fin-1' class='form-label'>Date fin l'experience</label>
                                <input type='date' class='form-control' id='exp-date-fin-1' name='position_end_date'>
                            </div>
                            <div class='col-sm-8'>
                                <label for='exp-1' class='form-label'>Description</label>
                                <textarea class='form-control' rows='3' id='exp-1' name='experience_description' placeholder='Ex : ingénieur en systemes embarquées' name='text' ></textarea>
                                <button onclick=" . '"' . "ModifySection('experience_1', 'save', 'ExperienceSectionController')".'"' . ">Save Experience</button>
                                <br>
                            </div>
                        </form>
                        <form id='delete_experience_1_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('experience_1', 'delete', 'ExperienceSectionController')".'"' . ">Delete Experience</button>
                            </div>
                        </form>
                    </div>
                    <br>";
        }

        $experienceSectionHtml .= "
                </div>
                <form id='addsubsec_experience_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('exp-pro', 'experience', 'addsubsec', 'ExperienceSectionController')" . '"' . ">ajouter une formation</button>
                </form>
            </div>
        </div>";

        echo $experienceSectionHtml;
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