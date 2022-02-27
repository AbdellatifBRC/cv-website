<?php

abstract class CvSectionController{
    protected $sectionModel;

    public function __construct($sectionModel){
        // create a section model
        require "../Models/" . $sectionModel . ".php";
        $this->sectionModel = new $sectionModel;
    }

    public function performAction($action){
        switch($action){
            case "save":
                $this->SaveData();
                break;
            case "delete":
                $this->DeleteData();
                break;
            default:
                break;
        }
    }

    abstract public function SaveData();
    abstract public function DeleteData();
}

?>