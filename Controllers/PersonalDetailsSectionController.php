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

    // display the personal details section
    public function DisplayData(){
        $PersonalDetailsAreSaved = false;
        // assign the session data sent from the curl request
        session_decode($_POST["session_data"]);

        // the default form to display
        $personalDetailsSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapseOne'>
                <h5>Details personels</h5>
            </a>
        </div>
        <div id='collapseOne' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='perso'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved personal details
            $personalDetails = $this->sectionModel->RetrieveData();

            // display the saved personal details only if they exist
            if(!empty($personalDetails)){
                $PersonalDetailsAreSaved= true;

                // create an image model for the user's saved image
                $photo = new ImageModel($personalDetails[0]["photo"], "", "", "");

                $personalDetailsSectionHtml .= "
                <div class='row'>
                    <div class='col-sm-3'>
                        <button class='btn btn-light' style='width:150px; height:150px' onclick=" . '"' . "showpopup('popup')" . '"' . "><img class='img-fluid' id='output-logo' src='" . $photo->filePath($personalDetails[0]["photo"]) . "'/><i class='fas fa-camera' id='img-logo' style='display:none'></i></button>
                    </div>
                    <div class='col-sm-9'>
                        <div class=''>
                            <form id='save_personal_details_section_form' enctype='multipart/form-data'>
                                <label for='nom' class='form-label'>Nom </label>
                                <input type='text' class='form-control' id='nom' placeholder='' name='last_name' value='" . $personalDetails[0]["last_name"] . "'>
                                <label for='prenom' class='form-label'>Prenom </label>
                                <input type='text' class='form-control' id='prenom' placeholder='' name='first_name' value='" . $personalDetails[0]["first_name"] . "'>
                            </form>
                        </div>
                    </div>
                </div>
                <br>
                <div class='row g-3'>
                    <div class='col-md-6'>
                        <label for='inputEmail4' class='form-label'>Email</label>
                        <input type='email' class='form-control' id='inputEmail4' name='email' value='" . $personalDetails[0]["email"] . "' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-md-6'>
                        <label for='numero' class='form-label'>Numero</label>
                        <input type='text' class='form-control' id='numro' name='phone'  value='" . $personalDetails[0]["phone"] . "' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-12'>
                        <label for='inputAddress' class='form-label'>Address</label>
                        <input type='text' class='form-control' id='inputAddress' placeholder='' name='address'  value='" . $personalDetails[0]["address"] . "' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-12'>
                        <label for='inputAddress2' class='form-label'>Date de naissance</label>
                        <input type='date' class='form-control' id='inputAddress2' placeholder='' name='birthdate'  value='" . $personalDetails[0]["birthdate"] . "' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-md-6'>
                        <label for='inputJobTitle' class='form-label'>Job Title</label>
                        <input type='text' class='form-control' id='inputJobTitle' name='job_title'  value='" . $personalDetails[0]["job_title"] . "' form='save_personal_details_section_form'>
                    </div> 
                </div>
                <button type='submit' form='save_personal_details_section_form' onclick=" . '"' . "ModifySection('personal_details', 'save', 'PersonalDetailsSectionController')" . '"' . ">Save Personal Details</button>
                <form id='delete_personal_details_section_form'>
                    <button type='submit' onclick=" . '"' . "ModifySection('personal_details', 'delete', 'PersonalDetailsSectionController')" . '"' . ">Delete Personal Details</button>
                </form>";
            }
        }

        // the default form to display
        if($PersonalDetailsAreSaved === false){
            $personalDetailsSectionHtml .= "
                <div class='row'>
                    <div class='col-sm-3'>
                        <button class='btn btn-light' style='width:150px; height:150px' onclick=" . '"' . "showpopup('popup')" . '"' . "><img class='img-fluid' id='output-logo'/><i class='fas fa-camera' id='img-logo'></i></button>
                    </div>
                    <div class='col-sm-9'>
                        <div class=''>
                            <form id='save_personal_details_section_form' enctype='multipart/form-data'>
                                <label for='nom' class='form-label'>Nom </label>
                                <input type='text' class='form-control' id='nom' placeholder='' name='last_name'>
                                <label for='prenom' class='form-label'>Prenom </label>
                                <input type='text' class='form-control' id='prenom' placeholder='' name='first_name'>
                            </form>
                        </div>
                    </div>
                </div>
                <br>
                <div class='row g-3'>
                    <div class='col-md-6'>
                        <label for='inputEmail4' class='form-label'>Email</label>
                        <input type='email' class='form-control' id='inputEmail4' name='email' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-md-6'>
                        <label for='numero' class='form-label'>Numero</label>
                        <input type='text' class='form-control' id='numro' name='phone' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-12'>
                        <label for='inputAddress' class='form-label'>Address</label>
                        <input type='text' class='form-control' id='inputAddress' placeholder='' name='address' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-12'>
                        <label for='inputAddress2' class='form-label'>Date de naissance</label>
                        <input type='date' class='form-control' id='inputAddress2' placeholder='' name='birthdate' form='save_personal_details_section_form'>
                    </div>
                    <div class='col-md-6'>
                        <label for='inputJobTitle' class='form-label'>Job Title</label>
                        <input type='text' class='form-control' id='inputJobTitle' name='job_title' form='save_personal_details_section_form'>
                    </div> 
                </div>
                <button type='submit' form='save_personal_details_section_form' onclick=" . '"' . "ModifySection('personal_details', 'save', 'PersonalDetailsSectionController')" . '"' . ">Save Personal Details</button>
                <form id='delete_personal_details_section_form'>
                    <button type='submit' onclick=" . '"' . "ModifySection('personal_details', 'delete', 'PersonalDetailsSectionController')" . '"' . ">Delete Personal Details</button>
                </form>";
        }

        $personalDetailsSectionHtml .= "
                </div>
            </div>
        </div>";

        echo $personalDetailsSectionHtml;
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