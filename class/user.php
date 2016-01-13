<?php
class user {

    private $userName;
    /**
     * Created by Raymon Boer
     * Date: 11-12-2015
     * Description: Register a user to the database
     */
    public function register($userName,$userPass,$userPassConf,$userEmail,$file){
        GLOBAL $config;
        GLOBAL $db;
        GLOBAL $cms;

        if($userName==="" || $userPass==="" || $userPassConf==="" || $userEmail===""){
            $cms->addGlobal("error","Vult u alle velden in");
            $cms->loadPage("register.twig");
            return 0;
        }else if($userPassConf===$userPass) {
            $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
            if (preg_match($pattern, $userEmail) != 1) {
                // emailaddress is invalid
                $cms->addGlobal("error","Ongeldig email adres");
                $cms->loadPage("register.twig");
                $cms->kill();
            }
            $target_dir = "C:/xampp/htdocs/stenden/public/uploads/";
            $imageFileType = pathinfo($_FILES["avatar"]["name"],PATHINFO_EXTENSION);
            $imageName=md5(md5($_FILES["avatar"]["name"].rand(100,1000)).rand(100,1000)) . ".".$imageFileType;
            $target_file = $target_dir . $imageName;
            // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["avatar"]["tmp_name"]);
                if($check !== false) {
                   // echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                   // echo "File is not an image.";
                    $uploadOk = 0;
                }
            // Check if file already exists
            if (file_exists($target_file)) {
               // echo "Sorry, file already exists.";
                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["avatar"]["size"] > 500000) {
               // echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
               // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                //echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    //echo "The file " . basename($_FILES["avatar"]["name"]) . " has been uploaded.";
                } else {
                    //echo "Sorry, there was an error uploading your file.";
                }
            }


            // Encrypt password
            $rnd = $this->generateSalt();
            $str = substr($userName,0,2).$rnd;
            $salt = $config->bcrypt_salt.$str."$";
            $password = crypt($userPass, $salt);

            //insert user into database
            $insert["userName"] = MySQL::SQLValue($userName, "text");
            $insert["userPass"] = MySQL::SQLValue($password, "text");
            $insert["userSalt"] = MySQL::SQLValue($rnd, "text");
            $insert["userEmail"] = MySQL::SQLValue($userEmail, "text");
            $insert["userImagePath"] = MySQL::SQLValue($imageName, "text");
            $db->InsertRow("stenden_users", $insert);

            //$this->sendConfirmMail($userName, $userEmail); //Word lekker Meteen geactiveerd!
            $cms->redirect("login");
            $cms->loadPage("base.twig");
        }else{
            $cms->addGlobal("error","Wachtwoorden komen niet overeen");
            $cms->loadPage("register.twig");
            return 0;
        }
    }
    /**
     * Created by Raymon Boer
     * Date: 11-12-2015
     * Description: Login the user
     */
    public function login($userName,$userPass){
        GLOBAL $config;
        GLOBAL $db;
        GLOBAL $cms;
        GLOBAL $user;
        $select["userSalt"] = "userSalt";
        $select["userName"] = "userName";
        $filter["userName"] = MySQL::SQLValue($userName,"text");
        $db->SelectRows("stenden_users", $filter,$select);
        $row = $db->Row();
        if($db->RowCount()!=1){
            //account not found
            $cms->addGlobal("error","Account niet gevonden.");
            $cms->loadPage("login.twig");
            return 0;
        }else{
            $str = substr($row->userName,0,2).$row->userSalt;
            $salt = $config->bcrypt_salt.$str."$";
            $password = crypt($userPass, $salt);

            $auth["userName"] = MySQL::SQLValue($userName,"text");
            $auth["userPass"] = MySQL::SQLValue($password,"text");
            $db->SelectRows("stenden_users", $auth);
            $row = $db->Row();
            if($db->RowCount()==1) {
                //set login
                $user->setSinged($row->userId,$row->userName);
                $cms->redirect("home");
                $cms->loadPage("base.twig");
            }else{
                //wrong password
                $cms->addGlobal("error","Gebruikersnaam en wachtwoord komen niet overeen.");
                $cms->loadPage("login.twig");
                return 0;
            }
        }
    }
    /**
     * Created by Raymon Boer
     * Date: 12-12-2015
     * Description: Logout and destroy session
     */
    public function logout($redirect=true){
        GLOBAL $cms;
        session_regenerate_id(true);
        session_destroy();
        $_SESSION=array();
        unset($_SESSION);
        if($redirect){
            $cms->redirect("login");
            $cms->loadPage("base.twig");
        }
        return 0;
    }
    /**
     * Created by Raymon Boer
     * Date: 12-12-2015
     * Description: Send the user an email to activate their account
     * To do: PHP MAIL CLASS
     */
    public function sendConfirmMail($userName,$userEmail){

        return 1;
    }
    /**
     * Created by Raymon Boer
     * Date: 12-12-2015
     * Description: Check if user is loggedin
     */
    public function isSinged(){
        if(isset($_SESSION['isSinged'])){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * Created by Raymon Boer
     * Date: 13-12-2015
     * Description: Create a session and login
     */
    public function setSinged($id,$username){
        GLOBAL $cms;
        GLOBAL $db;
        // Anti session hijacking
        session_regenerate_id(true);
        // user ip (voor sessie hijacking)
        if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
            $cms->setSession("sessCheck_ip",$_SERVER['HTTP_CF_CONNECTING_IP']);
            $update_ip['userIp']=MySQL::SQLValue($_SERVER['HTTP_CF_CONNECTING_IP'],"text");
        }else{
            $cms->setSession("sessCheck_ip",$_SERVER['REMOTE_ADDR']);
            $update_ip['userIp']=MySQL::SQLValue($_SERVER['REMOTE_ADDR'],"text");
        }
        $db->UpdateRows('stenden_users',$update_ip,array("userId"=>$id));

        // Session sets
        $cms->setSession("isSinged",true);
        $cms->setSession("userName",$username);
        $cms->setSession("userId",$id);
        $cms->setSession("sessCheck_browser",$_SERVER['HTTP_USER_AGENT']);
    }
    /**
     * Created by Raymon Boer
     * Date: 13-12-2015
     * Description: Check if user is loggedin
     */
    public function activate($hash){
        return 1;
    }
    /**
     * Created by Raymon Boer
     * Date: 13-12-2015
     * Description: User lost password
     */
    public function lostPassword($email){
        return 1;
    }
    /**
     * Created by Raymon Boer
     * Date: 13-12-2015
     * Description: User lost password
     */
    public function generateSalt(){
        $Allowed_Chars ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
        $Chars_Len = strlen($Allowed_Chars);
        $Salt_Length = 20;
        $salt = "";
        for($i=0; $i<$Salt_Length; $i++)
        {
            $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
        }
        return $salt;
    }
}