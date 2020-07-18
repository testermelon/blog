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
 * Determine type of content and layout to use based on http request.
 * Perform checks as necessary (TODO).
 * After this section there should be no reference to http request.
 *
 * Request scheme 
 *
 * HOSTNAME/(layout)/(urlname)
 * (use mod_rewrite in .htaccess to perform above substitution)
 *
 * layouts:
 * 	music
 * 	song
 * 	category
 * 	article
 * 	preview (should be private) 
 *
 * urlname: 
 * 	unique identifier for the content
 *
 * input: $_GET, $_POST, $config
 * output: $request['layout','urlname']
 ******************************************************************* */

$request = [];

if(isset($_GET['layout'])){
	$request['layout'] = $_GET['layout'];
}else{
	//default to home when unidentified
	//Maybe should leave a message when this happens? 
	$request['layout'] = 'home';
}

if(isset($_GET['urlname'])){
	$request['urlname'] = $_GET['urlname'];
}else{
	//default to home when unidentified
	//Maybe should leave a message when this happens? 
	$request['layout'] = 'home';
	$request['urlname'] = "";
}

//Exception for about page
if ($request['urlname'] == 'about') {
	$request['layout'] = 'about';
}


/* ******************************
 * read appropriate layout file
 * layout file should contain 2 functions 
 *
 * fetch_data(&$data,$config,$request);
 * This uses data_handling.php functions to interface with the database 
 *
 * render($htmlcontent, $data);
 * this uses components.php collections of functions to print html elements
 *
 * ******************************
 */

if(file_exists('layouts/'. $request['layout']. '.php'))
	include('layouts/'. $request['layout'] . '.php');
else{
	die("Unidentified layout");
}

/*****************************
 * Execute Render
 * ****************************/


//data instances for current request
$data = [];
$htmlcontent = [];

//populate data and put to frame
fetch_data($data,$config,$request);
render($htmlcontent,$data);

//common to all layouts
$htmlcontent['active-css'] = $config['csspath'];
$htmlcontent['theme-buttons'] = print_theme_buttons();

/********************************
 * fetch html template and print
 * ******************************/

include("templates/basic.php");

?>
