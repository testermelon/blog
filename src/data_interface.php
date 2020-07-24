<?php

//--------------------------------------------
//Content data getter and and helper manipulators
//---------------------------------------------

/************************************************
 *
 * This library should become an API for data storage of the blog
 *
 * Posts are organized in single text files containing metadata
 * Posts can be grouped into directories
 * Directories should have a file inside it to contain its metadata
 *
 * What requests can be done to this database?
 * - List items inside a directory
 * - List directories inside a directory
 * - Retrieve data in a post file
 * - Retrieve data about a directory
 * - Search for file name
 *
 * This is a read only database 
 * For writing, use a text editor to directly edit the files
 *
 * input: $dataroot, $path
 * output: $files[] or $meta[] or $body[]
 *
 * ********************************************** */

function get_list_items($dataroot,$path,$sort){
	$ls = glob("$dataroot$path*");
	$list = [];
	foreach($ls as $items) {
		if(!is_dir($items) ){
			array_push($list,$items);
		}
	}
	return $list;
}

function get_list_dir($dataroot,$path,$sort){
	$ls = glob("$dataroot$path*");
	$list = [];
	foreach($ls as $items) {
		if(is_dir($items)) {
			array_push($list,$items);
		}
	}
	return $list;
}

function get_file_metadata($filepath, $reqmeta){

	$data = [];
	$hfile = fopen($filepath,'r');
	if(!$hfile)
		return $data;

	//reading metadata
	do{
		$temp_read = trim(fgets($hfile));
		if($temp_read == '----')
			break;
		$metadata = explode('=',$temp_read);
		$data[$metadata[0]] = $metadata[1];
		if(feof($hfile)) {
			fclose($hfile);
			break;
		}
	}while($temp_read != '----' || !feof($hfile));

	fclose($hfile);

	//extract requested metadata
	if($reqmeta == [])
		return $data;
	$meta = [];
	foreach($reqmeta as $metaname){
		$meta[$metaname] = $data[$metaname];
	}
	return $meta;
}

function get_data($filepath){

	$data = [];
	$hfile = fopen($filepath,'r');
	if(!$hfile)
		return $data;

	//reading metadata
	do{
		$temp_read = trim(fgets($hfile));
		if($temp_read == '----')
			break;
		$metadata = explode('=',$temp_read);
		$data[$metadata[0]] = $metadata[1];
		if(feof($hfile)) {
			fclose($hfile);
			return $data;
		}
	}while($temp_read != '----');

	//read body
	$data['body'] ="";
	while(!feof($hfile)){
		$data['body'] .= fgets($hfile);
	}

	fclose($hfile);
	return $data;
}

function get_data_item($dataroot,$name){
	$path = glob("$dataroot/*/$name");

	if ($path==[]){
		return [];
	}

	$hfile = fopen($path[0],'r');
	if(!$hfile){
		return [];
	}

	$data =[];
	//reading metadata

	$data['date'] = fgets($hfile);
	$data['title'] = fgets($hfile);

	do{
		$temp_read = fgets($hfile);
		if(feof($hfile)) 
			return $data;
		if(trim($temp_read) == '----')
			break;
		$metadata = explode(',',$temp_read);
		$data[$metadata[0]] = $metadata[1];
	}while($temp_read != '----');

	$data['body'] ="";
	while(!feof($hfile)){
		$data['body'] .= fgets($hfile);
	}
	fclose($hfile);

	return $data;
}

function get_data_dir($dataroot,$path){

}

?>

