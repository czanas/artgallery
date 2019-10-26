<?php
    /**
    *@Project: Art Gallery 
    *@file: adminfuncs.php
    *contains various functions that are used for administration purposes
    *@description: Tests if everything is set up properly for the application gallery
    *@author: MZ (czanas) on Github
    *@email: zackvixacd@gmail.com 
    *@date: 2019-10-29
    **/
    
    require('../funcutils.php'); 
    require('./adminfuncs.php');
    $test_db = new artDB();
    checkIfAdminSet();
    /*check if convert is installed*/
    exec("convert -version", $out, $rcode);
    if($rcode != 0)
    {
        die("Error: <b>convert</b> tool not installed. You must install imagick"); 
    }
    echo "If you are seeing this message, everything is working perfectly! Congratulations!"; 

?>