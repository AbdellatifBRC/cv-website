<?php

// require the parent class
require "CvSectionModel.php";

class CourseSectionModel extends CvSectionModel{
    // insert a course
    public function InsertData($ColumnsValues){
        // check if the user already saved this course
        $stmt = $this->dbconn->prepare("SELECT id FROM courses_section WHERE user_id = :user_id AND course_name = :old_course_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_course_name", $ColumnsValues["old_course_name"]);
        $stmt->execute();
        // if the user alreeady saved this course then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO courses_section (user_id, course_name) VALUES (:user_id, :new_course_name)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_course_name", $ColumnsValues["new_course_name"]);
            $stmt->execute();
        }
    }

    // update a course
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE courses_section SET course_name = :new_course_name WHERE user_id = :user_id AND course_name = :old_course_name");
        $stmt->bindParam(":new_course_name", $ColumnsValues["new_course_name"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_course_name", $ColumnsValues["old_course_name"]);
        $stmt->execute();
    }

    // delete a course
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM courses_section WHERE user_id = :user_id AND course_name = :course_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":course_name", $ColumnsValues["old_course_name"]);
        $stmt->execute();
    }

    // retrieve a user's courses data
    public function RetrieveData(){
        // get the user id
        $userId = $this->auth->getUserId();
        // retrieve the saved courses
        $stmt = $this->dbconn->prepare("SELECT * FROM courses_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>