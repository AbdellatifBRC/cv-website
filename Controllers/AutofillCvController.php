<?php

// start the session
session_start();

class AutofillCv{
    private $cvControllers;

    public function __construct($cvControllers){
        $this->cvControllers = $cvControllers;
    }

    public function DisplayCv($action){
        $result = [];
        // send a post request to each cv section controller and print the data returned
        foreach($this->cvControllers as $controller){
            // controller url
            $url = "http://localhost:3200/cv-website/Controllers/" . $controller . "SectionController.php";
            // url-ify the data for the POST (also send the data of the current session as they would be lost after a curl request)
            $dataString = http_build_query(array("action" => $action, "session_data" => session_encode()));
            // open curl connection
            $curlConn = curl_init();
            // set the url and post data
            curl_setopt($curlConn, CURLOPT_URL, $url);
            curl_setopt($curlConn, CURLOPT_POST, true);
            curl_setopt($curlConn, CURLOPT_POSTFIELDS, $dataString);
            // return the contents of the curl instead of echoing it
            curl_setopt($curlConn,CURLOPT_RETURNTRANSFER, true); 

            // execute the request
            $result[$controller] = curl_exec($curlConn);
            
            // close curl connection
            curl_close($curlConn);
        }

        echo json_encode($result);
    }
}

// a request has been sent from a view
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])){
    // require the input sanitizer
    require "InputController.php";

    // retrieve the action
    $action = new Input($_POST["action"]);
    $action->Sanitize();

    $cvControllers = ["PersonalDetails", "Course", "Experience", "Education", "Skill", "Profile", "Language", "Hobby", "SideProject", "SocialLink", "Custom"];
    $autofillCv = new AutofillCv($cvControllers);
    $autofillCv->DisplayCv($action->value);
}

?>