<?php
    $router->map('/',array('url' => 'index'));
    $router->map('/home/',array('url' => 'index'));
    $router->map('/login/',array('url' => 'login'));
    $router->map('/logout/',array('url' => 'logout'));
    $router->map('/aanmelden/',array('url' => 'register'));
    $router->map('/profile/',array('url' => 'profile'));
    $router->map('/profile/:profileID',array('url' => 'profile'));
    $router->map('/addMessage/',array('url' => 'addMessage'));
    $router->map('/error/',array('url' => 'error'));
?>