<?php


	/*utility function to get img information*/
	function getImgWidth($data){
		if(is_array($data) && count($data) != 0){
			$img_dim = getimagesize("./imgs/${data['name']}");
			$data['width'] = $img_dim[0]; 
			$data['height'] = $img_dim[1]; 
		}
		return $data; 
	}
	
    require('./funcutils.php'); 
    $db_api = new artDB();

	$id = 0; 
	if(array_key_exists("id", $_GET) && is_numeric($_GET["id"])){
		
		$id = (int)($_GET["id"]); 
	}

	$thisItem = array("caption"=>"Your  Gallery is Empty. Please add new items!", "name"=>"empty.jpg", "title"=>"Empty Gallery", "date"=>0, "id"=>0, 'width'=>0, 'height'=>0); 
	$prevItem = array("caption"=>"Your  Gallery is Empty. Please add new items!", "name"=>"empty.jpg", "title"=>"Empty Gallery", "date"=>0, "id"=>-1, 'width'=>0, 'height'=>0); 
	$nextItem = array("caption"=>"Your  Gallery is Empty. Please add new items!", "name"=>"empty.jpg", "title"=>"Empty Gallery", "date"=>0, "id"=>-1, 'width'=>0, 'height'=>0); 

	$this_data = $db_api->getItem($id); 

	$this_data = ($this_data && count($this_data))==0?$thisItem:getImgWidth($this_data); 
	
	$prev_data = $db_api->getPrevItem($id); 

	$prev_data = ($prev_data && count($prev_data))==0?$prevItem:getImgWidth($prev_data); 
	
	$next_data = $db_api->getNextItem($id); 
	$next_data = ($next_data && count($next_data))==0?$nextItem:getImgWidth($next_data); 
	
	$retArray = array(); 
	$retArray['item'] = $this_data; 
	$retArray['next'] = $next_data; 
	$retArray['prev'] = $prev_data; 
	
	echo json_encode($retArray); 
?>