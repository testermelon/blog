<?php

function fetch_data(&$data, $config, $request){
	$globdir = $request['urlname'] . '*/*';
	$data['categories'] = get_categories($request['urlname'], $config['dataroot']);
	$data['urlname-list'] = get_urlname_list($config['dataroot'],$globdir);
}

function render(&$htmlcontent,$data){
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['title'] = "testermelon - ". substr($data['categories']['active'],2);
	$htmlcontent['main'] .= '<h2>' . substr($data['categories']['active'],2). '</h2>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($data) . '</p>';
}

?>

