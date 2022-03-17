<?php
abstract class CvSectionModel{
    private $dbconnector;
    protected $dbconn;
    public $auth;

    public function __construct(){
        // connect to db
        require_once "../Config/DbConnect.php";
        $this->dbconnector = new DbConnect();
        $this->dbconn = $this->dbconnector->connect();

        // require the authentication library
        require '../Libraries/AuthLib/vendor/autoload.php';
        $this->auth = new \Delight\Auth\Auth($this->dbconn);
    }

    abstract public function InsertData($ColumnsValues);
    abstract public function UpdateData($ColumnsValues);
    abstract public function DeleteData($ColumnsValues);
    abstract public function RetrieveData();
}

?>