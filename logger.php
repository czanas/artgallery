<?php
if(array_key_exists('ACCESS_CODE', $_POST)){
    require('./appConst.php'); 
    if($_POST['ACCESS_CODE'] == GALLERY_ACCESS_CODE){
            setcookie('access_code', GALLERY_ACCESS_CODE, time() + (86400 * 30 * 30), "/"); //set cookie for 30 days         
    }
    header('location: ./'); 
}elseif(array_key_exists('showall', $_POST))
{
    require('./funcutils.php');
    if(hasAccess())
    {
        $_db = new artDB(); 
        $items = $_db->getAllShownItems();
        $out = "";
        foreach($items as $item){
            $out.= itemsInDiv($item); 
        }
        echo $out; 
    }
}elseif(array_key_exists('getData', $_POST) && array_key_exists('id', $_POST)){
        require('./funcutils.php');
        if(hasAccess()){
            $_db = new artDB(); 
            $item = $_db->getShownItem($_POST['id']); 
            $nextItem = $_db->getNextItem($_POST['id']);
            $prevItem = $_db->getPrevItem($_POST['id']);
            
            $next_id = -1; 
            $next_img = ""; 
            $next_title = "";

            $prev_id = -1; 
            $prev_img = ""; 
            $prev_title = ""; 
            if(isItem($nextItem)){$next_img=$nextItem['name']; $next_id=$nextItem['id']; $next_title=$nextItem['title'];}
            if(isItem($prevItem)){$prev_img=$prevItem['name']; $prev_id=$prevItem['id']; $prev_title=$prevItem['title'];}
            $data = array(
                        'next_img'=>$next_img,
                        'id'=>$item['id'],
                        'title'=>$item['title'],
                        'next_id'=>$next_id,
                        'img'=>$item['name'],
                        'prev_id'=>$prev_id,
                        'prev_img'=>$prev_img              
            );
            $json = json_encode($data);
            echo $json;
        }
}else{
    header('location: ./'); 
}
?>