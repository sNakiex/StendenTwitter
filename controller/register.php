<?php
if(empty($_POST)){
    if($user->isSinged()){
        $cms->redirect("home");
        $cms->loadPage("base.twig");
    }else {
        $cms->loadPage("register.twig");
    }
}else{
    if($user->isSinged()){
        $cms->redirect("home");
        $cms->loadPage("base.twig");
    }else{
        //create account
        $user->register($_POST['username'],$_POST['password'],$_POST['password2'],$_POST['email'],$_FILES['avatar']);
    }
}
?>