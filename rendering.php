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

//Set CSS path according to servername and cookie settings
//using '//' to force the link as absolute reference to file
//
// TODO use cookie to get/set chosen css file
// CURRENTLY default set by name in config file
//
$config['csspath'] =  '//' . $_SERVER['SERVER_NAME'] . $config['csspath']; 

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
	$request['category'] = $_GET['category'];
	if($request['category'] == "")
		//serve home
		$layout = "home";
}
else if(isset($_GET['article'])){
	if( $_GET['article'] == 'about' || $_GET['article'] == 'links'){
		$layout = 'fixed';
	}
	else{
		$layout = "article";
	}
	$request['article'] = $_GET['article'];
	if($request['article'] == "")
		//redirect to home
		$layout = "home";
}
else if(isset($_GET['preview'])){
	$layout = "preview";
	$request['path'] = $_GET['preview'];
}
else{
	$layout = "home";
}

/* ******************************************************************
 * Obtain data and prints html according to set layout
 *
 * input: $layout, $config, $request
 * output: $htmlcontent
 * ******************************************************************
 */

//common to all layouts
$htmlcontent['conf-base'] = $config['conf-base'];
$htmlcontent['active-css'] = $config['csspath'];

switch ($layout){
case 'home': 
	$content['categories'] = get_categories($request['category'], $config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($content);
	$htmlcontent['title'] =  "testermelon - Home";
	$content['urlname-list'] = get_urlname_list($config['dataroot'],'*/*');
	$htmlcontent['main'] .= '<h2>Artikel Terbaru</h2>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($content) . '</p>';
	break;

case 'category': 
	$content['categories'] = get_categories($request['category'], $config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($content);
	$htmlcontent['title'] = "testermelon - ". substr($content['categories']['active'],2);
	$cat = $request['category'] . '/*';
	$content['urlname-list'] = get_urlname_list($config['dataroot'],$cat);
	$htmlcontent['main'] .= '<h2>' . substr($content['categories']['active'],2). '</h2>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($content) . '</p>';
	break;

case 'article': 
	$content['categories'] = get_categories($request['category'], $config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($content);
	$content = get_article_content($request['article'],$config['dataroot']);
	if($content === false) {
		$htmlcontent['main'] =  print_404_article();
	}else{
		$htmlcontent['main'] = print_article_header($content);
		$htmlcontent['main'] .= print_article_body($content);
	}
	$htmlcontent['main'] .= print_article_nav_away($content);
	break;

case 'fixed': 
	$content['categories'] = get_categories($request['category'], $config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($content);
	$content = get_article_content($request['article'] ,$config['dataroot']);
	$htmlcontent['main'] = print_article_body($content);
	break;

case 'preview': 
	$content = get_article_data($request['path']);
	$htmlcontent['main'] = "";
	if ($content == []){
		$htmlcontent['main'] =  print_404_article();
		break;
	}
	$htmlcontent['main'] .= print_article_header($content);
	$htmlcontent['main'] .= print_article_body($content);
	break;

}

//TODO 
//
//Need to change abstraction scheme
//
//After deciding layout based on request, 
//this should do data handling, and then pass the read data to layout template
//
//e.g. echo print_layout($layout,$content);
//
//This function should read a dir named "layouts" for "*.layout" files, and populate the html

?>
