<?php
/*
* @Project: Art Gallery 
* @file appConst.php 
* @description: Defines application settings
*               Constants here should be changed based on the user's preference
*@author: MZ (czanas) on Github
*@email: zackvixacd@gmail.com 
*@date: 2019-10-29
* @date: 2019-10-20
*/


define("DB_LOCATION", dirname( __FILE__ )."/dbs"); /*SQLite dataBase location. This folder should be set to be executable 
                                                    by the apache user. This path will be relative to this file location (appConst.php)
                                                    Please change this location so that people who know about this application do not 
                                                    go snooping around -- Just change "/dbs" to whatever you want. Example "/newFolder"
                                                    */

define("DB_NAME", "myArtDB.db"); /*Name of the SQLite dataBase*/
                                
define("ART_TITLE", "My Humble Art"); /*Title of the HTML Page*/

define("SHOW_RAND_GALLERY", true);/*determine if random gallery should be shown*/
define("MAX_RAND_GALLERY", 6); /*Maximum items to be shown as thumbnail in the gallery. Set it to 0 to show all pictures in the gallery*/

define("JPG_QUALITY", 75); /*Conversion quality from whatever image format used to JPG when uploading*/
                           /*Value can be from 1 to 100*/

define("GALLERY_ACCESS_CODE", ""); /*Leave this blank if you would like the gallery to be accessed without any code.
                                     If you like people to have an access code, defin it here.
                                     Example: define("GALLERY_ACCESS_CODE", "XXYYZZ"); */

define("DEV", false); /*if in development or in production mode. This will be used to show errors or not*/
/*Note: if you are still seeing a blank page without any errors (even if there should be one),
Change the setting in php.ini of display_error
OR
run the php file from command line
*/

/**
*Note: You should probably change the directory where the admin.php is located
*
**/


if(DEV){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
?>