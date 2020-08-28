<?php

//--------------------------------------------
//Content data getter and and helper manipulators
//---------------------------------------------

/************************************************
 *
 * This library should become an API for data storage of the blog
 *
 * Data are organized as files and directories
 * Single items represents an addressable page 
 * Single items has metadata and body text 
 * Single items can have other single items as children
 * Parent items are implemented as below pair 
 * 	directory/ 
 * 	directory/--info (to contain its data)
 *
 * What requests can be done to this database?
 * - List children of items (contents of a dir)
 * - Retrieve data in an item
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

