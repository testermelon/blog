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
 * Loading Config file, if not exist load defaults
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
 * Determine type of content and layout to use based on http request.
 * Perform checks as necessary (TODO).
 * After this section there should be no reference to http request.
 *
 * input: http request, config
 * output: $request, $config
 ******************************************************************* */

if(isset($_GET['category'])){
	$request['layout'] = "category";
	$request['category'] = $_GET['category'];
	if($request['category'] == "")
		//serve home
		$request['layout'] = "home";
}
else if(isset($_GET['article'])){
	if( $_GET['article'] == 'about' || $_GET['article'] == 'links'){
		$request['layout'] = 'fixed';
	}
	else{
		$request['layout'] = "article";
	}
	$request['article'] = $_GET['article'];
	if($request['article'] == "")
		//redirect to home
		$request['layout'] = "home";
}
else if(isset($_GET['preview'])){
	$request['layout'] = "preview";
	$request['path'] = $_GET['preview'];
}
else{
	$request['layout'] = "home";
}

/* ******************************
 * read template file
 * template file should contain 2 functions 
 *
 * fetch_data(&$data,$config,$request);
 * render($htmlcontent, $data);
 *
 * ******************************
 */

if(file_exists('layouts/'. $request['layout']. '.php'))
	include('layouts/'. $request['layout'] . '.php');
else
	die("Internal server error");

/*****************************
 * Execute Render
 * ****************************/

$data = [];
$htmlcontent = [];
fetch_data($data,$config,$request);
render($htmlcontent,$data);

//common to all layouts
$htmlcontent['active-css'] = $config['csspath'];
$htmlcontent['theme-buttons'] = print_theme_buttons();

?>
