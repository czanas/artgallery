<?php
/**
*@Project: Art Gallery 
*@file: auxmanip.php
*contains various functions used by JQuery to make certain post requests from the admin panel
*@description: Tests if everything is set up properly for the application gallery
*@author: MZ (czanas) on Github
*@email: zackvixacd@gmail.com 
*@date: 2019-10-29
**/

require("../funcutils.php");

if(array_key_exists('act', $_POST)){
    
    $act = $_POST['act'];
   
    switch($act){
        case "hide":
            if(array_key_exists('id', $_POST)){
                $id = $_POST['id'];
                if(is_numeric($id)){
                    $_db = new artDB(); 
                    $_db->setItemToHidden($id); 
                    echo miniSummary($_db->getItem($id)); 
                }else{
                    echo ErrorString('Passed id must be numeric'); 
                }
            }
        
        break;
        
        case "getall":
            $_db = new artDB(); 
            $items = $_db->getAllItems();
            foreach($items as $item){
                echo miniSummary($item); 
            }
        
        break;
        
        case "swap":
            if(array_key_exists('ida', $_POST) && array_key_exists('idb', $_POST))
            {
                if( preg_match("/(\d+)/", $_POST['ida'], $matchA) &&  preg_match("/(\d+)/", $_POST['idb'], $matchB) ){
                    $_db = new artDB(); 
                    $idA = $matchA[1];  
                    $idB = $matchB[1]; 
                    $_db->swapItems($idA, $idB);
                    echo $idA."--".$idB;
                    usleep(100);
                }else{
                    echo ErrorString('Passed id must have numeric content'); 
                }
            }
        break;
        
        case "rotatem":
            if(array_key_exists('id', $_POST)){
                $id = $_POST['id'];
                if(is_numeric($id)){
                    $_db = new artDB(); 
                    $item = $_db->getItem($id); 
                    shell_exec("cd ../imgs/; convert -rotate \"-90\" ${item['name']} ${item['name']}; convert -rotate \"-90\" thumb${item['name']} thumb${item['name']}");
                    echo miniSummary($_db->getItem($id)); 
                }else{
                    echo ErrorString('Passed id must be numeric'); 
                }
            }        
        
        break;
        
        case "rotatep":
            if(array_key_exists('id', $_POST)){
                $id = $_POST['id'];
                if(is_numeric($id)){
                    $_db = new artDB(); 
                    $item = $_db->getItem($id); 
                    shell_exec("cd ../imgs/; convert -rotate \"90\" ${item['name']} ${item['name']}; convert -rotate \"90\" thumb${item['name']} thumb${item['name']}");
                    echo miniSummary($_db->getItem($id)); 
                }else{
                    echo ErrorString('Passed id must be numeric'); 
                }
            }             
        
        
        
        break;
        
        case "show":
            if(array_key_exists('id', $_POST)){
                $id = $_POST['id'];
                if(is_numeric($id)){
                    $_db = new artDB(); 
                    $_db->setItemToShown($id); 
                    echo miniSummary($_db->getItem($id)); 
                }else{
                    echo ErrorString('Passed id must be numeric'); 
                }
            }
        break;
        
        
        case "purge":
            if(array_key_exists('id', $_POST)){
                $id = $_POST['id'];
                if(is_numeric($id)){
                    $_db = new artDB(); 
                    $_db->removeItem($id); 
                    echo SuccessString("Attempt Successful"); 
                }else{
                    echo ErrorString('Passed id must be numeric'); 
                }
            }
        break; 
        
    }
    
    
}
?>