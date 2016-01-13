<?php
    $userID = $_GET['profileID'];
    
    $laatsteStweets = $db->QueryArray("SELECT * FROM stenden_messages WHERE `userId` = '$userID' ORDER BY msgId DESC LIMIT 9");
    $cms->addGlobal("userStweets",$laatsteStweets);
    $cms->addGlobal("profileFound",$userID);
    $cms->loadPage("profile.twig");
?>