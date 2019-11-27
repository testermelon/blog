<?php
/*
 * This file renders html according to data and layouts
 *
 */

include(dirname(__FILE__) . '/data_handling.php');
include(dirname(__FILE__) . '/components.php');

//Loading Config file, if not exist load defaults
if(file_exists('config.php'))
	$config = include('config.php');
else
	$config = include('defaults.php');

//Set CSS path
//using '//' to force the link as absolute reference to file
$css_path =  '//' . $_SERVER['SERVER_NAME'] . $config['csspath']; 

//load image for social media if set. If not load default thumbnail image
if (isset($content['thumbnail']))
	$page_thumbnail = $content['thumbnail'];
else 
	$page_thumbnail = 'http://testermelon.com/img/testermelon-banner.png';

//Determine type of content and layout to use based on http request
if(isset($_GET['category'])){
	$page_title = "testermelon - ". $_GET['category'];
	$layout = "category";
}
else
if(isset($_GET['article'])){
	$content = get_article_content($_GET['article'],$config['dataroot']);
	$page_title = $content['title'];
	if( $_GET['article'] == 'about' || $_GET['article'] == 'links')
		$layout = 'fixed';
	else
		$layout = "article";
}
else{
	$page_title =  "testermelon - Home";
	$layout = "home";
}

//rendering components into layouts:

//category menu content
$comp_category_menu = print_categories($config['dataroot']);

$comp_main = "";
switch ($layout){
case 'home': 
	$comp_main .= print_recent($config['dataroot']);
	break;
case 'category': 
	$comp_main .= print_category($_GET['category'],$config['dataroot']);
	break;
case 'article': 
	$comp_main .= print_article_header($content);
	$comp_main .= print_article_body($content);
	break;
case 'fixed': 
	$comp_main .= print_article_body($content);
}
?>

