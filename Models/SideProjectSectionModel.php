<?php

// require the parent class
require "CvSectionModel.php";

class SideProjectSectionModel extends CvSectionModel{
    // insert a side project
    public function InsertData($ColumnsValues){
        // check if the user already saved this side project
        $stmt = $this->dbconn->prepare("SELECT id FROM side_projects_section WHERE user_id = :user_id AND title = :old_title");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_title", $ColumnsValues["old_title"]);
        $stmt->execute();
        // if the user alreeady saved this side project then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO side_projects_section (user_id, title, description) VALUES (:user_id, :new_title, :new_description)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_title", $ColumnsValues["new_title"]);
            $stmt->bindParam(":new_description", $ColumnsValues["new_description"]);
            $stmt->execute();
        }
    }

    // update a side project
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE side_projects_section SET title = :new_title, description = :new_description WHERE user_id = :user_id AND title = :old_title");
        $stmt->bindParam(":new_title", $ColumnsValues["new_title"]);
        $stmt->bindParam(":new_description", $ColumnsValues["new_description"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_title", $ColumnsValues["old_title"]);
        $stmt->execute();
    }

    // delete a side project
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM side_projects_section WHERE user_id = :user_id AND title = :title");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":title", $ColumnsValues["old_title"]);
        $stmt->execute();
    }
}

?>