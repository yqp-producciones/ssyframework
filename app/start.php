<?php
    session_start();
    require_once 'Config/Config.php';
    require_once 'Library/Helper.php';
    spl_autoload_register(function($class){
        include DirLibrary.$class.php; 
    });
?>