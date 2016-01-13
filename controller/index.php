<?php
    $laatsteStweets = $db->QueryArray("SELECT * FROM stenden_messages ORDER BY msgId DESC LIMIT 9");
    $cms->addGlobal("laatsteStweets",$laatsteStweets);
    $cms->loadPage("home.twig");
?>