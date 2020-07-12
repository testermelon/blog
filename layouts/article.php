<?php

function fetch_data(&$data, $config, $request){
	get_article_content($data, $request['urlname'],$config['dataroot'],$config['imgpath']);
	$data['categories'] = get_categories($data['cat'], $config['dataroot']);

}

function render(&$htmlcontent,$data){
	if($data['status'] == '404') {
		$htmlcontent['main'] =  print_404_article();
		$htmlcontent['title'] =  "testermelon - 404";
	}else{
		$htmlcontent['main'] = print_article_header($data);
		$htmlcontent['main'] .= print_article_body($data);
		$htmlcontent['title'] = $data['title'];
		$htmlcontent['thumbnail'] = $data['thumbnail'];
	}
	$htmlcontent['main'] .= print_article_nav_away($data);

	$htmlcontent['category-menu'] = print_cat_menu($data);

}

?>

