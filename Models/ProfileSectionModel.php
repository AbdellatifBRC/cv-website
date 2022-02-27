<?php

// require the parent class
require "CvSectionModel.php";

class ProfileSectionModel extends CvSectionModel{
    // insert data to the profile_section table
    public function InsertData($ColumnsValues){
        // check if the user already has a profile section
        $stmt = $this->dbconn->prepare("SELECT id FROM profile_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->execute();
        // if the user already has a profile section then update it, else insert it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        }else{
            $stmt = $this->dbconn->prepare("INSERT INTO profile_section (user_id, description) VALUES (:user_id, :description)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":description", $ColumnsValues["description"]);
            $stmt->execute();
        }
    }

    // update data in the profile_section table
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE profile_section SET user_id = :user_id, description = :description WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":description", $ColumnsValues["description"]);
        $stmt->execute();
    }

    // delete data from the profile_section table
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM profile_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->execute();
    }
}

?>