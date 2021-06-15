<?php
    session_start();
    require_once 'Config/Format.php';
    require_once 'Config/Config.php';
    require_once 'Library/Helper.php';
   //error_reporting(0);
    //autoregistro de clases 
    spl_autoload_register(function($class){
        include DirLibrary.$class.php; 
    });
?>