<?php
/* This file renders html according to data and layouts
 *
 * 	Load Libraries
 * 		|
 * 	Process http request 
 * 		|
 * 	Obtain data & prints html 
 * 	according to layout 
 *
 */

/* **********************
 * Load these libraries
 *
 ************************
 */

include(dirname(__FILE__) . '/data_handling.php');
include(dirname(__FILE__) . '/components.php');

//Loading Config file, if not exist load defaults [
//
//and create the config file TODO

if(file_exists('config.php'))
	$config = include('config.php');
else
	$config = include('defaults.php');

//Set CSS path according to servername
//using '//' to force the link as absolute reference to file
$css_path =  '//' . $_SERVER['SERVER_NAME'] . $config['csspath']; 

/* *****************************************************************
 * Determine type of content and layout to use based on http request.
 *
 * Perform checks as necessary (TODO).
 *
 * After this section there should be no reference to http request.
 *******************************************************************
 */

if(isset($_GET['category'])){
	$layout = "category";
	$request_cat = $_GET['category'];
}
else if(isset($_GET['article'])){
	if( $_GET['article'] == 'about' || $_GET['article'] == 'links'){
		$layout = 'fixed';
	}
	else{
		$layout = "article";
	}
	$request_article = $_GET['article'];
}
else if(isset($_GET['preview'])){
	$layout = "preview";
	$request_path = $_GET['preview'];
}
else{
	$layout = "home";
}

/* ******************************************************************
 * Obtain data and prints html according to set layout
 *
 * ******************************************************************
 */

//category menu content
$comp_category_menu = print_cat_menu($request_cat, $config['dataroot']);

$comp_main = "";
switch ($layout){
case 'home': 
	$content['title'] =  "testermelon - Home";
	$comp_main .= print_recent($config['dataroot']);
	break;
case 'category': 
	$content['title'] = "testermelon - ". $request_cat;
	$comp_main .= print_category($request_cat,$config['dataroot']);
	break;
case 'article': 
	$content = get_article_content($request_article,$config['dataroot']);
	$comp_main .= print_article_header($content);
	$comp_main .= print_article_body($content);
	$comp_main .= print_article_nav_away($content);
	break;
case 'fixed': 
	$content = get_article_content($request_article ,$config['dataroot']);
	$comp_main .= print_article_body($content);
	break;
case 'preview': 
	$content = get_article_data($request_path);
	if ($content == []){
		$comp_main .= "File tidak ditemukan atau tidak bisa dibuka.";
		break;
	}
	$comp_main .= print_article_header($content);
	$comp_main .= print_article_body($content);
	break;
}

?>
