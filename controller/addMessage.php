<?php
    if($user->isSinged()){
        if(isset($_POST['tweet'])){
            $insert["userId"] = MySQL::SQLValue($_SESSION['userId'],"number");
            $insert["message"] = MySQL::SQLValue($_POST['tweet'],"text");
            $db->InsertRow("stenden_messages",$insert);
            $cms->redirect("home");
        }
        $cms->loadPage("base.twig");
    }else{
        $cms->redirect("login");
        $cms->loadPage("base.twig");
    }
?>