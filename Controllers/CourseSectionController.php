<?php

// require the parent class
require "CvSectionController.php";

class CourseSectionController extends CvSectionController{
    // save a course
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["course_name"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newCourseName = new Input($_POST["course_name"]);
                    $newCourseName->Sanitize();

                    // check to see if the user already added this course
                    $oldCourseName = new Input("");
                    if(isset($_POST["old_course_name"])){
                        $oldCourseName->value = $_POST["old_course_name"];
                        $oldCourseName->Sanitize();
                    } else{
                        $oldCourseName->value = null;
                    }

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // save the course
                    $this->sectionModel->InsertData(array("user_id" => $userId, "new_course_name" => $newCourseName->value, "old_course_name" => $oldCourseName->value));

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

    // delete a course
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_course_name"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $oldCourseName = new Input($_POST["old_course_name"]);
                    $oldCourseName->Sanitize();

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete the course
                    $this->sectionModel->DeleteData(array("user_id" => $userId, "old_course_name" => $oldCourseName->value));

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

    // add another course subsection
    public function AddSubsecToSec(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{

                // sanitize the user's input
                $coursesNumber = new Input($_POST["subsecs_in_section"]);
                $coursesNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $coursesNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='course' id='course_" . strval($coursesNumber->value + 1) . "'>
                        <form id='save_course_" . strval($coursesNumber->value + 1) . "_section_form'>
                            <input type='text' id='' name='course_name'>
                            <button type='submit' onclick=" . '"'. "ModifySection('course_" . strval($coursesNumber->value + 1) . "', 'save', 'CourseSectionController')" . '"'. ">Save Course</button>
                        </form>
                        <form id='delete_course_" . strval($coursesNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('course_" . strval($coursesNumber->value + 1) . "', 'delete', 'CourseSectionController')" . '"' . ">Delete Course</button>
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

    // display the courses section
    public function DisplayData(){
        $coursesAreSaved = false;

        // assign the session data sent from the curl request
        if($_POST["session_data"]){
            session_decode($_POST["session_data"]);
        }

        // the default form to display
        $coursesSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapsetwo'>
            <h5>Courses</h5>
            </a>
        </div>
        <div id='collapsetwo' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='formations' id='formations'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved courses
            $coursesDetails = $this->sectionModel->RetrieveData();

            // display the saved courses only if they exist
            if(!empty($coursesDetails)){
                $coursesAreSaved = true;
                foreach($coursesDetails as $course){
                    $coursesSectionHtml .= "
                    <div class='row course' id='course_" . $course["id"] . "'>
                        <form id='save_course_" . $course["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <label for='formation-" . $course["id"] . "' class='form-label'>Description</label>
                                <input type='text' class='form-control' id='formation-" . $course["id"] . "' placeholder='Ex : Cisco certificat' name='course_name' value='" . str_replace("'", "&apos;", str_replace('"', "&quot;", $course["course_name"])) . "'>
                                <button onclick=" . '"' . "ModifySection('course_" . $course["id"] . "', 'save', 'CourseSectionController')".'"' . ">Save Course</button>
                            </div>
                        </form>
                        <form id='delete_course_" . $course["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('course_" . $course["id"] . "', 'delete', 'CourseSectionController')".'"' . ">Delete Course</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($coursesAreSaved === false){
            $coursesSectionHtml .= "
                    <div class='row course' id='course_1'>
                        <form id='save_course_1_section_form'>
                            <div class='col-sm-9'>
                                <label for='formation-1' class='form-label'>Description</label>
                                <input type='text' class='form-control' id='formation-1' placeholder='Ex : Cisco certificat' name='course_name'>
                                <button onclick=" . '"' . "ModifySection('course_1', 'save', 'CourseSectionController')".'"' . ">Save Course</button>
                            </div>
                        </form>
                        <form id='delete_course_1_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('course_1', 'delete', 'CourseSectionController')".'"' . ">Delete Course</button>
                            </div>
                        </form>
                    </div>
                    <br>";
        }

        $coursesSectionHtml .= "
                </div>
                <form id='addsubsec_course_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('formations', 'course', 'addsubsec', 'CourseSectionController')" . '"' . ">ajouter une formation</button>
                </form>
            </div>
        </div>";

        echo $coursesSectionHtml;
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
    $account = new CourseSectionController("CourseSectionModel");
    $account->performAction($action->value);
}

?>