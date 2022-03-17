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
    
    // display the hobby section
    public function DisplayData(){
        $hobbiesAreSaved= false;
        // assign the session data sent from the curl request
        session_decode($_POST["session_data"]);

        // the default form to display
        $hobbySectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapseeight'>
                <h5>Centre d'interets</h5>
            </a>
        </div>
        <div id='collapseeight' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='hobbies' id='hobbies'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved hobbies
            $hobbiesDetails = $this->sectionModel->RetrieveData();

            // display the saved hobbies only if they exist
            if(!empty($hobbiesDetails)){
                $hobbiesAreSaved= true;
                foreach($hobbiesDetails as $hobby){
                    $hobbySectionHtml .= "
                    <div class='row hobby' id='hobby_" . $hobby["id"] . "'>
                        <form id='save_hobby_" . $hobby["id"] . "_section_form'>
                            <div class='col-sm-12'>
                                <label for='hobbie-" . $hobby["id"] . "' class='form-label'>interet</label>
                                <input type='text' class='form-control' placeholder='' name='hobby_name' id='hobbie-" . $hobby["id"] . "' value='" . $hobby["hobby_name"] . "'>
                                <button type='submit' onclick=" . '"' . "ModifySection('hobby_" . $hobby["id"] . "', 'save', 'HobbySectionController')".'"' . ">Save Hobby</button>
                            </div>
                        </form>
                        <form id='delete_hobby_" . $hobby["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('hobby_" . $hobby["id"] . "', 'delete', 'HobbySectionController')".'"' . ">Delete Hobby</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($hobbiesAreSaved=== false){
            $hobbySectionHtml .= "
                    <div class='row hobby' id='hobby_1'>
                        <form id='save_hobby_1_section_form'>
                            <div class='col-sm-12'>
                                <label for='hobbie-1' class='form-label'>interet</label>
                                <input type='text' class='form-control' placeholder='' name='hobby_name' id='hobbie-1'>
                                <button type='submit' onclick=" . '"' . "ModifySection('hobby_1', 'save', 'HobbySectionController')".'"' . ">Save Hobby</button>
                            </div>
                        </form>
                        <form id='delete_hobby_1_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('hobby_1', 'delete', 'HobbySectionController')".'"' . ">Delete Hobby</button>
                            </div>
                        </form>
                    </div>
                    <br>";
        }

        $hobbySectionHtml .= "
                </div>
                <form id='addsubsec_hobby_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('hobbies', 'hobby', 'addsubsec', 'HobbySectionController')" . '"' . ">ajouter un interet</button>
                </form>
            </div>
        </div>";

        echo $hobbySectionHtml;
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