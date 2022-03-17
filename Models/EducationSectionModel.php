<?php

// require the parent class
require "CvSectionModel.php";

class EducationSectionModel extends CvSectionModel{
    // insert an education
    public function InsertData($ColumnsValues){
        // check if the user already saved this education
        $stmt = $this->dbconn->prepare("SELECT id FROM education_section WHERE user_id = :user_id AND degree = :old_degree AND field = :old_field AND school_name = :old_school_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_degree", $ColumnsValues["old_degree"]);
        $stmt->bindParam(":old_field", $ColumnsValues["old_field"]);
        $stmt->bindParam(":old_school_name", $ColumnsValues["old_school_name"]);
        $stmt->execute();
        // if the user alreeady saved this education then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO education_section (user_id, degree, field, school_name, start_date, end_date) VALUES (:user_id, :new_degree, :new_field, :new_school_name, :new_start_date, :new_end_date)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_degree", $ColumnsValues["new_degree"]);
            $stmt->bindParam(":new_field", $ColumnsValues["new_field"]);
            $stmt->bindParam(":new_school_name", $ColumnsValues["new_school_name"]);
            $stmt->bindParam(":new_start_date", $ColumnsValues["new_start_date"]);
            $stmt->bindParam(":new_end_date", $ColumnsValues["new_end_date"]);
            $stmt->execute();
        }
    }

    // update an education
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE education_section SET user_id = :user_id, degree = :new_degree, field = :new_field, school_name = :new_school_name, start_date = :new_start_date, end_date = :new_end_date WHERE user_id = :user_id AND degree = :old_degree AND field = :old_field AND school_name = :old_school_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":new_degree", $ColumnsValues["new_degree"]);
        $stmt->bindParam(":new_field", $ColumnsValues["new_field"]);
        $stmt->bindParam(":new_school_name", $ColumnsValues["new_school_name"]);
        $stmt->bindParam(":new_start_date", $ColumnsValues["new_start_date"]);
        $stmt->bindParam(":new_end_date", $ColumnsValues["new_end_date"]);
        $stmt->bindParam(":old_degree", $ColumnsValues["old_degree"]);
        $stmt->bindParam(":old_field", $ColumnsValues["old_field"]);
        $stmt->bindParam(":old_school_name", $ColumnsValues["old_school_name"]);
        $stmt->execute();
    }

    // delete a course
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM education_section WHERE user_id = :user_id AND degree = :old_degree AND field = :old_field AND school_name = :old_school_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_degree", $ColumnsValues["old_degree"]);
        $stmt->bindParam(":old_field", $ColumnsValues["old_field"]);
        $stmt->bindParam(":old_school_name", $ColumnsValues["old_school_name"]);
        $stmt->execute();
    }

    // retrieve a user's experience data
    public function RetrieveData(){
        // get the user id
        $userId = $this->auth->getUserId();
        // retrieve the saved courses
        $stmt = $this->dbconn->prepare("SELECT * FROM education_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>