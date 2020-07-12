<?php

function fetch_data(&$data, $config, $request){
	get_article_content($data, $request['urlname'],$config['dataroot'],$config['imgpath']);
	$data['categories'] = get_categories( "X_Tentang Saya",$config['dataroot']);
}

function render(&$htmlcontent,$data){
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['main'] = '<h2>'.$data['title'] . '</h2>';
	$htmlcontent['main'] .= print_article_body($data);
	$htmlcontent['thumbnail'] = $data['thumbnail'];
}

?>

