<?php

// require the parent class
require "CvSectionModel.php";

class HobbySectionModel extends CvSectionModel{
    // insert a hobby
    public function InsertData($ColumnsValues){
        // check if the user already saved this hobby
        $stmt = $this->dbconn->prepare("SELECT id FROM hobbies_section WHERE user_id = :user_id AND hobby_name = :old_hobby_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_hobby_name", $ColumnsValues["old_hobby_name"]);
        $stmt->execute();
        // if the user already saved this hobby then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO hobbies_section (user_id, hobby_name) VALUES (:user_id, :new_hobby_name)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_hobby_name", $ColumnsValues["new_hobby_name"]);
            $stmt->execute();
        }
    }

    // update a hobby
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE hobbies_section SET hobby_name = :new_hobby_name WHERE user_id = :user_id AND hobby_name = :old_hobby_name");
        $stmt->bindParam(":new_hobby_name", $ColumnsValues["new_hobby_name"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_hobby_name", $ColumnsValues["old_hobby_name"]);
        $stmt->execute();
    }

    // delete a hobby
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM hobbies_section WHERE user_id = :user_id AND hobby_name = :hobby_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":hobby_name", $ColumnsValues["old_hobby_name"]);
        $stmt->execute();
    }
}

?>