<?php
    /**
    *@Project: Art Gallery 
    *@file funcutils.php
    *@description: Admin page used to add, remove, and show art statistics
    *@author: MZ (czanas) on Github
    *@email: zackvixacd@gmail.com 
    *@date: 2019-10-29
    **/
    require("../funcutils.php");
    require("./adminfuncs.php"); 

    /*.htaccess check*/
    checkIfAdminSet();
        

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Admin Area</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src='../jquery-3.4.1.min.js'></script>
    </head>
    <body style='background-color:whitesmoke'>
        <div align='center'><b>Admin Area</b><br><br>
            <a href='./'>Admin Menu</a> | <a href='../'>Main Site</a> <br><br>
            <div align='center' style='width:90%;border:2px solid black'>
                <div style='margin:10px' align='left'>
                    <?php
                        renderAdmin();
                    ?>
                </div>
            </div>
        </div>
        
        <script>
            function allowDrop(ev) {
                ev.preventDefault();
            }

            function drag(ev, myid) {
                ev.dataTransfer.setData("text", myid);
            }

            function drop(ev,myid) {
                ev.preventDefault();
                var data = ev.dataTransfer.getData("text");
                //ev.target.appendChild(document.getElementById(data));
                //alert("Items to swap: "+data+" with "+myid); 
                var hh = $('#allItemShow').height(); 
                //$('#allItemShow').html("<b>Processing...</b>"); 
                $('#allItemShow').height(hh);
                $.post("./auxmanip.php", {'act':'swap', 'ida':data, 'idb':myid},
                                function(ret){
                                    console.log('returned'+ret); 
                                    populateItems(); 
                                });
            }
      
        
        </script>
    </body>
</html>
