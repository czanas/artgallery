<?php
/**
*@Project: Art Gallery 
*@file funcutils.php
*@description: file contains various  utility functions to be used in the gallery project
*@author: MZ (czanas) on Github
*@email: zackvixacd@gmail.com 
*@date: 2019-10-29
**/


/**
* @class: SqliteDatabase
* @description: Sqlite Database class that is used to access and manipulate the application database 
*               It also serves as an instantiation of the database if it doesn't exist 
*
*   The schema of the database is as follows: 
*   TABLE `art` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT, 
	`caption`	TEXT,
	`title`	TEXT,
	`date`	INTEGER,
	`show`	INTEGER, 
    `view` INTEGER,
    `vieworder` INTEGER
);
*
**/
/*
$db = new SQLite3;
$statement = $db->prepare('SELECT * FROM table WHERE id = :id;');
$statement->bindValue(':id', $id);

$result = $statement->execute();
*/

require(dirname( __FILE__ ).'/appConst.php'); /*file containing Settings*/

class artDB{
    
    private $sqlDB; 
   
    /**
    *   @desctiption: constructor. Is responsible for checking if the SQLite DB exists or not
    *   @param: location of database, name of database 
    *   @return: none
    **/
    public function __construct($location=DB_LOCATION, $dbName=DB_NAME)
    {
        
        /*check if sqlite exists*/
        if(!extension_loaded('sqlite3')){
            die("Error. This application uses SQLite3 extension. The extension is not installed or not enabled<br>\n
                Enable/Install the extension with: <b>apt-get install php[YOUR_PHP_VERSION]-sqlite3</b>\n<br>
                Then restart the apache server"); 
        }
        /*create or open the database if exists*/
        try{
            $this->sqlDB = new SQLite3("$location/$dbName");
            $this->sqlDB->busyTimeout(5000);
            $this->sqlDB->exec("PRAGMA journal_mode = wal;"); 
            $sql_create = "CREATE TABLE IF NOT EXISTS `art` (`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
                                          `name` TEXT,
                                          `caption`	TEXT,
                                          `title`	TEXT,
                                          `date`	INTEGER,
                                          `show`	INTEGER, 
                                          `view`    INTEGER DEFAULT 0,
                                          `vieworder` INTEGER)";                     
            $this->sqlDB->exec($sql_create); 
        }catch (Exception $e){
            if($location != "." && $location !="./")
            {
                /*check if folder already exists*/
                exec("ls $location", $retA, $rcode); 
                if($rcode == 0){
                    exec("mkdir $location", $retA, $rcode); 
                    if($rcode == 0) {
                        echo "Error: $retA";
                        echo "<br>\nPlease give the server user (apache -- maybe) permission to create $location Or manually create such folder."; 
                    }
                }
                $ret =  shell_exec("echo '--' >> ./$location/index.html");
                if(strlen($ret)>0){
                    echo "Error: $ret";
                    echo "<br>\Note: the server user (apache -- maybe) needs to have rwx permission on $location to perform SQLite operations";
                }
            }
            echo shell_exec("chmod u+rwx $location");
            /*create the db*/
            $queryCreate =  "CREATE TABLE `art` (`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
                                          `name` TEXT,
                                          `caption`	TEXT,
                                          `title`	TEXT,
                                          `date`	INTEGER,
                                          `show`	INTEGER,
                                          `view` INTEGER DEFAULT 0, 
                                          `vieworder` INTEGER)";
            try{
                //echo "$location/$dbName";
                $this->sqlDB = new SQLite3("$location/$dbName");
                $this->sqlDB->busyTimeout(5000);
                $this->sqlDB->exec("PRAGMA journal_mode = wal;"); 
                $this->sqlDB->exec($queryCreate); 
            }catch (Exception $e){
                echo "Error. Couldn't create SQLite DataBase. Probably some permission issues."; 
                echo "<br><b>Useful Command to change access without changing ownership :</b>\n setfacl -m 'u:programX:rwx' /folder <br>";
                echo "\nchgrp www-data ./folderName<br>";
                echo "\nchmod g+rwx www-data"; 
                echo "<br>www-data is usually the right group";
                echo die(); 
            }
        }
        
    }

    /**
    * @description: get the count of all items in the database
    * @param none:
    * @return the number of shown items in the database
    *
    **/
    public function getAllCount(){
        $countVal = $this->sqlDB->querySingle("select count(*) as ct from art ", true); 
        return $countVal['ct'];          
    }
    
    /**
    * @description: get the count of shown items in the dataBase
    * @param none:
    * @return the number of shown items in the database
    *
    **/
    public function getShownCount(){
        $countVal = $this->sqlDB->querySingle("select count(*) as ct from art where show='1'", true); 
        return $countVal['ct'];          
    }

    /**
    * @description: get the count of hidden items in the dataBase
    * @param none:
    * @return the number of shown items in the database
    *
    **/
    public function getHiddenCount(){
        $countVal = $this->sqlDB->querySingle("select count(*) as ct from art where show='0'", true); 
        return $countVal['ct'];          
    }
    
    
    /**
    * @description: insert an art item into the database 
    * @param: name (string), caption (string), title (string), date (integer), show (integer)
    * @return: none 
    */
    public function addItem($name, $caption, $title, $date, $show, $ordering=0)
    {
        $smt = $this->sqlDB->prepare("insert into art (name, caption, title, date, show, vieworder) values (:name, :caption, :title, :date, :show, :order)"); 
        $smt->bindValue(':name', $name); 
        $smt->bindValue(':caption', $caption);
        $smt->bindValue(':title', $title); 
        $smt->bindValue(':date', $date); 
        $smt->bindValue(':show', $show); 
        $order = $ordering==0?time():$ordering; 
        $smt->bindValue(':order', $order); 
        
        $smt->execute(); 
    }
    
    /**
    * @description: returns one item from the gallery based on its id
    * @param: id (integer)
    * @return: return the last item in the gallery if the id doesn't match anything 
    **/
    public function getItem($id){
        $smt = $this->sqlDB->prepare("select * from art where id= :id limit 1"); 
        $smt->bindValue(':id', $id); 
        $result = $smt->execute(); 
        $itemData = $result->fetchArray();//returns false is nothing
        if(!$itemData){
            $itemData = $this->sqlDB->querySingle("select * from art order by vieworder DESC limit 1", true); 
        }
        return $itemData; 
    }
    
    /**
    * @description: returns one item from the gallery based on its id and if it is shown
    * @param: id (integer)
    * @return: return the last item in the gallery if the id doesn't match anything 
    **/
    public function getShownItem($id){
        $smt = $this->sqlDB->prepare("select * from art where id= :id and show='1' limit 1"); 
        $smt->bindValue(':id', $id); 
        $result = $smt->execute(); 
        $itemData = $result->fetchArray();//returns false is nothing
        if(!$itemData){
            $itemData = $this->sqlDB->querySingle("select * from art where show='1' order by vieworder DESC limit 1", true); 
        }
        return $itemData; 
    }

    /**
    * @description: returns the item at the bottom of the queue (ordering)
    * @param: id (integer)
    * @return: return the last item in the gallery
    **/
    public function getLastItem(){
        $itemData = $this->sqlDB->querySingle("select * from art order by vieworder ASC limit 1", true);  
        return $itemData; 
    }
    
    /**
    * @description: returns the item at the bottom of the queue (ordering)
    * @param: id (integer)
    * @return: return the last item in the gallery
    **/
    public function getLastShownItem(){
        $itemData = $this->sqlDB->querySingle("select * from art where show='1' order by vieworder ASC limit 1", true);  
        return $itemData; 
    }
    
    /**
    * @description: returns the item at the top of the queue (ordering)
    * @param: id (integer)
    * @return: return the last item in the gallery
    **/
    public function getFirstItem(){
        $itemData = $this->sqlDB->querySingle("select * from art order by vieworder DESC limit 1", true);  
        return $itemData; 
    }

    /**
    * @description: returns the item at the top of the queue (ordering)
    * @param: id (integer)
    * @return: return the last item in the gallery
    **/
    public function getFirstShownItem(){
        $itemData = $this->sqlDB->querySingle("select * from art where show='1' order by vieworder DESC limit 1", true);  
        return $itemData; 
    }
    
    /**
    * @description: returns an item preceeding $id or false if nothing is found
    *
    **/
    public function getPrevItem($id){
        $smt = $this->sqlDB->prepare("select * from art where id = :id and show='1' "); 
        $smt->bindValue(':id', $id); 
        $result = $smt->execute(); 
        $itemDataID = $result->fetchArray();

        if(isItem($itemDataID)){
            $smt = $this->sqlDB->prepare("select * from art where vieworder < :vieworder and show='1' order by vieworder DESC limit 1"); 
            $smt->bindValue(':vieworder', $itemDataID['vieworder']); 
            $result = $smt->execute(); 
            $itemData = $result->fetchArray();
        }
        return $itemData;         
    }
    
    /**
    * @description: returns an item following $id or false if nothing is found
    *
    **/
    public function getNextItem($id){
        $smt = $this->sqlDB->prepare("select * from art where id = :id and show='1' "); 
        $smt->bindValue(':id', $id); 
        $result = $smt->execute(); 
        $itemDataID = $result->fetchArray();

        if(isItem($itemDataID)){
            $smt = $this->sqlDB->prepare("select * from art where vieworder > :vieworder and show='1' order by vieworder ASC limit 1"); 
            $smt->bindValue(':vieworder', $itemDataID['vieworder']); 
            $result = $smt->execute(); 
            $itemData = $result->fetchArray();
        }
        return $itemData;                  
    }
    /**
    * @description: returns n items from the dataBase, ordery by ID descending
    * @param: $n (integer) number of items to return if <= 0, returns all items
    *         $rand (boolean) whether to get random items or not
    *         $show (-1) return all items 
    *               (0) returns all items marked as hidden 
    *               (1) returns all items marked as shown
    *         $exclude (-1) the id to exclude
    * @return: returns an array of items
    **/
    private function genericGetnItems($n, $rand=false, $show=-1, $exclude=-1){
        
        $extraQuery = $rand==true?" ORDER BY RANDOM() DESC ":" ORDER  BY vieworder DESC "; 
        $limit = $n>0?" limit :n ": " "; 
        $extraShow = $show==0?" and show='0' ":($show==1?" and show='1' ":" "); 
        
        $smt = $this->sqlDB->prepare("select * from art where id <> :id $extraShow $extraQuery $limit  ");

        $smt->bindValue(':id', $exclude);
        if($n>0){
            $smt->bindValue(':n', $n); 
        }
        
        $result = $smt->execute(); 
        $bigArray = Array(); 
        while($itemData = $result->fetchArray()){
            $bigArray[] = $itemData; 
        }
        
        return $bigArray;         
    }
    
    /*@description: get all items*/
    public function getAllItems()
    {
        return $this->genericGetnItems(0); 
    }
    /*@description: returns n items*/
    public function getnItems($n){
        
        return $this->genericGetnItems($n, false, -1);
    }
    
    /*@description: returns n hidden items in the db*/
    public function getnHiddenItems($n){
        
        return $this->genericGetnItems($n, false, 0);
    }
    /*@description: returns n random hidden items in the db*/
    public function getnRandomHiddenItems($n){
        
        return $this->genericGetnItems($n, true, 0);
    }
    /*@description: returns all hidden items in the db*/
    public function getAllHiddenItems(){
        
        return $this->genericGetnItems(0, false, 0);
    }
    
    /*@description: returns n items marked as shown*/
    public function getnShownItems($n){
       return $this->genericGetnItems($n, false, 1); 
    }  
    /*@description: return n random items marked as shown */
    public function getnRandomShownItems($n, $exclude=-1){
        return $this->genericGetnItems($n, true, 1, $exclude); 
    }
    /*@description: returns n items marked as shown*/
    public function getAllShownItems(){
       return $this->genericGetnItems(0, false, 1); 
    }
    
    
    /*@description update the view of an item*/
    public function incView($id)
    {
        $smt = $this->sqlDB->prepare("UPDATE art set view=view+1 where id= :id"); 
        $smt->bindValue(':id', $id);
        $smt->execute();     
    }
    
    /**
    * @description: delete an item from the database with a particular id. Before the item is deleted
    *                           it must be marked as hidden 
    * @param: $id (integer). This is to be called only from the admin panel which should be one level down
    * @return: none
    **/
    public function removeItem($id){
        $item = $this->getItem($id); 
        /*delete files*/
        if(!unlink("../imgs/thumb${item['name']}")){
            die("error deleting thumbnail file"); 
        }
        if(!unlink("../imgs/${item['name']}")){
            die("error deleting main file"); 
        }
        /*remove from database*/
        $smt = $this->sqlDB->prepare("DELETE FROM art where id= :id and show='0' "); 
        $smt->bindValue(':id', $id); 
        $smt->execute();        
        
    }
    
    /**
    * @description: mark an item as shown
    * @param: $id (integer)
    * @return: none
    **/
    public function setItemToHidden($id){
        $smt = $this->sqlDB->prepare("UPDATE art set show='0' where id= :id"); 
        $smt->bindValue(':id', $id); 
        $smt->execute();        
        
    }  
    
    /**
    * @description: mark an item as shown
    * @param: $id (integer)
    * @return: none
    **/
    public function setItemToShown($id){
        $smt = $this->sqlDB->prepare("UPDATE art set show='1' where id= :id"); 
        $smt->bindValue(':id', $id); 
        $smt->execute();        
        
    }  
    
    /**
    *   @description: updates titles and caption of an item 
    *   @param id, title, caption 
    *   @return none: 
    **/
    public function updateItem($id, $title, $caption){
        
        $smt = $this->sqlDB->prepare("UPDATE art set title = :title , caption = :caption where id = :id"); 
        $smt->bindValue(':title', $title); 
        $smt->bindValue(':caption', $caption); 
        $smt->bindValue(':id', $id); 
        $smt->execute(); 
    }
    
    /**
    * @description: set the viewOrder of an item 
    * @param: $id (int) and $vieworder (int)
    * return: none
    **/
    public function setViewOrder($id, $viewOrder){
       $smt = $this->sqlDB->prepare("UPDATE art set vieworder = :vieworder where id = :id"); 
       $smt->bindValue(':id', $id); 
       $smt->bindValue(':vieworder', $viewOrder); 
       $smt->execute(); 
    }


    /**
    * @description: swaps the ordering of two items
    * @param: id of both items 
    * @return none:
    *   
    **/
    public function swapItems($ida, $idb){
            $itemA = $this->getItem($ida); 
            $itemB = $this->getItem($idb);
            if(isItem($itemA) && isItem($itemB)){
                $this->setViewOrder($itemA['id'], $itemB['vieworder']);
                $this->setViewOrder($itemB['id'], $itemA['vieworder']);
            }
    }    
}



/**
* @description: check if an array is a gallery item
*
*
**/
function isItem($item)
{
    return (is_array($item) && array_key_exists('title', $item)
                           && array_key_exists('caption', $item)
                           && array_key_exists('name', $item)
                           && array_key_exists('id', $item)
                           && array_key_exists('show', $item)
                           && array_key_exists('view', $item)   
                           && array_key_exists('vieworder', $item));
}

/**
* @description: a mini utility function that formats a gallery item in a manner that can be displayed 
*               in the admin panel
* @param: $item (array) needs to be an array with title, caption, name, id, show, view as keys
**/
function miniSummary($item){
    
    $string = "";
    
    if(isItem($item) ){
                       
            $now=time(); 
            $cap = strlen($item['caption'])>10?substr($item['caption'],0,10)."...":$item['caption'];
            $string.= "\n\n<div style='border:1px solid black;padding:5px;margin-top:5px;margin-bottom:5px;max-width:200px' id='item${item['id']}' name='item${item['id']}'
                        ondrop='drop(event, this.id)' ondragover='allowDrop(event)' >";
            $string.= "<b>Title</b>: ".$item['title']."<br>";
            $string.= "<img src='../imgs/thumb".$item['name']."?t=$now'  draggable='true' ondragstart='drag(event, this.id)' id='drag${item['id']}' style='max-width:200px'><br>";
            $string.= "<b>Caption</b>: ".$cap."<br>";
            $string.= "<b>Views</b>: ".$item['view']."<br>";
            $string.= "<b>Visibility</b>: "; 
            $string.= $item['show']==0?"<span style='color:#AA0000'>hidden</span><br>":"<span style='color:#00AA00'>shown"."</span><br>";
            $string.= date("Y-m-d @ h:i:s T", $item['date']);
            //$string.= "<br><br>Admin Options:<br>";
            $string.= "<br><a href='#item${item['id']}' onclick='funcQuery(${item['id']}, \"rotatep\")' >Rotate +90 Degrees &orarr;</a><br>";
             $string.= "<a href='#item${item['id']}' onclick='funcQuery(${item['id']}, \"rotatem\")' >Rotate -90 Degrees &olarr;</a><br><br>";
            $string.= "<a href='./?act=edit&id=${item['id']}'>Edit Title/Caption</a> |"; 
            if($item['show']==0){
                $string.= " <a href='#item${item['id']}' onclick='funcQuery(${item['id']}, \"show\")' >Show</a> | 
                            <a href='#item${item['id']}' onclick='funcQuery(${item['id']}, \"purge\")'>Purge</a> ";
            }else{
                $string.= " <a href='#item${item['id']}' onclick='funcQuery(${item['id']}, \"hide\")' >Hide</a> "; 
            }  
            $string .="</div>\n\n";
    
    }else{
        $string = "argument is not a gallery item"; 
    }
    return $string; 
}

/**
* @description: show gallery item in a simple div
*
**/
function itemsInDiv($item){ 
    $string = "";
    if(isItem($item))
    {
        $string .= "<div onclick='moveTo(${item['id']})' class='galleryItem'>";
        $string .= "<img src='./imgs/thumb${item['name']}' style='width:100%'>";
        $string .= "</div>"; 
    }else{
        
        $string .= "<div>not a gallery item</div>"; 
    } 
    return $string;
}
/**
* @desscription: simple function to decorate a text as an error 
* @param: a string as error 
* @return: formated error 
**/
function ErrorString($text){
    return "<p style='background-color:\"#696969\";border:1px dashed black;'><b style='color:#AA0000'>Error:</b> $text<br></p>"; 
}

/**
* @desscription: simple function to decorate a text as a success 
* @param: a string as a success 
* @return: formated success 
**/
function SuccessString($text){
    return "<p style='background-color:\"#696969\";border:1px dashed black;'><b style='color:#00AA00'>Success:</b> $text<br></p>"; 
}


/**
* @description main function to render the gallery 
* @param: none
**/
function renderGallery(){

        $_db = new artDB();
        /*default value for empty database*/
        $thisItem = array("caption"=>"Your  Gallery is Empty. Please add new items!", "name"=>"empty.jpg", "title"=>"Empty Gallery", "date"=>0, "id"=>0); 
        $prevItem = array("caption"=>"Your  Gallery is Empty. Please add new items!", "name"=>"empty.jpg", "title"=>"Empty Gallery", "date"=>0, "id"=>-1); 
        $nextItem = array("caption"=>"Your  Gallery is Empty. Please add new items!", "name"=>"empty.jpg", "title"=>"Empty Gallery", "date"=>0, "id"=>-1); 
        $width = 100; 
        $id = 0; 
        $prevId = -1; 
        $nextId = -1; 
        
        $last_id = -1; 
        $first_id = -1; 
        
        $mobileLeft = "";
        $mobileRight = ""; 
        
        if($_db->getShownCount() == 0){
            
            
        }else{
            
            if(array_key_exists('id', $_GET))
            {
                if(is_numeric($_GET['id'])){
                    $id = $_GET['id'];
                }
            }
            
            $thisItem = $_db->getShownItem($id); 
            $im_data = getimagesize("./imgs/${thisItem['name']}");
            $width=$im_data[1]>590?590:$im_data[1];
            /*update the item view count*/
            $_db->incView($thisItem['id']);
            
            /*get Id of the previous and next items*/
            $prevItem = $_db->getPrevItem($thisItem['id']); 
            $nextItem = $_db->getNextItem($thisItem['id']);
            
            $nextId = $nextItem?$nextItem['id']:-1; 
            $prevId = $prevItem?$prevItem['id']:-1; 
            
            $firstItem = $_db->getLastItem(); 
            $first_id = $firstItem?$firstItem['id']:-1; 
            
            $lastItem = $_db->getFirstItem(); 
            $last_id = $lastItem?$lastItem['id']:-1;
        }
        
        if($prevId != -1){
            $mobileLeft = "style=\"background:url('./imgs/thumb${prevItem['name']}');background-size:50%;
                                                    background-repeat:no-repeat;background-position:center\"";
        }
        if($nextId != -1){
            $mobileRight = "style=\"background:url('./imgs/thumb${nextItem['name']}');background-size:50%;
                                                    background-repeat:no-repeat;background-position:center\"";
        }
        
        //write these values to JavaScript
        ?>
        <script>
            /*values for next and previous set on server side*/
            var prev_id = <?=$prevId?>;
            var next_id = <?=$nextId?>;
            var this_id = <?=$id?>;
        </script>
        
        <?php
        if($prevItem){
            
        }
        /*DEV DEBUG*/
        if(DEV){
            print_r($thisItem);
            print_r($prevItem);
            print_r($nextItem);
        }
        
    ?>
    <!--style='box-shadow:0 0 0 10px hsl(0, 0%, 60%),0 0 0 15px hsl(0, 0%, 90%);border: 5px solid hsl(0, 0%, 40%);  padding: 5px;' align='center'-->
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close">Close or [ESC]</span>
        <img class="modal-content" id="img01" src='./imgs/blank.jpg' alt=''>
        <div id="caption"></div>
    </div> 
    
    <div style='width:100%;padding:10px;margin:auto'>
        <div id='biggerContainer' style='background-color:hsl(0, 0%, 80%);box-shadow:0 0 0 10px hsl(0, 0%, 60%),0 0 0 15px hsl(0, 0%, 90%);border: 5px solid hsl(0, 0%, 40%);  padding: 5px;'>
            <!-- row flex -->
            <div id='mainContainer'> 
            
                <!-- Title -->
                <div id='titlePart'>
                    <?php
                        echo $thisItem['title'];
                    ?>
                </div>
                <div id='mainImageContainer'>
                    <!-- mobile navigation -->
                    <div id='fastPreviousPartMobile' onclick='moveFirst()'>&nbsp;&laquo;</div>
                    <div id='previousPartMobile' onclick='movePrevious()' <?=$mobileLeft?> >&Larr;&nbsp;</div> 
                    <div id='nextPartMobile' onclick='moveNext()'         <?=$mobileRight?>>&nbsp;&Rarr;</div>
                    <div id='fastNextPartMobile' onclick='moveLast()'>&raquo;&nbsp;</div>                    
                    <!-- Left arrow -->
                    <div id='fastPreviousPart' onclick='moveFirst()'>&nbsp;&laquo;</div>
                    <div id='previousPart' onclick='movePrevious()' >&nbsp;&Larr;</div> 
                    
                    <!-- Main Drawing -->
                    <div id='imagePart'>
                        <?php

                            echo "<img src='./imgs/${thisItem['name']}' id='myImg' alt='${thisItem['title']}<br>${thisItem['caption']}'>";
                            echo "<br style='display:block; margin-top:5px; line-height:5px;'><i style='font-size:10px'>&uarr; click for full screen or press [F] key.&uarr;<br>Use arrow keys to navigate</i>";
                        ?>
                            <script>
                                if(<?=$width?> < $('#imagePart').width())
                                {
                                    $('#myImg').css({'width':<?=$width?>+'px'});
                                }
                                //$('#myImg').css({'width':imH+'px'});
                                //$('#myImg').width(imH);
                                //alert(imH+' '+window.innerWidth);
                            </script>
                    </div> 
                    
                    <!-- Right arrow -->
                    <div id='nextPart' onclick='moveNext()'>&Rarr;&nbsp;</div> 
                    <div id='fastNextPart' onclick='moveLast()'>&raquo;&nbsp;</div>
                </div>
                <!-- caption -->
                <div id='captionPart'>
                        <span id='captionText'><b><u>Caption</u>:</b> <i>Uploaded on <?=date('Y-m-d @ H:i T', $thisItem['date'])?></i><br>
                            <?=$thisItem['caption']?>
                        </span>
                        
                </div> 
                <div><a href='#showall' onclick='showAllThumb()'>Show all Items in Gallery</a><br>&nbsp;</div>
                <!-- other items in the gallery -->
                <div id='galleryContainer'>
                       
                    <?php
                    
                    if(SHOW_RAND_GALLERY){
                        ?>

                            <div id='randTitle'>Random Gallery Items (out of <b><?=$_db->getShownCount()?></b>)</div>
                            <?php
                                $galleryItems = $_db->getnRandomShownItems(MAX_RAND_GALLERY, $thisItem['id']);
                                foreach($galleryItems as $items)
                                {
                                    $im_data = getimagesize("./imgs/${items['name']}");
                                    $width=$im_data[1]>100?100:$im_data[1]; 
                                    echo "<div class='galleryItem' onclick='moveTo(${items['id']})'><img src='./imgs/thumb${items['name']}' style='width:100%' alt='gallery item'></div>\n";
                                }
                            ?>
                        
                        <?php
                    }
                    ?>
                </div>
            </div>
            
            
            <div id='credit'>
                Art Gallery: A Midnight Zen Production. version 1.0. <i><a href='https://github.com/czanas/artgallery' target='_blank'>@github</a></i>
            </div>
        </div>
        
    </div> 

            <script>
            /*
                $(document).bind('keydown', 'right', function(){moveNext();});
                $(document).bind('keydown', 'left',  function(){movePrevious();});
                */
            var fullScreenMode = false; 
            var KEYCODES={"backspace":8,"tab":9,"enter":13,"shift":16,"ctrl":17,"alt":18,"pausebreak":19,"capslock":20,"esc":27,"space":32,
                          "pageup":33,"pagedown":34,"end":35,"home":36,"leftarrow":37,"uparrow":38,"rightarrow":39,
                          "downarrow":40,"insert":45,"delete":46,"0":48,"1":49,"2":50,"3":51,"4":52,"5":53,"6":54,"7":55,
                          "8":56,"9":57,"a":65,"b":66,"c":67,"d":68,"e":69,"f":70,"g":71,"h":72,"i":73,"j":74,"k":75,"l":76,
                          "m":77,"n":78,"o":79,"p":80,"q":81,"r":82,"s":83,"t":84,"u":85,"v":86,"w":87,"x":88,"y":89,"z":90,
                          "leftwindowkey":91,"rightwindowkey":92,"selectkey":93,"numpad0":96,"numpad1":97,"numpad2":98,
                          "numpad3":99,"numpad4":100,"numpad5":101,"numpad6":102,"numpad7":103,"numpad8":104,"numpad9":105,
                          "multiply":106,"add":107,"subtract":109,"decimalpoint":110,"divide":111,"f1":112,"f2":113,"f3":114,
                          "f4":115,"f5":116,"f6":117,"f7":118,"f8":119,"f9":120,"f10":121,"f11":122,"f12":123,"numlock":144,
                          "scrolllock":145,"semicolon":186,"equalsign":187,"comma":188,"dash":189,"period":190,"forwardslash":191,
                          "graveaccent":192,"openbracket":219,"backslash":220,"closebracket":221,"singlequote":222};    


                function advanceGalleryRight(){
                    if(fullScreenMode && next_id != -1){
                                //document.getElementById("img01").src = './imgs/loading.gif'; 
                                //document.getElementById("myImg").src = './imgs/loading.gif';
                                captionText.innerHTML = "loading...";
                                $.post('./logger.php', {'getData':'getData', 'id':next_id},
                                function(data){
                                    console.log(data);
                                    var obj = JSON.parse(data); 
                                    next_img = obj.next_img; 
                                    this_id  = obj.id; 
                                    next_id  = obj.next_id; 
                                    prev_id  = obj.prev_id; 
                                    document.getElementById("img01").src = './imgs/'+obj.img; 
                                    document.getElementById("myImg").src = './imgs/'+obj.img;
                                    $('#previousPart').html("&Larr;&nbsp;<br><img src='./imgs/thumb"+obj.prev_img+"' style='width:50px'><br>&Larr;&nbsp;");
                                    if(next_id != -1){
                                        $('#nextPart').html("&Rarr;&nbsp;<br><img src='./imgs/"+obj.next_img+"' style='width:50px'><br>&Rarr;&nbsp;");
                                    }else{
                                        $('#nextPart').html("&Rarr;&nbsp;<br>&Rarr;&nbsp;");
                                    }
                                    captionText.innerHTML = obj.title;                                                 
                                });
                    }else{
                        moveNext();
                    }                        
                }
                
                function advanceGalleryLeft(){
                    if(fullScreenMode && prev_id !=-1){
                                //document.getElementById("img01").src = './imgs/loading.gif'; 
                                //document.getElementById("myImg").src = './imgs/loading.gif';
                                 captionText.innerHTML = "loading...";
                                $.post('./logger.php', {'getData':'getData', 'id':prev_id},
                                function(data){
                                    console.log(data);
                                    var obj = JSON.parse(data); 
                                    next_img = obj.next_img; 
                                    this_id  = obj.id; 
                                    next_id  = obj.next_id; 
                                    prev_id  = obj.prev_id; 
                                    document.getElementById("img01").src = './imgs/'+obj.img; 
                                    document.getElementById("myImg").src = './imgs/'+obj.img; 
                                    captionText.innerHTML = obj.title;   
                                    $('#nextPart').html("&Rarr;&nbsp;<br><img src='./imgs/"+obj.next_img+"' style='width:50px'><br>&Rarr;&nbsp;");
                                    if(prev_id != -1){
                                        $('#previousPart').html("&Larr;&nbsp;<br><img src='./imgs/thumb"+obj.prev_img+"' style='width:50px'><br>&Larr;&nbsp;");
                                    }else{
                                       $('#previousPart').html("&Larr;&nbsp;<br>&Larr;&nbsp;");
                                    }                                    
                                });
                    }else{                                
                        movePrevious();
                    }                    
                }        
                
                function moveLast(){
                    moveTo(<?=$last_id?>);
                }
                
                function moveFirst(){
                    moveTo(<?=$first_id?>);
                }
                document.addEventListener('keydown', function(e){
                            switch(e.keyCode){
                                case KEYCODES['rightarrow']:
                                    advanceGalleryRight(); 
                                break;
                                case KEYCODES['leftarrow']:
                                    advanceGalleryLeft(); 
                                break;
                                
                                case KEYCODES['f']:
                                    img.onclick();
                                break;
                                
                                case KEYCODES['esc']:
                                    span.onclick();
                                break;
                            }                          
                    });
                
                detectswipe('img01', mobileSwipe);  
                detectswipe('caption', mobileSwipe);
                
                function mobileSwipe(el, dir){
                    switch(dir){
                        case 'r':
                            advanceGalleryLeft();
                        break;
                        
                        case 'l':
                            advanceGalleryRight(); 
                        break;  
                    }                           
                }
                
                if(prev_id != -1){
                    $('#previousPart').html("&Larr;&nbsp;<br><img src='./imgs/<?="thumb".$prevItem['name']?>' style='width:50px'><br>&Larr;&nbsp;");
                    //$('#previousPartMobile').html("<div align='center'>&Larr;&nbsp;<img src='./imgs/<?="thumb".$prevItem['name']?>' style='max-width:50%;max-height:50px' align='middle'></div>");
                }
                if(next_id != -1){
                    $('#nextPart').html("&Rarr;&nbsp;<br><img src='./imgs/<?="thumb".$nextItem['name']?>' style='width:50px'><br>&Rarr;&nbsp;");
                    //$('#nextPartMobile').html("<div align='center'><img src='./imgs/<?="thumb".$nextItem['name']?>' style='max-width:50%;max-height:50px' align='middle'>&nbsp;&Rarr;</div>");
                }
                
                // Get the modal
                var modal = document.getElementById("myModal");

                // Get the image and insert it inside the modal - use its "alt" text as a caption
                var img = document.getElementById("myImg");
                var modalImg = document.getElementById("img01");
                var captionText = document.getElementById("caption");
                //var captionText2 = document.getElementById("caption2");
                img.onclick = function(){
                  modal.style.display = "block";
                  modalImg.src = this.src;
                  captionText.innerHTML = this.alt;
                  fullScreenMode = true;
                  //captionText2.innerHTML = this.alt;
                }

                // Get the <span> element that closes the modal
                var span = document.getElementsByClassName("close")[0];

                // When the user clicks on <span> (x), close the modal
                span.onclick = function() { 
                  modal.style.display = "none";
                  fullScreenMode  = false; 
                }        

                function showAllThumb(){
                    $.post('./logger.php', {'showall':'yes'},
                            function(data){
                               $('#galleryContainer').html(data);
                                
                            });
                }
            </script>    
    <?php   
}

/**
* @description: check to see if a user has access to the gallery 
*
*
**/
function hasAccess(){
    $access = false; 
    if(GALLERY_ACCESS_CODE == "")
    {
        $access = true; 
    }else{
        if(array_key_exists("access_code", $_COOKIE)){
            if($_COOKIE['access_code'] == GALLERY_ACCESS_CODE){
                $access = true; 
            }
        }
    }
    return $access; 
}

/**
*
*
*
**/
function promptAccess(){
    
    ?>
        <div style='width:100%'>
            <div align='center'>
                <div style='width:600px;background-color:#AAAAAA;margin-top:100px;border:1px solid black' align='center' >An access code is required to view this gallery<br>
                    <form action='./logger.php' method='post'>
                        <label>Enter Access Code</label>:<br>
                        <input type='text' name='ACCESS_CODE' value=''><br>
                        <input type='submit' value='Submit'>
                    </form>
                    &nbsp;
                </div>
            </div>
            <div id='credit'>
                Art Gallery: A Midnight Zen Production. version 1.0. <i><a href='https://github.com/czanas/artgallery' target='_blank'>@github</a></i>
            </div>
        </div>
    <?php
}
?>