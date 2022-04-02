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
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{

                // sanitize the user's input
                $sideProjectsNumber = new Input($_POST["subsecs_in_section"]);
                $sideProjectsNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $sideProjectsNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='row project' id='project_" . strval($sideProjectsNumber->value + 1) . "'>
                        <form id='save_project_" . strval($sideProjectsNumber->value + 1) . "_section_form'>
                            <div class'col-sm-4'>
                                <label for='project-title-" . strval($sideProjectsNumber->value + 1) . "' class='form-label'>titre de projet</label>
                                <input type='text' class='form-control cv-input-1' placeholder='' name='side_project_title' id='project-title-" . strval($sideProjectsNumber->value + 1) . "'>
                            </div>
                            <div class'col-sm-4'>
                                <label for='project-" . strval($sideProjectsNumber->value + 1) . "' class='form-label'>description</label>
                                <textarea class='form-control cv-input-1' rows='3' id='project-" . strval($sideProjectsNumber->value + 1) . "' placeholder='' name='side_project_description'></textarea>
                            </div>
                            <div class'col-sm-4'>
                                <button type='submit' onclick=" . '"'. "ModifySection('project_" . strval($sideProjectsNumber->value + 1) . "', 'save', 'SideProjectSectionController')" . '"'. ">Save Project</button>
                            </div>
                        </form>
                        <form id='delete_project_" . strval($sideProjectsNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('project_" . strval($sideProjectsNumber->value + 1) . "', 'delete', 'SideProjectSectionController')" . '"' . ">Delete Project</button>
                        </form>
                    </div>
                    <br>";

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

    // display the side project section
    public function DisplayData(){
        $hobbiesAreSaved= false;
        // assign the session data sent from the curl request
        if($_POST["session_data"]){
            session_decode($_POST["session_data"]);
        }

        // the default form to display
        $sideProjectSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapsenine'>
                <h5>Projets</h5>
            </a>
        </div>
        <div id='collapsenine' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='projects' id='projects'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved hobbies
            $hobbiesDetails = $this->sectionModel->RetrieveData();

            // display the saved hobbies only if they exist
            if(!empty($hobbiesDetails)){
                $hobbiesAreSaved= true;
                foreach($hobbiesDetails as $sideProject){
                    $sideProjectSectionHtml .= "
                    <div class='row side_project' id='side_project_" . $sideProject["id"] . "'>
                        <form id='save_side_project_" . $sideProject["id"] . "_section_form'>
                            <div class='col-sm-4'>
                                <label for='project-title-" . $sideProject["id"] . "' class='form-label'>titre de projet</label>
                                <input type='text' class='form-control cv-input-1' placeholder='' name='side_project_title' id='project-title-" . $sideProject["id"] . "' value='" . str_replace("'", "&apos;", str_replace('"', "&quot;", $sideProject["title"])) . "'>
                            </div>
                            <div class='col-sm-8'>
                                <label for='project-" . $sideProject["id"] . "' class='form-label'>projet</label>
                                <textarea class='form-control cv-input-1' rows='5' id='project-" . $sideProject["id"] . "' name='side_project_description'>" . $sideProject["description"] . "</textarea>
                                <button type='submit' onclick=" . '"' . "ModifySection('side_project_" . $sideProject["id"] . "', 'save', 'SideProjectSectionController')".'"' . ">Save Side Project</button>
                            </div>
                        </form>
                        <form id='delete_side_project_" . $sideProject["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('side_project_" . $sideProject["id"] . "', 'delete', 'SideProjectSectionController')".'"' . ">Delete Side Project</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($hobbiesAreSaved=== false){
            $sideProjectSectionHtml .= "
                    <div class='row side_project' id='side_project_1'>
                        <form id='save_side_project_1_section_form'>
                            <div class='col-sm-4'>
                                <label for='project-title-1' class='form-label'>titre de projet</label>
                                <input type='text' class='form-control cv-input-1' placeholder='' name='side_project_title' id='project-title-1'>
                            </div>
                            <div class='col-sm-8'>
                                <label for='project-1' class='form-label'>projet</label>
                                <textarea class='form-control cv-input-1' rows='5' id='project-1' name='side_project_description'></textarea>
                                <button type='submit' onclick=" . '"' . "ModifySection('side_project_1', 'save', 'SideProjectSectionController')".'"' . ">Save Side Project</button>
                            </div>
                        </form>
                        <form id='delete_side_project_1_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('side_project_1', 'delete', 'SideProjectSectionController')".'"' . ">Delete Side Project</button>
                            </div>
                        </form>
                    </div>
                    <br>";
        }

        $sideProjectSectionHtml .= "
                </div>
                <form id='addsubsec_side_project_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('projects', 'side_project', 'addsubsec', 'SideProjectSectionController')" . '"' . ">ajouter un projet</button>
                </form>
            </div>
        </div>";

        echo $sideProjectSectionHtml;
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