<?php

// require the parent class
require "CvSectionModel.php";

class PersonalDetailsSectionModel extends CvSectionModel{
    // insert the personal details
    public function InsertData($ColumnsValues){
        // check if the user already saved this language
        $stmt = $this->dbconn->prepare("SELECT id FROM personal_details_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->execute();
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO personal_details_section (user_id, first_name, last_name, phone, address, birthdate, job_title, email, photo) VALUES (:user_id, :first_name, :last_name, :phone, :address, :birthdate, :job_title, :email, :photo)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":first_name", $ColumnsValues["first_name"]);
            $stmt->bindParam(":last_name", $ColumnsValues["last_name"]);
            $stmt->bindParam("phone", $ColumnsValues["phone"]);
            $stmt->bindParam(":address", $ColumnsValues["address"]);
            $stmt->bindParam(":birthdate", $ColumnsValues["birthdate"]);
            $stmt->bindParam(":job_title", $ColumnsValues["job_title"]);
            $stmt->bindParam(":email", $ColumnsValues["email"]);
            $stmt->bindParam(":photo", $ColumnsValues["photo"]);
            $stmt->execute();
        }
    }

    // update the personal details
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE personal_details_section SET user_id = :user_id, first_name = :first_name, last_name = :last_name, phone = :phone, address = :address, birthdate = :birthdate, job_title = :job_title, email = :email, photo = :photo WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":first_name", $ColumnsValues["first_name"]);
        $stmt->bindParam(":last_name", $ColumnsValues["last_name"]);
        $stmt->bindParam("phone", $ColumnsValues["phone"]);
        $stmt->bindParam(":address", $ColumnsValues["address"]);
        $stmt->bindParam(":birthdate", $ColumnsValues["birthdate"]);
        $stmt->bindParam(":job_title", $ColumnsValues["job_title"]);
        $stmt->bindParam(":email", $ColumnsValues["email"]);
        $stmt->bindParam(":photo", $ColumnsValues["photo"]);
        $stmt->execute();
    }

    // delete the personal details
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM personal_details_section WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->execute();
    }
}

?>