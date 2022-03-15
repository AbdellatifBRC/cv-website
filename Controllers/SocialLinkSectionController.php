<?php

// require the parent class
require "CvSectionController.php";

class SocialLinkSectionController extends CvSectionController{
    // save a social link
    public function SaveData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["website_name"]) && isset($_POST["website_link"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $newSocialLinkName = new Input($_POST["website_name"]);
                    $newSocialLinkName->Sanitize();
                    $newSocialLink = new Input($_POST["website_link"]);
                    $newSocialLink->Sanitize();

                    // ensure a valid link
                    if(filter_var($newSocialLink->value, FILTER_VALIDATE_URL)){
                        // check to see if the user already saved this social link
                        $oldSocialLinkName = new Input("");
                        if(isset($_POST["old_website_name"])){
                            $oldSocialLinkName->value = $_POST["old_website_name"];
                            $oldSocialLinkName->Sanitize();
                        } else{
                            $oldSocialLinkName->value = null;
                        }


                        // get the user id
                        $userId = $this->sectionModel->auth->getUserId();

                        // save the social link
                        $this->sectionModel->InsertData(array("user_id" => $userId, "new_website_name" => $newSocialLinkName->value, "new_link" => $newSocialLink->value, "old_website_name" => $oldSocialLinkName->value));

                        // indicate that the action has completed
                        $response_array["action_completed"] = true;
                    } else{
                        $response_array["error"] = "Invalid Link";
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

    // delete a social link
    public function DeleteData(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_website_name"])){
            try{
                if($this->sectionModel->auth->isLoggedIn()){
                    // indicate that the user is logged in
                    $response_array["logged_in"] = true;

                    // sanitize the user's input
                    $oldWebsiteName = new Input($_POST["old_website_name"]);
                    $oldWebsiteName->Sanitize();

                    // get the user id
                    $userId = $this->sectionModel->auth->getUserId();

                    // delete the social link
                    $this->sectionModel->DeleteData(array("user_id" => $userId, "old_website_name" => $oldWebsiteName->value));

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

    // add another social link subsection
    public function AddSubsecToSec(){
        // this array will be sent as a response to the client
        $response_array["action_completed"] = false;
        $response_array["error"] = "";
        $response_array["new_subsec_html"] = "";
        
        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["subsecs_in_section"])){
            try{

                // sanitize the user's input
                $socialLinksNumber = new Input($_POST["subsecs_in_section"]);
                $socialLinksNumber->Sanitize();
                if(preg_match("/^(0|[1-9]+[0-9]*)$/", $socialLinksNumber->value)){
                    $response_array["new_subsec_html"] = "
                    <div class='row link' id='link_" . strval($socialLinksNumber->value + 1) . "'>
                        <form id='save_link_" . strval($socialLinksNumber->value + 1) . "_section_form'>
                            <div class'col-sm-4'>
                                <label for='website-name-" . strval($socialLinksNumber->value + 1) . "' class='form-label'>nom du site</label>
                                <input type='text' class='form-control' placeholder='' name='website_name' id='website-name-" . strval($socialLinksNumber->value + 1) . "'>
                            </div>
                            <div class'col-sm-4'>
                                <label for='link-" . strval($socialLinksNumber->value + 1) . "' class='form-label'>lien</label>
                                <input type='text' class='form-control' placeholder='' name='website_link' id='link-" . strval($socialLinksNumber->value + 1) . "'>
                            </div>
                            <div class'col-sm-4'>
                                <button type='submit' onclick=" . '"'. "ModifySection('link_" . strval($socialLinksNumber->value + 1) . "', 'save', 'SocialLinkSectionController')" . '"'. ">Save Link</button>
                            </div>
                        </form>
                        <form id='delete_link_" . strval($socialLinksNumber->value + 1) . "_section_form'>
                            <button type='submit' onclick=" . '"' . "ModifySection('link_" . strval($socialLinksNumber->value + 1) . "', 'delete', 'SocialLinkSectionController')" . '"' . ">Delete Link</button>
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

    // display the social links section
    public function DisplayData(){
        $socialLinksAreSaved= false;
        // assign the session data sent from the curl request
        session_decode($_POST["session_data"]);

        // the default form to display
        $socialLinkSectionHtml = "
        <div class='card-header'>
            <a class='btn' data-bs-toggle='collapse' href='#collapseten'>
                <h5>Liens des réseaux sociaux</h5>
            </a>
        </div>
        <div id='collapseten' class='collapse show' data-bs-parent='#accordion'>
            <div class='card-body'>
                <div class='links' id='links'>";

        // only logged in users can view their saved data
        if($this->sectionModel->auth->isLoggedIn()){
            // get the user's saved social_links
            $socialLinksDetails = $this->sectionModel->RetrieveData();

            // display the saved social_links only if they exist
            if(!empty($socialLinksDetails)){
                $socialLinksAreSaved= true;
                foreach($socialLinksDetails as $socialLink){
                    $socialLinkSectionHtml .= "
                    <div class='row social_link' id='social-link_" . $socialLink["id"] . "'>
                        <form id='save_social_link_" . $socialLink["id"] . "_section_form'>
                            <div class'col-sm-4'>
                                <label for='website-name-" . $socialLink["id"] . "' class='form-label'>nom du site</label>
                                <input type='text' class='form-control' placeholder='' name='website_name' id='website-name-" . $socialLink["id"] . "' value='" . $socialLink["website_name"] . "'>
                            </div>
                            <div class'col-sm-4'>
                                <label for='link-" . $socialLink["id"] . "' class='form-label'>lien</label>
                                <input type='text' class='form-control' placeholder='' name='website_link' id='link-" . $socialLink["id"] . "' value='" . $socialLink["link"] . "'>
                            </div>
                            <div class'col-sm-4'>
                                <button type='submit' onclick=" . '"'. "ModifySection('social_link_" . $socialLink["id"] . "', 'save', 'SocialLinkSectionController')" . '"'. ">Save Link</button>
                            </div>
                        </form>
                        <form id='delete_social_link_" . $socialLink["id"] . "_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('social_link_" . $socialLink["id"] . "', 'delete', 'SocialLinkSectionController')".'"' . ">Delete Link</button>
                            </div>
                        </form>
                    </div>
                    <br>";
                }
            }
        }

        // the default form to display
        if($socialLinksAreSaved=== false){
            $socialLinkSectionHtml .= "
                    <div class='row social_link' id='social-link_1'>
                        <form id='save_social_link_1_section_form'>
                            <div class'col-sm-4'>
                                <label for='website-name-1' class='form-label'>nom du site</label>
                                <input type='text' class='form-control' placeholder='' name='website_name' id='website-name-1'>
                            </div>
                            <div class'col-sm-4'>
                                <label for='link-1' class='form-label'>lien</label>
                                <input type='text' class='form-control' placeholder='' name='website_link' id='link-1'>
                            </div>
                            <div class'col-sm-4'>
                                <button type='submit' onclick=" . '"'. "ModifySection('social_link_1', 'save', 'SocialLinkSectionController')" . '"'. ">Save Link</button>
                            </div>
                        </form>
                        <form id='delete_social_link_1_section_form'>
                            <div class='col-sm-9'>
                                <button type='submit' onclick=" . '"' . "ModifySection('social_link_1', 'delete', 'SocialLinkSectionController')".'"' . ">Delete Link</button>
                            </div>
                        </form>
                    </div>
                    <br>";
        }

        $socialLinkSectionHtml .= "
                </div>
                <form id='addsubsec_social_link_section_form'>
                    <button class='btn btn-primary ' onclick=" . '"' . "AddSubsec('links', 'social_link', 'addsubsec', 'SocialLinkSectionController')" . '"' . ">ajouter un lien</button>
                </form>
            </div>
        </div>";

        echo $socialLinkSectionHtml;
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
    $account = new SocialLinkSectionController("SocialLinkSectionModel");
    $account->performAction($action->value);
}

?>