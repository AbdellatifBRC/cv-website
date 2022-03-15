<?php

// require the parent class
require "CvSectionModel.php";

class LanguageSectionModel extends CvSectionModel{
    // insert a language
    public function InsertData($ColumnsValues){
        // check if the user already saved this language
        $stmt = $this->dbconn->prepare("SELECT id FROM languages_section WHERE user_id = :user_id AND language_name = :old_language_name AND language_level = :old_language_level");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_language_name", $ColumnsValues["old_language_name"]);
        $stmt->bindParam(":old_language_level", $ColumnsValues["old_language_level"]);
        $stmt->execute();
        // if the user alreeady saved this language then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO languages_section (user_id, language_name, language_level) VALUES (:user_id, :new_language_name, :new_language_level)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_language_name", $ColumnsValues["new_language_name"]);
            $stmt->bindParam(":new_language_level", $ColumnsValues["new_language_level"]);
            $stmt->execute();
        }
    }

    // update a language
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE languages_section SET language_name = :new_language_name, language_level = :new_language_level WHERE user_id = :user_id AND language_name = :old_language_name AND language_level = :old_language_level");
        $stmt->bindParam(":new_language_name", $ColumnsValues["new_language_name"]);
        $stmt->bindParam(":new_language_level", $ColumnsValues["new_language_level"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_language_name", $ColumnsValues["old_language_name"]);
        $stmt->bindParam(":old_language_level", $ColumnsValues["old_language_level"]);
        $stmt->execute();
    }

    // delete a language
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM languages_section WHERE user_id = :user_id AND language_name = :language_name AND language_level = :language_level");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":language_name", $ColumnsValues["old_language_name"]);
        $stmt->bindParam(":language_level", $ColumnsValues["old_language_level"]);
        $stmt->execute();
    }

    // retrieve a user's saved languages
    public function RetrieveData(){
        // get the user id
        $userId = $this->auth->getUserId();
        // retrieve the saved courses
        $stmt = $this->dbconn->prepare("SELECT * FROM languages_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>