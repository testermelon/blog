<?php
/* This file renders html according to data and layouts
 *
 * 	Load Libraries
 * 		|
 * 	Configuration
 * 		|
 * 	Process http request (ROUTING)
 * 	  Determine layout
 * 		|
 * 	Obtain data 
 * 		|
 * 	prints html 
 *
 */

/* **********************
 * Load these libraries
 ************************
 */

include(dirname(__FILE__) . '/data_handling.php');
include(dirname(__FILE__) . '/components.php');

/***************************************************
 * Load config files
 *
 * Use default if config file doesn't exist
 *
 * This scheme is easier to maintain using git
 * because we can keep default.php in repo and let 
 * flexible enough for the user copy it and edit 
 * on their own.
 **************************************************/

if(file_exists('config.php'))
	$config = include('config.php');
else
	$config = include('defaults.php');

/****************************************************
 * Set CSS path according to servername and cookie settings
 *******************************************************/

if(isset($_COOKIE['theme'])){
	$theme = $_COOKIE['theme'];
}

if(isset($_POST['theme'])){
	setcookie('theme', $_POST['theme'],time()+365*24*60*60,'/');
	$theme = $_POST['theme'];
}

//using '//' to force the link as absolute reference to file
$config['csspath'] =  '//' . $_SERVER['SERVER_NAME'] . $config['csspath']; 

switch($theme) {
	case 'polos': 
		$config['csspath'] .= 'plain.css';
		break;

	case 'terang': 
		$config['csspath'] .= 'light.css';
		break;

	case 'gelap':
	default:
		$config['csspath'] .= 'dark.css';
		break;
}


/* *****************************************************************
 * ROUTER
 * Perform checks as necessary (TODO).
 * After this section there should be no reference to http request.
 *
 * Request scheme 
 *
 * HOSTNAME/($request_path)
 * (use mod_rewrite in .htaccess to perform above substitution)
 *
 * Parse $request path to locate requested content 
 * If the content was a directory check the file named "--info".
 * It should contain metadata:
 * - Display name of the directory "displayname"
 * - sorting order priority in menus "order"
 * - layout to display index "layout"
 * - etc
 * If the content was a file, check the content of the file
 * It should contain metadata:
 * - date created (obligatory)
 * - title (obligatory)
 * - etc
 *
 * Directories serve double function as menu items
 * in the dataroot, store misc files such as error pages
 *
 * 	REASON for this setup is to make flexible menu and content types 
 * 	just by maintaining content in a flat file system
 * 	Using directories as menu traversal nodes also simplifies site map
 * 	resembling vanilla html pages but seperating data and building process
 *
 * input: $_GET, $_POST, $config
 * output: $target_path,$target_name,$req_nodes, $data
 ******************************************************************* */

$dataroot = $config['dataroot'];

//check special cases of requests and redirect as necessary
//here, we convert all exceptions into a problem of which data to fetch 

$request = $_GET['request_path'];
if($request == ""){
	$request = "beranda";
}

//take first array element to string
$target_path = glob("$dataroot$request");
if($target_path == []) {
	$target_path = ""; 
}else {
	$target_path = $target_path[0];
}

if($target_path == ""){
	//glob cannot find the file, show not found error
	$target_path = "$dataroot/404";
	if(!file_exists($target_path)){
		die("<h1> Pencarianmu sungguh sia-sia (404)</h1>");
	}
}else {
	if(is_dir($target_path)){
		//append path to check the directory's metadata
		$target_path .= '/--info';
	}
	//leave $target_path as it is
}
//obtain data from the content file 

$data = [];
get_data($data, $target_path);
if($data==[]){
	//file cannot be opened, show server error
	get_data($data, "$dataroot/500");
	die("<h1> Kegagalan dalam menjawab tantangan bermula dari kegagalan menata pikiran (500)(1)");
}
var_dump($data);

/* ******************************
 * Load appropriate layout file
 * Layouts are handled as modules of the renderer,
 * included conditioanally based on request of the content file
 *
 * example of layouts:
 * 	music gallery
 * 	song
 * 	category list
 * 	article
 * 	preview (should be private) 
 *
 * layout file should contain 1 function:
 *
 * render($htmlcontent, $data);
 * this uses components.php collections of functions to print html elements
 *
 * ******************************
 */
var_dump($data['layout']);
if(file_exists('layouts/'. $data['layout']. '.php'))
	include('layouts/'. $data['layout'] . '.php');
else{
	die("<h1> Kegagalan dalam menjawab tantangan bermula dari kegagalan menata pikiran (500)(2)");
}

/*****************************
 * Execute Render
 * ****************************/

//data instances for current request
$htmlcontent = [];

//populate data and put to frame
//fetch_data($data,$config,$request);
render($htmlcontent,$data);

//common to all layouts
$htmlcontent['active-css'] = $config['csspath'];
$htmlcontent['theme-buttons'] = print_theme_buttons();

/********************************
 * fetch html template and print
 * ******************************/

include("templates/basic.php");

?>
