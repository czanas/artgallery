<?php
/**
*@Project: Art Gallery 
*@file: adminfuncs.php
*contains various functions that are used for administration purposes
*@description: Admin page used to add, remove, and show art statistics
*@author: MZ (czanas) on Github
*@email: zackvixacd@gmail.com 
*@date: 2019-10-29
**/



/*Sanity Check
* @description: check to see if the file is protected from view via .htaccess
* a user should be set in that case
* Note: If you are using cloudflare, you might have to edit the way you obtain 
*       the logged-in user. 
**/
function getAuthorizedUser(){
    
    $USER_AUTH = !empty($_SERVER['PHP_AUTH_USER'])? $_SERVER['PHP_AUTH_USER']:(!empty($_SERVER['REMOTE_USER'])?$_SERVER['REMOTE_USER']:$_SERVER['REDIRECT_REMOTE_USER']);
    return $USER_AUTH;    
}


/**check if .htaccess is set properly
*   @function: checkAdminSet
*   @description: stops running if username is not set in .htaccess 
*   this function should be called anywhere in the admin folder.
**/
function checkIfAdminSet(){
    if(strlen(getAuthorizedUser())==0){
            die("A Username and password need to be set in the .htaccess file before you can continue");
    }
}

/**
*@description: shows menu options 
*
**/
function showMenu(){
    ?>
        <ul>
            <li><a href='?act=add'>Add New Art</a><br>&nbsp;</li>
            <li><a href='?act=stat'>Stats/Hide/Show/Edit/Purge</a><br>&nbsp;</li>
        </ul>
    
   <?php 
}


/*
*
*
**/
function addMenu(){
    ?>
        <script>
            function verifySub(){
                var ret = false; 
                var title = $('#title').val();
                var caption = $('#caption').val(); 
                var art = $('#newart').val(); 
                var urlart = $('#urlloc').val(); 
                
                if(title=='' || (art=='' && urlart=='') ){
                    
                    var errorText = title==''?"Title":(art==''&&urlart==''?"Art File":'--'); 
                    alert( errorText+" Cannot be left empty."); 
                }else{
                    ret = true;
                }
                if(art!='' && urlart!='')
                {
                    ret = false; 
                    alert('Please either upload an image or provide a link to an image. Not both'); 
                    
                }
                return ret; 
            }
        
            function loadurl(){
                var urlart = $('#urlloc').val();
                console.log('loading url');
                if(urlart != '')
                {
                    $('#imgpreview').html("<b>Image Preview</b>Please check your link if you cannot see any preview.<br>Google Photo? Click first on the image to make it 'full screen' then right click to copy image location<br><img src='"+urlart+"' style='max-width:150px;max-height:150px'>"); 
                }else{
                     $('#imgpreview').html(""); 
                }
                
            }
            
            function clearAll(){
                $('#urlloc').val('');
            }
        </script>
        <b>Option: Add New Item</b><br><br>
        <form action='./?act=addConf' method='post' onSubmit='return verifySub()' enctype="multipart/form-data">
            <label>Choose a file to Upload (png,jpg,jpeg, gif):</label><br>
            <input type='file' id='newart' name='newart' accept=".png, .jpeg, .jpg, .gif"><br> <b>OR</b><br>
            
            <label>Image URL</label><br>
            <input type='text' id='urlloc' name='urlloc' onchange='loadurl()' onfocusout='loadurl()' value=''><br><br>
            
            <span id='imgpreview'></span><br><br>
            <label>Art Title:</label><br>
            <textarea type='text' id='title' name='title' rows="2" cols="33"></textarea><br><br>
            
            <label>Caption (Optional):</label><br>
            <textarea type='text' id='caption' name='caption' rows="5" cols="33"></textarea><br><br>
            
            <label>Order</label><br>
            <input type='radio' name='order' value='first' checked='checked'>First (will be show as most recent item)<br>
            <input type='radio' name='order' value='last'>Last<br>

            <br><br>
            
            <input type="submit" value="submit" name='submit'>
        </form>
    
    
    <?php
    
}



/**
* @description: edit an item
*               loads the item's field and allows them to be edited
*
**/
function editItem(){
    if(array_key_exists('id', $_GET)){
        $id = $_GET['id'];
        if(!is_numeric($id)){
            
            echo ErrorString("invalid id: $id"); 
        }else{
            $_db = new artDB(); 
            $item = $_db->getItem($id);
            $im_data = getimagesize("../imgs/${item['name']}");
            $width=$im_data[1]>150?150:$im_data[1]; 
            
            if($item && $item['id']==$id){
                ?>
                    <script>
                        function verifySub(){
                            var ret = false; 
                            var title = $('#title').val();
                            var caption = $('#caption').val(); 
                            if(title=='' || art=='' || caption==''){
                                
                                var errorText = title==''?"Title":(caption==''?"Caption":":/"); 
                                alert( errorText+" Cannot be left empty."); 
                            }else{
                                ret = true;
                            }
                            return ret; 
                        }
                    
                    </script>
                    <b>Option: Edit Item</b><br><br>
                    <a href='?act=stats'>Cancel</a><br><br><br>
                    <img src='../imgs/thumb<?=$item['name']?>' style='width:<?=$width?>'><br>
                    <form action='./?act=editConf' method='post' onSubmit='return verifySub()' enctype="multipart/form-data">
                        <label>Art Title:</label><br>
                        <textarea type='text' id='title' name='title' rows="2" cols="33"><?=$item['title'];?></textarea><br><br>
                        
                        <label>Caption:</label><br>
                        <textarea type='text' id='caption' name='caption' rows="5" cols="33"><?=$item['caption']?></textarea><br><br>
                        <input type="submit" value="submit" name='submit'>
                        <input type='hidden' value='<?=$item['id']?>' name='id'>
                        <br><br><br>
                        <a href='?act=stats'>Cancel</a>
                    </form>            
                <?php
            }else{
                
                echo ErrorString('no such item in collection'); 
            }
        }
        
    }else{
        
        echo ErrorString("null ID sent"); 
    }
    
}

/**
* @description: edit confirmation of an item
*
*
**/
function editConf(){
    
    if(array_key_exists('id', $_POST) && array_key_exists('title', $_POST) && array_key_exists('caption', $_POST)){
        $id = $_POST['id']; 
        $title = $_POST['title'];
        $caption = $_POST['caption']; 
        $_db = new artDB(); 
        $_db->updateItem($id, $title, $caption); 
        echo SuccessString("Update done!"); 
        echo "<br><br><a href='./?act=stats'>Back to Stats</a><br><br>";
        echo "<a href='./'>Back to Main Menu</a>";
    }        
}
/**
* @description: show stats of all items in gallery
*
*
**/
function showStats(){
    
    $_db = new artDB();
    $all_items = $_db->getAllItems();
    if(DEV){
        print_r($all_items); 
    }
    ?>
        <script>
            /*Script for item manipulation*/
            function funcQuery(id, op){
                    $.post( "./auxmanip.php", 
                            {'id':id, 'act':op}, 
                            function(data){
                                
                            $('#item'+id).html(data); 
                    }); 
                
            }
            
            function populateItems(){
                
                    $.post("./auxmanip.php",
                            {'act':'getall'},
                            function(data){
                               $('#allItemShow').html(data);  
                            });
            }
        </script>
    
    <?php
    echo "<div style='text-align:left'>";

    echo "<b>Directions</b><br>
           <ul>
                <li>Edit Title and Caption of an item</li>
                <li>Change the visibility of an Item by clicking show or hide option.</li>
                <li>To Purge (delete an item), it must first be marked as hidden.</li>
                <li>To Change the order of an item, simply drag and drop that item.</li>
            </ul></div>";
    echo "<b>Total Items = ".$_db->getAllCount().". Shown Items= ".$_db->getShownCount().". Hidden Items= ".$_db->getHiddenCount().".</b>";
    echo "<div style='display:flex;justify-content:space-evenly;flex-wrap:wrap' id='allItemShow'>";
    
    foreach($all_items as $item){
        echo miniSummary($item); 
    }
    
    echo "</div>";
}



/**
*  Confirms the addition of an item
*
*
**/
function addItemConf(){
    /*check if all post items have been set*/
    if(DEV){
        echo "<b>DEV OUTPUT</b><br>";
        print_r($_POST);
        print_r($_FILES);
    }
    /*PHP Error codes to use*/
    $phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );    
    $imageTypeArray = array
    (
        0=>'UNKNOWN',
        1=>'GIF',
        2=>'JPEG',
        3=>'PNG',
        4=>'SWF',
        5=>'PSD',
        6=>'BMP',
        7=>'TIFF_II',
        8=>'TIFF_MM',
        9=>'JPC',
        10=>'JP2',
        11=>'JPX',
        12=>'JB2',
        13=>'SWC',
        14=>'IFF',
        15=>'WBMP',
        16=>'XBM',
        17=>'ICO',
        18=>'COUNT' 
    );
    
    if(array_key_exists('newart', $_FILES)  || (array_key_exists('urlloc', $_POST) && $_POST['urlloc'] != '')
        && array_key_exists('title', $_POST) && array_key_exists('caption', $_POST) && array_key_exists('order', $_POST)){
        
        
        /*check if file was uploaded correctly*/
        if($_FILES['newart']['error'] == 0 || $_POST['urlloc'] != '')
        {     
            /*check if the file uploaded is an image*/
            if(strlen($_FILES['newart']['tmp_name'])>0 &&  getimagesize($_FILES["newart"]["tmp_name"])){
                $imData = getimagesize($_FILES["newart"]["tmp_name"]); 
                $now = time(); 
                $fname = $now.$_FILES["newart"]["name"]; 
                $moved = move_uploaded_file($_FILES["newart"]["tmp_name"], "../imgs/".$fname);
                if( $moved ) {
                    echo "<br>Successfully uploaded<br>";
                   /*converting file to jpg and creating thumbnail*/
                   /*Why convert to jpg? To reduce the filesize*/
                   $rcode = 0; 
                   $out = ""; 
                   if($imData[2] == 1){/*if it is a gif*/
                        $jpg_fname = explode(".", $fname)[0]."conv.gif"; 
                        exec("cd ../imgs && cp \"$jpg_fname\" \"thumb$jpg_fname\" && chmod a+r \"$jpg_fname\" ",
                            $out, $rcode);
                   }else{
                        $jpg_fname = explode(".", $fname)[0]."conv.jpg"; 
                        exec("cd ../imgs && convert -quality ". JPG_QUALITY." \"$fname\" \"$jpg_fname\" && convert -resize 150x \"$jpg_fname\" \"thumb$jpg_fname\" && chmod a+r \"$jpg_fname\" && rm \"$fname\"",
                            $out, $rcode);
                   }

                    if($rcode != 0){
                        
                        echo ErrorString("Error in conversion:".$out); 
                    }else{
                        /*now add item to data base*/
                        $_db = new artDB();
                        if($_POST['order'] == "first"){
                            $_db->addItem($jpg_fname, $_POST['caption'], $_POST['title'], $now, 1); 
                        }else{
                            $order = $_db->getLastItem()['vieworder']-1;
                            //echo "LAST ".$now;
                            $_db->addItem($jpg_fname, $_POST['caption'], $_POST['title'], $now, 1, $order); 
                        }
                        //print_r($_POST);
                        echo SuccessString("Item: ".$_POST['title']." Added!");
                    }
                   
                }else {
                    echo ErrorString("File could not be moved to the imgs folder. Please check that apache has write priviledge to that folder.");
                    
                }
            }elseif(getimagesize($_POST['urlloc'])){
                    $imurl = $_POST['urlloc']; 
                    $now = time(); 
                    $fshort = explode("/",$imurl);
                    $fshort = $fshort[count($fshort)-1];
                    if(strlen($fshort)>255){
                        $fshort = substr($fshort,0,10).substr($fshort,10,10);
                    }
                    
                    $imData = getimagesize($_POST['urlloc']); 
                    
                    $fname = $now.$fshort; 
                    $jpg_fname = explode(".", $fname)[0]."conv.jpg"; 
                    $rcode = 0; 
                    $out = ""; 
                    $cmd = ""; 
                    if($imData[2] == 1){/*if it is a gif*/
                        $jpg_fname = $now.$fshort."conv.gif"; 
                        $cmd = "cd ../imgs && wget \"$imurl\" -O \"$jpg_fname\" && cp \"$jpg_fname\" \"thumb$jpg_fname\" && chmod a+r \"$jpg_fname\" ";
                        exec($cmd, $out, $rcode);
                    }else{
                        //$jpg_fname = explode(".", $fname)[0]."conv.jpg"; 
                        exec("cd ../imgs && wget \"$imurl\" -O \"$fname\" && convert -quality ". JPG_QUALITY." \"$fname\" \"$jpg_fname\" && convert -resize 150x \"$jpg_fname\" \"thumb$jpg_fname\" && chmod a+r \"$jpg_fname\" && rm \"$fname\"",
                            $out, $rcode);
                    }                    
                            
                    if($rcode != 0){                      
                        
                        echo ErrorString("Error Downloading or converting file:".$cmd.">>".$out); 
                        
                    }else{
                        /*now add item to data base*/
                        $_db = new artDB();
                        if($_POST['order'] == "first"){
                            $_db->addItem($jpg_fname, $_POST['caption'], $_POST['title'], $now, 1); 
                        }else{
                            $order = $_db->getLastItem()['vieworder']-1;
                            //echo "LAST ".$now;
                            $_db->addItem($jpg_fname, $_POST['caption'], $_POST['title'], $now, 1, $order); 
                        }
                        //print_r($_POST);
                        echo SuccessString("Item: ".$_POST['title']." Added!");
                    }
            }                    
            else{
                
                echo ErrorString("Uploaded file needs to be an image"); 
            }
            
        }else{
            echo ErrorString("File Not uploaded because of error #".$_FILES["newart"]["error"].": ".$phpFileUploadErrors[$_FILES["newart"]["error"]]);
        }
        
    }else{
      
        echo ErrorString("Error: Empty form elements were sent. All form element must be set."); 
    }
    echo "<br><br><a href='./?act=add'>Add New Art</a><br><br><a href='./'>Back to Main Menu</a>";
    
    
}

/**
*@description: show various flow based on the option clicked
*
**/
function renderAdmin(){
    $act = "";
    if(array_key_exists('act', $_GET)){
        $act = $_GET['act'];
    }
    
    switch($act){
        
        case "edit":
            editItem();
            break; 
        case "editConf":
            editConf();
            break;
        case "stats":
        case "stat":
            showStats();
            break; 
        case "addConf":
            addItemConf(); 
            break;
        case "add":
            addMenu(); 
            break; 
        default:
            showMenu();
            break;
        
    }
}
?>