<?php
/* This file controls html rendition according to data and layouts
 *
 * 	Load Libraries
 * 		|
 * 	Load Configuration
 * 		|
 * 	Process http request (ROUTING)
 * 		|
 * 	Fetch data 
 * 		|
 * 	Determine layout
 * 		|
 * 	Render 
 *
 */

/* **********************
 * Load these libraries
 ************************
 */

include(dirname(__FILE__) . '/data_handling.php');
include(dirname(__FILE__) . '/data_interface.php');
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
 *
 * Also set up active css according to defaults
 * or user settings in cookies
 **************************************************/

if(file_exists('config.php'))
	$config = include('config.php');
else
	$config = include('defaults.php');

//Set CSS path according to servername and cookie settings
if(isset($_COOKIE['theme'])){
	$theme = $_COOKIE['theme'];
}
if(isset($_GET['theme'])){
	setcookie('theme', $_POST['theme'],time()+365*24*60*60,'/');
	$theme = $_GET['theme'];
}
//using '//' to force the link as absolute reference to file
$config['csspath'] =  '//' . $_SERVER['SERVER_NAME'] . $config['csspath']; 
//
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
 * output: $target_path
 ******************************************************************* */


//check special cases of requests and redirect as necessary
//here, we convert all exceptions into a problem of which data to fetch 
//There is also the need to set reasonable defaults here
//worst case, just show message and die

//define request
$request = $_GET['request_path'];
if($request == ""){
	//default to home
	//when not exist, should redirect again to a default built in response
	$request = $config['homedir'];
}

//search for requested file
//TODO: Expand searchability to enable providing filename only 
//instead of full file path of target
$dataroot = $config['dataroot'];
$target_path = glob("$dataroot$request");

//parse search result to string $target_path
if($target_path == []) {
	$target_path = ""; 
}else {
	$target_path = $target_path[0];
}

//for directories read data from --info file inside it
if(is_dir($target_path)){
	//append path to check the directory's metadata
	$target_path .= '/--info';
}

//glob cannot find the file, redirect to error 404
if($target_path == ""){
	$target_path = "$dataroot/404";
	if(!file_exists($target_path))
	//even the 404 page doesn't exist, just die already.
		die("<h1> Pencarianmu sungguh sia-sia (404)</h1>");
}

/****************************************
 * Fetch data pointed by $target_path
 *
 * input: $target_path
 * output: $data
 *
 * Data shall contain all meta data and content body text
 * of the file pointed by the $target_path
 ****************************************
 */

$data = get_data($target_path);
if($data==[]){
	//file cannot be opened, show server error 500
	$target_path = "$dataroot/500";
	$data = get_data($target_path);
	if($data==[])
		die("<h1> Kegagalan dalam menjawab tantangan bermula dari kegagalan membuka hati (500)");
}

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
 * render($data,$target_path,$config);
 * this uses components.php collections of functions to print html elements
 *
 * Components in components.php in turn obtain additional data, 
 * or infer it from data passed into it.
 * And then each components returns html code accordingly
 * The layout file then accepts the components' html and place them into the template php file
 *
 * This makes layout php files concerns only about overall layout of the page 
 * while each components deal with their own contents
 * Components is allowed in turn call other components for convenience.
 *
 * ******************************
 */

//var_dump($data['layout']);

if(file_exists('layouts/'. $data['layout']. '.php'))
	include('layouts/'. $data['layout'] . '.php');
else{
	//file cannot be opened, show server error 500
	$target_path = "$dataroot/500";
	$data = get_data($target_path);
	if($data==[])
		die("<h1> Kegagalan dalam menjawab tantangan </h1> <br> bermula dari kegagalan menata pikiran (500)");
}

/*****************************
 * Execute Render
 * ****************************/

//var_dump($target_name);

//populate data and put to frame
render($data,$target_path,$config);

?>
