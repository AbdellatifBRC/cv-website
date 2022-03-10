<?php

class ImageModel{
    private $imageName;
    private $imageSize;
    private $imageTmpName;
    private $imageType;
    private $dbconnector;
    private $dbconn;
    private $auth;

    public function __construct($imageName, $imageSize, $imageTmpName, $imageType){
        $this->imageName = $imageName;
        $this->imageSize = $imageSize;
        $this->imageTmpName = $imageTmpName;
        $this->imageType = $imageType;

        // connect to db
        require_once "../Config/DbConnect.php";
        $this->dbconnector = new DbConnect();
        $this->dbconn = $this->dbconnector->connect();

        // require the authentication library
        require '../Libraries/AuthLib/vendor/autoload.php';
        $this->auth = new \Delight\Auth\Auth($this->dbconn);
    }

    // check if a file is a valid image
    public function ValidateFile(){
        // this array holds the allowed file types
        $allowedFileTypes= ["image/jpeg", "image/png"];
        // this array holds the allowed file extensions
        $allowedFileExtensions = ["jpeg", "png", "jpg", "JPEG", "PNG", "JPG"];

        // check if the mime type is valid
        if(!in_array($this->imageType, $allowedFileTypes)){
            return false;
        }

        // check if the uploaded file's name is of valid format
        if(strlen($this->imageName) > 254 || !preg_match("/^([a-zA-Z0-9]+[_-]*)+[a-zA-Z0-9]+\.(jpeg|jpg|png|JPG|PNG|JPEG)$/", $this->imageName)){
            return false;
        }

        // assign the extension
        $fileNameAndExtension = explode('.', $this->imageName);
        $fileExtension = strtolower(end($fileNameAndExtension));
        // check if the uploaded file's extension is of valid format
        if(!in_array($fileExtension, $allowedFileExtensions)){
            return false;
        }

        // check if the uploaded file is of valid size
        if($this->imageSize < 67 || $this->imageSize > 2000000){
            return false;
        }

        // check if the file is an actual image
        // create a new image based on the uploaded file
        if($fileExtension == "jpg"){
            $fileExtension = "jpeg";
        }
        $newImage = call_user_func("imagecreatefrom" . $fileExtension, $this->imageTmpName);
        // if the image wasn't successfully created then the file should be considered as invalid
        if($newImage === false){
            return false;
        }

        // all the tests have been successfully passed and the image is valid
        return true;
    }

    // get the image name (if found) associated to a user
    private function getUserImgName($userId){
        try{
            $stmt = $this->dbconn->prepare("SELECT photo FROM personal_details_section WHERE user_id = :user_id LIMIT 1");
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($result)){
                return array("error" => "", "user_img_name" => null);
            } else{
                return array("error" => "", "user_img_name" => $result[0]["photo"]);
            }
        } catch (Exception $e) {
            return array("error" => $e->getMessage(), "user_img_name" => null);
        }
    }

    // get the user (if found) associated to an image name
    private function getImgNameUser($imageName){
        try{
            $stmt = $this->dbconn->prepare("SELECT user_id FROM personal_details_section WHERE photo = :photo LIMIT 1");
            $stmt->bindParam(":photo", $imageName);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($result)){
                return array("error" => "", "img_user_id" => null);
            } else{
                return array("error" => "", "img_user_id" => $result[0]["user_id"]);
            }
        } catch (Exception $e) {
            return array("error" => $e->getMessage(), "img_user_id" => null);
        }
    }

    // assign a path to a file
    private function filePath($fileName){
        // assign the extension
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        // hash the name of the file
        $hashedFileName = md5($fileName);
        // assign the path
        $filePath = "../Images/" . $hashedFileName . "." . $extension;

        return $filePath;
    }

    // store an uploaded file
    public function StoreFile(){
        // get the user id
        $userId = $this->auth->getUserId();

        // get the image name (if found) associated to the user
        $userImageName = $this->getUserImgName($userId);
        // get the user (if found) associated to the image name
        $imageNameUser =$this->getImgNameUser($this->imageName);

        if($userImageName["error"] === "" && $imageNameUser["error"] === ""){
            // the user already has an associated image that's already in the filesystem
            if($userImageName["user_img_name"] !== null){
                // the uploaded img name is associated to a user and it's already in the filesystem
                if($imageNameUser["img_user_id"] !== null){
                    // both image names belong to the same user
                    if($userId == $imageNameUser["img_user_id"]){
                        // the images have different contents
                        if(md5_file($this->imageTmpName) !== md5_file($this->filePath($userImageName["user_img_name"]))){
                            // delete the old image
                            unlink($this->filePath($userImageName["user_img_name"]));
                            // store the new image
                            move_uploaded_file($this->imageTmpName, $this->filePath($this->imageName));
                        }
                    } else{
                        return "Please choose another file name 1";
                    }
                } else{
                    // delete the old image
                    unlink($this->filePath($userImageName["user_img_name"]));
                    // store the new image
                    move_uploaded_file($this->imageTmpName, $this->filePath($this->imageName));
                }
            } else if($imageNameUser["img_user_id"] !== null){
                return "Please choose another file name 2";
            } else{
                move_uploaded_file($this->imageTmpName, $this->filePath($this->imageName));
            }
        } else{
            return "An error happened";
        }

        return "";
    }

    // delete the user's image from the filesystem
    public function DeleteUserImg(){
        // get the name of the user's image
        $fileName = $this->getUserImgName($this->auth->getUserId());
        // delete the image from the filesystem
        if($fileName["user_img_name"] !== null){
            unlink($this->filePath($fileName["user_img_name"]));
        }
    }
}

?>