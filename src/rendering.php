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

//Set CSS path according to servername and cookie settings
//
// TODO use cookie to get/set chosen css file
// CURRENTLY default set by name in config file

if(isset($_COOKIE['theme'])){
	$theme = $_COOKIE['theme'];
}
/* *****************************************************************
 * ROUTER
 * Determine type of content and layout to use based on http request.
 * Perform checks as necessary (TODO).
 * After this section there should be no reference to http request.
 *******************************************************************
 */


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

/*
 * Perencanaan ulang skema data biar lebih fleksibel
 *
 * dataroot
 * 	Musik
 * 	Blog
 * 		Teknologi
 * 			article
 * 			article
 * 		Subkultur
 * 			article
 * 			article
 * 	Serbaneka
 * 		article
 * 		article
 * 	about
 * 	dir.info
 *
 *
 * */
//common to all layouts
$htmlcontent['conf-base'] = $config['conf-base'];
$htmlcontent['active-css'] = $config['csspath'];
$htmlcontent['theme-buttons'] = print_theme_buttons();

switch ($layout){
case 'home': 
	$data['categories'] = get_categories("0_Beranda", $config['dataroot']);
	$data['urlname-list'] = get_urlname_list($config['dataroot'],'*/*');

	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['title'] =  "testermelon - Home";
	$htmlcontent['main'] .= '<h2>Artikel Terbaru</h2>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($data) . '</p>';
	break;

case 'category': 
	$data['categories'] = get_categories($request['category'], $config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['title'] = "testermelon - ". substr($data['categories']['active'],2);

	$data['urlname-list'] = get_urlname_list($config['dataroot'],$request['category'] . '/*');
	$htmlcontent['main'] .= '<h2>' . substr($data['categories']['active'],2). '</h2>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($data) . '</p>';
	break;

case 'article': 
	$data = get_article_content($request['article'],$config['dataroot'],$config['imgpath']);
	if($data === false) {
		$htmlcontent['main'] =  print_404_article();
		$htmlcontent['title'] =  "testermelon - 404";
	}else{
		$htmlcontent['main'] = print_article_header($data);
		$htmlcontent['main'] .= print_article_body($data);
		$htmlcontent['title'] = $data['title'];
		$htmlcontent['thumbnail'] = $data['thumbnail'];
	}
	$htmlcontent['main'] .= print_article_nav_away($data);
	$data['categories'] = get_categories($data['cat'], $config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($data);
	break;

case 'fixed': 
	$data = get_article_content($request['article'] ,$config['dataroot'], $config['imgpath']);
	$data['categories'] = get_categories("X_Tentang Saya",$config['dataroot']);
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['main'] = '<h2>'.$data['title'] . '</h2>';
	$htmlcontent['main'] .= print_article_body($data);
	$htmlcontent['thumbnail'] = $data['thumbnail'];
	break;

case 'preview': 
	$data = get_article_data($request['path']);
	$htmlcontent['main'] = "";
	if ($data == []){
		$htmlcontent['main'] =  print_404_article();
		break;
	}
	$htmlcontent['main'] .= print_article_header($data);
	$htmlcontent['main'] .= print_article_body($data);
	break;

}

//TODO 
//
//Need to change abstraction scheme
//
//After deciding layout based on request, 
//this should do data handling, and then pass the read data to layout template
//
//e.g. echo print_layout($layout,$data);
//
//This function should read a dir named "layouts" for "*.layout" files, and populate the html

?>
