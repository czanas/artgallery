<?php
    /**
    *@Project: Art Gallery 
    *@file funcutils.php
    *@description: Main art gallery page. 
    *@author: MZ (czanas) on Github
    *@email: zackvixacd@gmail.com 
    *@date: 2019-10-29
    **/
    require('./funcutils.php'); 
    $test_db = new artDB();
?>
    
<!DOCTYPE html>
<!--
Please to give me credit, leave this part intact
@Projecct: Art Gallery version 1.0
@Author: MZ (czanas) on Github
@email: zackviacd@gmail.com 
@date: 2019-10-23

-->
<html lang='en'>
	<head><title><?=ART_TITLE?></title>
        <script src='./jquery-3.4.1.min.js'></script>
        <script src='./sweetalert2v8.js'></script>
        <style>
            /*css for device with width > 720 px*/
            @media(min-width:720px){
                body{background-color: hsl(0, 0%, 60%);
                }
                #titlePart{
                    border:solid 1px black;
                    width:600px;
                    text-align:center;
                    border-radius:5px;
                    font-weight:bold; 
                    font-size:20px;
                }
                #previousPart{
                    border:solid 1px black;
                   
                    width:50px;
                    text-align:left;
                    margin-right:5px;
                    border-top-right-radius:8px;
                    border-bottom-right-radius:8px;
                    cursor: pointer; 
                    background-color:white;
                    box-shadow: 1px 1px 1px 1px black;
                }
                #fastPreviousPart{
                    border:solid 1px black;
                    
                    width:20px;
                    text-align:left;
                    margin-right:5px;
                    border-top-right-radius:8px;
                    border-bottom-right-radius:8px;
                    cursor: pointer; 
                    background-color:white;
                    box-shadow: 1px 1px 1px 1px black;
                }
                #fastPreviousPartMobile{
                    display:none;
                }
                #fastNextPartMobile{
                    display:none;
                }
                #previousPartMobile{
                    display:none; 
                }
                #nextPartMobile{
                    display:none;
                }
                #imagePart{
                    
                    width:600px;
                    text-align:center;
                    margin-top:5px; 
                    margin-bottom:5px;
                }
                
                #imagePart > img{
                    margin-top:5px;
                    width:590px;   
                    margin-bottom:5px;
                    border:solid 4px black;
                }
                #nextPart{
                    border:solid 1px black;
                    
                    width:50px;
                    text-align:right;  
                    margin-left:5px;
                    border-top-left-radius:8px;
                    border-bottom-left-radius:8px;
                    cursor: pointer; 
                    background-color:white;
                    box-shadow: 1px 1px 1px 1px black;
                }
                
                #fastNextPart{
                    border:solid 1px black;
                    
                    width:20px;
                    text-align:right;  
                    margin-left:5px;
                    border-top-left-radius:8px;
                    border-bottom-left-radius:8px;
                    cursor: pointer; 
                    background-color:white;
                    box-shadow: 1px 1px 1px 1px black;
                }
                
                #captionPart{
                    border:solid 1px black;
                    width:600px;
                    margin-top:5px;
                    margin-bottom:5px;
                    text-align:left;
                    padding-left:4px;
                }
                #captionText{
                    margin:5px;
                }
                #biggerContainer{
                    margin:auto;
                    width:610px;
                    border: 1px solid black;
                }
                #mainContainer{
                    display:flex;
                    flex-direction:column;
                    align-items:center;
                }
                #mainImageContainer{
                    display:flex;
                    flex-direction:row;
                    align-items:center;
                }
                #galleryContainer{
                    border:solid 1px black;
                    
                    width:600px;
                    display:flex;
                    justify-content:space-evenly;
                    flex-wrap:wrap;
                }
                #randTitle{
                    width:600px;
                    text-align:center;
                }
                .galleryItem{
                    width:100px;
                    margin:2px;
                    cursor:pointer;
                }
                .galleryItem > img{
                    border:solid 1px black;
                }
                #credit{
                    font-size:10px;
                    margin-top:20px;
                    text-align:center;
                    font-style:italic;
                }
            }
            /*css for device with width > 720 px*/
            @media screen and (max-width:719px){
                body{/*background-color:whitesmoke;*/background-color: hsl(0, 0%, 60%);
                }
                #titlePart{
                    border:solid 1px black;
                    width:90%;
                    text-align:center;
                    border-radius:5px;
                    font-weight:bold; 
                    font-size:20px;
                }
                #previousPart{
                    display:none;  
                }
                #previousPartMobile{
                    border:solid 1px black;
                    
                    margin-top:5px;
                    width:60px;
                    text-align:left;
                    margin-right:5px;
                    border-top-right-radius:8px;
                    border-bottom-right-radius:8px;
                    cursor: pointer;
                    box-shadow: 1px 1px 1px 1px black;
                }
                #nextPartMobile{
                    border:solid 1px black;
                    margin-top:5px;
                    
                    width:60px;
                    text-align:right;
                    margin-left:5px;
                    border-top-left-radius:8px;
                    border-bottom-left-radius:8px;
                    cursor: pointer; 
                    box-shadow: 1px 1px 1px 1px black;
                }
                #fastPreviousPartMobile{
                    border:solid 1px black;
                    
                    width:20px;
                    text-align:left;
                    margin-right:5px;
                    border-top-right-radius:8px;
                    border-bottom-right-radius:8px;
                    cursor: pointer; 
                    background-color:white;
                    box-shadow: 1px 1px 1px 1px black;
                }
                #fastNextPartMobile{
                    border:solid 1px black;
                    
                    width:20px;
                    text-align:right;  
                    margin-left:5px;
                    border-top-left-radius:8px;
                    border-bottom-left-radius:8px;
                    cursor: pointer; 
                    background-color:white;
                    box-shadow: 1px 1px 1px 1px black;
                }
                                
                #fastPreviousPart{
                    display:none;
                }
                #fastNextPart{
                    display:none;
                }                
                #imagePart{
                    
                    width:80%;
                    text-align:center;
                    margin-top:5px; 
                    margin-bottom:5px;
                }
                
                #imagePart > img{
                    margin-top:5px;
                    width:90%;   
                    margin-bottom:5px;
                    border:solid 4px black;
                }
                #nextPart{
                    display:none;
                }
                
                #captionPart{
                    border:solid 1px black;
                    width:90%;
                    margin-top:5px;
                    margin-bottom:5px;
                    text-align:left;
                    padding:5px;
                    text-align:left;
                }
                #captionText{
                    margin:5px;
                }
                #mainContainer{
                    display:flex;
                    width:100%;
                    flex-direction:column;
                    align-items:center;
                }
                #mainImageContainer{
                    display:flex;
                    width:100%;
                    justify-content:space-evenly;
                    flex-wrap:wrap;
                }
                #galleryContainer{
                    border:solid 1px black;
                    
                    width:90%;
                    display:flex;
                    justify-content:space-evenly;
                    flex-wrap:wrap;
                }
                #randTitle{
                    width:90%;
                    text-align:center;
                }
                .galleryItem{
                    width:10vw;
                    margin:2px;
                    cursor:pointer;
                }
                .galleryItem > img{
                    border:solid 1px black;
                }
                #credit{
                    font-size:10px;
                    margin-top:20px;
                    text-align:center;
                    font-style:italic;
                }
                .modal-content {
                    width: 100%;
                }
            }            

            /*Part used for the zooming*/
            #myImg {
              border-radius: 5px;
              cursor: pointer;
              transition: 0.3s;
            }

            #myImg:hover {opacity: 0.7;}

            /* The Modal (background) */
            .modal {
              display: none; /* Hidden by default */
              position: fixed; /* Stay in place */
              z-index: 1; /* Sit on top */
              padding-top: 1px; /* Location of the box */
              left: 0;
              top: 0;
              width: 100%; /* Full width */
              height: 100%; /* Full height */
              overflow: auto; /* Enable scroll if needed */
              background-color: rgb(0,0,0); /* Fallback color */
              background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
            }

            /* Modal Content (image) */
            .modal-content {
              margin: auto;
              display: block;
              max-width: 90vw;
              max-height:90vh;
              /*max-width: 700px;*/
            }

            /* Caption of Modal Image */
            #caption {
              margin: auto;
              display: block;
              width: 80%;
              max-width: 700px;
              text-align: center;
              color: #ccc;
            }
            
            #caption2{
              margin: auto;
              display: block;
              width: 80%;
              max-width: 700px;
              text-align: center;
              color: #ccc;
            }
            /* Add Animation */
            .modal-content, #caption {  
              -webkit-animation-name: zoom;
              -webkit-animation-duration: 0.6s;
              animation-name: zoom;
              animation-duration: 0.6s;
            }

            @-webkit-keyframes zoom {
              from {-webkit-transform:scale(0)} 
              to {-webkit-transform:scale(1)}
            }

            @keyframes zoom {
              from {transform:scale(0)} 
              to {transform:scale(1)}
            }

            /* The Close Button */
            .close {
              position: absolute;
              top: 15px;
              right: 35px;
              color: #f1f1f1;
              font-size: 12px;
              font-weight: bold;
              transition: 0.3s;
            }

            .close:hover,
            .close:focus {
              color: #bbb;
              text-decoration: none;
              cursor: pointer;
            }
                                          
        </style>
        
        <script>
            /**
                @Note: the variables 
                next_id and prev_id have to be set by php on the server side
            **/
            
            /**
            * alertMsg
            * a function that wraps SweetAlert2
            * why? Because I might use a more complicated version of the Swal.fire call
            **/
            function alertMsg(text)
            {
                /*making sure that sweetalert has been installed*/
                if( typeof Swal.fire === "function"){
                    Swal.fire(text);
                }else{/*fall back on the default alert function*/
                    alert(text);
                }
                    
            }
            /**
            * @moveNext function 
            * @description: javascript function that redirects to the gallery element 
            *               whose ID is follows to the currently displayed one
            **/
            function moveNext(){
                
                if(typeof(next_id) == "undefined" ){
                    moveTo(0);
                }else if(next_id == -1){
                    alertMsg('You have reached the end of the Gallery'); 
                }else{
                    moveTo(next_id); 
                }
                
            }

            /**
            * @moveNext function 
            * @description: javascript function that redirects to the gallery element 
            *               whose ID preceed to the currently displayed one
            **/            
            function movePrevious(){
                
                if(typeof(prev_id) == "undefined"){
                    moveTo(0);
                }else if(prev_id == -1){
                    alertMsg('You have reached the beginning of the Gallery'); 
                }else{
                    moveTo(prev_id); 
                }
            }
            
            /**
            * @moveTo: 
            * @description: Generic function that change the page to display an art Item with 
            *               a particular id.  
            **/
            function moveTo(id){
                // Simulate a mouse click:
                window.location.href = "./?id="+id; 
            }
            
            function detectswipe(el,func) {
                  swipe_det = new Object();
                  swipe_det.sX = 0;
                  swipe_det.sY = 0;
                  swipe_det.eX = 0;
                  swipe_det.eY = 0;
                  var min_x = 20;  //min x swipe for horizontal swipe
                  var max_x = 40;  //max x difference for vertical swipe
                  var min_y = 40;  //min y swipe for vertical swipe
                  var max_y = 50;  //max y difference for horizontal swipe
                  var direc = "";
                  ele = document.getElementById(el);
                  ele.addEventListener('touchstart',function(e){
                    var t = e.touches[0];
                    swipe_det.sX = t.screenX; 
                    swipe_det.sY = t.screenY;
                  },false);
                  ele.addEventListener('touchmove',function(e){
                    e.preventDefault();
                    var t = e.touches[0];
                    swipe_det.eX = t.screenX; 
                    swipe_det.eY = t.screenY;    
                  },false);
                  ele.addEventListener('touchend',function(e){
                    //horizontal detection
                    if ((((swipe_det.eX - min_x > swipe_det.sX) || (swipe_det.eX + min_x < swipe_det.sX)) && ((swipe_det.eY < swipe_det.sY + max_y) && (swipe_det.sY > swipe_det.eY - max_y)))) {
                      if(swipe_det.eX > swipe_det.sX) direc = "r";
                      else direc = "l";
                    }
                    //vertical detection
                    if ((((swipe_det.eY - min_y > swipe_det.sY) || (swipe_det.eY + min_y < swipe_det.sY)) && ((swipe_det.eX < swipe_det.sX + max_x) && (swipe_det.sX > swipe_det.eX - max_x)))) {
                      if(swipe_det.eY > swipe_det.sY) direc = "d";
                      else direc = "u";
                    }
                
                    if (direc != "") {
                      if(typeof func == 'function') func(el,direc);
                    }
                    direc = "";
                  },false);  
                }

                function myfunction(el,d) {
                  alert("you swiped on element with id '"+el+"' to "+d+" direction");
                }



            //detectswipe('swipeme',myfunction);            
        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
        <div style='width:100%;text-align:center'>
            <?php
                if(hasAccess()){
                    renderGallery();
                }else{
                    promptAccess();
                }
            ?>     
        </div>
	</body>

</html>
