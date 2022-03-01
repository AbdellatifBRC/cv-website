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