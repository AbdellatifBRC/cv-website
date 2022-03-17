<?php

// require the parent class
require "CvSectionModel.php";

class CustomSectionModel extends CvSectionModel{
    // insert a custom section
    public function InsertData($ColumnsValues){
        // check if the user already saved this custom section
        $stmt = $this->dbconn->prepare("SELECT id FROM custom_sections_section WHERE user_id = :user_id AND title = :old_title");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_title", $ColumnsValues["old_title"]);
        $stmt->execute();
        // if the user alreeady saved this custom section then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO custom_sections_section (user_id, title, description) VALUES (:user_id, :new_title, :new_description)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_title", $ColumnsValues["new_title"]);
            $stmt->bindParam(":new_description", $ColumnsValues["new_description"]);
            $stmt->execute();
        }
    }

    // update a custom section
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE custom_sections_section SET title = :new_title, description = :new_description WHERE user_id = :user_id AND title = :old_title");
        $stmt->bindParam(":new_title", $ColumnsValues["new_title"]);
        $stmt->bindParam(":new_description", $ColumnsValues["new_description"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_title", $ColumnsValues["old_title"]);
        $stmt->execute();
    }

    // delete a custom section
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM custom_sections_section WHERE user_id = :user_id AND title = :title");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":title", $ColumnsValues["old_title"]);
        $stmt->execute();
    }

    // retrieve a user's saved custom sections
    public function RetrieveData(){
        // get the user id
        $userId = $this->auth->getUserId();
        // retrieve the saved courses
        $stmt = $this->dbconn->prepare("SELECT * FROM custom_sections_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>