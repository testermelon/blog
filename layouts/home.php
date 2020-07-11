<?php

function fetch_data(&$data, $config, $request){
	$request['category'] = "0_Beranda";
	$globdir = '*/*';
	$data['categories'] = get_categories($request['category'], $config['dataroot']);
	$data['urlname-list'] = get_urlname_list($config['dataroot'],$globdir);
}

function render(&$htmlcontent,$data){
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['title'] =  "testermelon - Home";
	$htmlcontent['main'] .= '<h2>Beranda</h2>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($data) . '</p>';

}

?>

