<?php

// require the parent class
require "CvSectionModel.php";

class ExperienceSectionModel extends CvSectionModel{
    // insert an experience
    public function InsertData($ColumnsValues){
        // check if the user already saved this experience
        $stmt = $this->dbconn->prepare("SELECT id FROM experience_section WHERE user_id = :user_id AND position = :old_position AND company_name = :old_company_name AND company_location = :old_company_location");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_position", $ColumnsValues["old_position"]);
        $stmt->bindParam(":old_company_name", $ColumnsValues["old_company_name"]);
        $stmt->bindParam(":old_company_location", $ColumnsValues["old_company_location"]);
        $stmt->execute();
        // if the user alreeady saved this experience then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO experience_section (user_id, position, company_name, company_location, start_date, end_date, description) VALUES (:user_id, :new_position, :new_company_name, :new_company_location, :new_start_date, :new_end_date, :new_description)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_position", $ColumnsValues["new_position"]);
            $stmt->bindParam(":new_company_name", $ColumnsValues["new_company_name"]);
            $stmt->bindParam(":new_company_location", $ColumnsValues["new_company_location"]);
            $stmt->bindParam(":new_start_date", $ColumnsValues["new_start_date"]);
            $stmt->bindParam(":new_end_date", $ColumnsValues["new_end_date"]);
            $stmt->bindParam(":new_description", $ColumnsValues["new_description"]);
            $stmt->execute();
        }
    }

    // update an experience
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE experience_section SET user_id = :user_id, position = :new_position, company_name = :new_company_name, company_location = :new_company_location, start_date = :new_start_date, end_date = :new_end_date, description = :new_description WHERE user_id = :user_id AND position = :old_position AND company_name = :old_company_name AND company_location = :old_company_location");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_position", $ColumnsValues["new_position"]);
            $stmt->bindParam(":new_company_name", $ColumnsValues["new_company_name"]);
            $stmt->bindParam(":new_company_location", $ColumnsValues["new_company_location"]);
            $stmt->bindParam(":new_start_date", $ColumnsValues["new_start_date"]);
            $stmt->bindParam(":new_end_date", $ColumnsValues["new_end_date"]);
            $stmt->bindParam(":new_description", $ColumnsValues["new_description"]);
            $stmt->bindParam(":old_position", $ColumnsValues["old_position"]);
            $stmt->bindParam(":old_company_name", $ColumnsValues["old_company_name"]);
            $stmt->bindParam(":old_company_location", $ColumnsValues["old_company_location"]);
            $stmt->execute();
    }

    // delete a course
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM experience_section WHERE user_id = :user_id AND position = :old_position AND company_name = :old_company_name AND company_location = :old_company_location");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_position", $ColumnsValues["old_position"]);
        $stmt->bindParam(":old_company_name", $ColumnsValues["old_company_name"]);
        $stmt->bindParam(":old_company_location", $ColumnsValues["old_company_location"]);
        $stmt->execute();
    }
}

?>