<?php

function render($data,$target_path,$config){
	//html head data
	$htmlcontent['active-css'] = $config['csspath'];
	$htmlcontent['title'] = $data['title'];
	$htmlcontent['thumbnail'] = $data['thumbnail'];

	//html body data
	$htmlcontent['header'] = print_menu($config['dataroot'],$target_path);
	$htmlcontent['main'] .= '<h1>'. $data['title'] . '</h1>';
	$htmlcontent['main'] .= print_article_body($data,$config['imgpath']);
	$htmlcontent['main'] .= print_article_nav_away($config['dataroot'],$target_path);
	$htmlcontent['footer'] = print_footer($config);

	//call template to print html response out
	include("templates/basic.php");

}

?>

