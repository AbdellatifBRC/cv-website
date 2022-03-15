<?php

// require the parent class
require "CvSectionModel.php";

class SkillSectionModel extends CvSectionModel{
    // insert a skill
    public function InsertData($ColumnsValues){
        // check if the user already saved this skill
        $stmt = $this->dbconn->prepare("SELECT id FROM skills_section WHERE user_id = :user_id AND skill_name = :old_skill_name AND skill_level = :old_skill_level");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_skill_name", $ColumnsValues["old_skill_name"]);
        $stmt->bindParam(":old_skill_level", $ColumnsValues["old_skill_level"]);
        $stmt->execute();
        // if the user already saved this skill then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO skills_section (user_id, skill_name, skill_level) VALUES (:user_id, :new_skill_name, :new_skill_level)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_skill_name", $ColumnsValues["new_skill_name"]);
            $stmt->bindParam(":new_skill_level", $ColumnsValues["new_skill_level"]);
            $stmt->execute();
        }
    }

    // update a skill
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE skills_section SET skill_name = :new_skill_name, skill_level = :new_skill_level WHERE user_id = :user_id AND skill_name = :old_skill_name AND skill_level = :old_skill_level");
        $stmt->bindParam(":new_skill_name", $ColumnsValues["new_skill_name"]);
        $stmt->bindParam(":new_skill_level", $ColumnsValues["new_skill_level"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_skill_name", $ColumnsValues["old_skill_name"]);
        $stmt->bindParam(":old_skill_level", $ColumnsValues["old_skill_level"]);
        $stmt->execute();
    }

    // delete a skill
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM skills_section WHERE user_id = :user_id AND skill_name = :skill_name AND skill_level = :skill_level");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":skill_name", $ColumnsValues["old_skill_name"]);
        $stmt->bindParam(":skill_level", $ColumnsValues["old_skill_level"]);
        $stmt->execute();
    }

    // retrieve a user's skills data
    public function RetrieveData(){
        // get the user id
        $userId = $this->auth->getUserId();
        // retrieve the saved courses
        $stmt = $this->dbconn->prepare("SELECT * FROM skills_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>