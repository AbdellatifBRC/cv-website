<?php

// require the parent class
require "CvSectionModel.php";

class SocialLinkSectionModel extends CvSectionModel{
    // insert a social link
    public function InsertData($ColumnsValues){
        // check if the user already saved this social link
        $stmt = $this->dbconn->prepare("SELECT id FROM social_links_section WHERE user_id = :user_id AND website_name = :old_website_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_website_name", $ColumnsValues["old_website_name"]);
        $stmt->execute();
        // if the user alreeady saved this social link then update it
        if(count($stmt->fetchAll()) === 1){
            $this->UpdateData($ColumnsValues);
        } else{
            $stmt = $this->dbconn->prepare("INSERT INTO social_links_section (user_id, website_name, link) VALUES (:user_id, :new_website_name, :new_link)");
            $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
            $stmt->bindParam(":new_website_name", $ColumnsValues["new_website_name"]);
            $stmt->bindParam(":new_link", $ColumnsValues["new_link"]);
            $stmt->execute();
        }
    }

    // update a social link
    public function UpdateData($ColumnsValues){
        $stmt = $this->dbconn->prepare("UPDATE social_links_section SET website_name = :new_website_name, link = :new_link WHERE user_id = :user_id AND website_name = :old_website_name");
        $stmt->bindParam(":new_website_name", $ColumnsValues["new_website_name"]);
        $stmt->bindParam(":new_link", $ColumnsValues["new_link"]);
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":old_website_name", $ColumnsValues["old_website_name"]);
        $stmt->execute();
    }

    // delete a social link
    public function DeleteData($ColumnsValues){
        $stmt = $this->dbconn->prepare("DELETE FROM social_links_section WHERE user_id = :user_id AND website_name = :website_name");
        $stmt->bindParam(":user_id", $ColumnsValues["user_id"]);
        $stmt->bindParam(":website_name", $ColumnsValues["old_website_name"]);
        $stmt->execute();
    }
}

?>