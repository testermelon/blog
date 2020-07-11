<?php

function fetch_data(&$data, $config, $request){
	$request['category'] = "X_Tentang Saya";
	get_article_content($data, $request['article'],$config['dataroot'],$config['imgpath']);
	$data['categories'] = get_categories($request['category'],$config['dataroot']);
}

function render(&$htmlcontent,$data){
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['main'] = '<h2>'.$data['title'] . '</h2>';
	$htmlcontent['main'] .= print_article_body($data);
	$htmlcontent['thumbnail'] = $data['thumbnail'];
}

?>

