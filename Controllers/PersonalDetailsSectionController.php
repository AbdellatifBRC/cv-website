<?php

// require the parent class
require "CvSectionController.php";
// require the image model
require "../Models/ImageModel.php";

class PersonalDetailsSectionController extends CvSectionController{
    // insert data to the personal_details_section table
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["phone"]) && isset($_POST["address"]) && isset($_POST["birthdate"]) && isset($_POST["job_title"]) && isset($_POST["email"]) && isset($_FILES["img"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input - start with photo validation first
                    $photoName = new Input($_FILES["img"]["name"]);
                    $photoName->Sanitize();
                    $photoSize = new Input($_FILES["img"]["size"]);
                    $photoTmpName = new Input($_FILES["img"]["tmp_name"]);
                    $photoType = new Input($_FILES["img"]["type"]);

                    // check if the photo is valid
                    $photo = new ImageModel($photoName->value, $photoSize->value, $photoTmpName->value, $photoType->value);
                    if($photo->ValidateFile()){
                        // sanitize the rest of the user's input
                        $firstName = new Input($_POST["first_name"]);
                        $firstName->Sanitize();
                        $lastName = new Input($_POST["last_name"]);
                        $lastName->Sanitize();
                        $phone = new Input($_POST["phone"]);
                        $phone->Sanitize();
                        $address = new Input($_POST["address"]);
                        $address->Sanitize();
                        $birthdate = new Input($_POST["birthdate"]);
                        $birthdate->Sanitize();
                        $jobTitle = new Input($_POST["job_title"]);
                        $jobTitle->Sanitize();
                        $email = new Input($_POST["email"]);
                        $email->Sanitize();

                        // ensure a valid date
                        if(date("Y-m-d", strtotime($birthdate->value)) !== date($birthdate->value)){
                            echo json_encode(array("error" => "Invalid date"));
                            exit();
                        }

                        // ensure a valid email
                        if(!filter_var($email->value, FILTER_VALIDATE_EMAIL)){
                            echo json_encode(array("error" => "Invalid email"));
                            exit();
                        }
                        
                        // store the file
                        $storeFileError = $photo->StoreFile();
                        if($storeFileError === ""){
                            // get the user id
                            $userId = $this->sectionModel->auth->getUserId();

                            // insert data
                            $this->sectionModel->InsertData(array("user_id" => $userId, "first_name" => $firstName->value, "last_name" => $lastName->value, "phone" => $phone->value, "address" => $address->value, "birthdate" => $birthdate->value, "job_title" => $jobTitle->value, "email" => $email->value, "photo" => $photoName->value));

                            // indicate that the action has completed
                            $response_array["action_completed"] = true;
                        } else{
                            $response_array["error"] = $storeFileError;
                        }
                    } else{
                        $response_array["error"] = "Invalid File";
                    }
                } else{
                    $response_array["logged_in"] = false;
                }
            } catch (Exception $e) {
                $response_array["error"] = $e->getMessage();
            }
        } else{
            $response_array["error"] = "Please enter all fields";
        }

        echo json_encode($response_array);
    }

    // delete data from the personal_details_section table
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete the user's image
                    $photo = new ImageModel("", "", "", "");
                    $photo->deleteUserImg();

                    // delete data from db
                    $this->sectionModel->DeleteData(array("user_id" => $userId));

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

    // users can not add another personal details section, so just display an error
    public function AddSubsecToSec(){
        echo json_encode(array("error" => "You can not add another personal details section"));
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
    $account = new PersonalDetailsSectionController("PersonalDetailsSectionModel");
    $account->performAction($action->value);
}

?>