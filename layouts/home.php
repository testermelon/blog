<?php

function render($data,$target_path,$config){
	//html head data
	$htmlcontent['active-css'] = $config['csspath'];
	$htmlcontent['title'] = $data['title'];
	$htmlcontent['thumbnail'] = print_head_thumbnail($config['imgpath'],$data['thumbnail']);
	$htmlcontent['head-js-modules'] = print_head_js_modules($data['jsmod']);

	//html body data
	$htmlcontent['header'] = print_menu($config['dataroot'],$target_path,$config);
	$htmlcontent['main'] .= '<h1>'.$data['title'].'</h1>';
	$htmlcontent['main'] .= '<p>Selamat datang di testermelon.com</p>';
	$htmlcontent['main'] .= print_article_body($data,$config['imgpath']);
	//planning to add these
	//same with menu in header, but fancier with image n animations
	//$htmlcontent['main'] .= print_menu_elaborate($config['dataroot'],$target_path);
	//list of latest 5 posts in every category
	//$htmlcontent['main'] .= print_latest_posts(5);

	//call template to print html response out
	include("templates/basic.php");
}

?>

