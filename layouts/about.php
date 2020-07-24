<?php

function render(&$htmlcontent,$data){
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['main'] = '<h2>'.$data['title'] . '</h2>';
	$htmlcontent['main'] .= print_article_body($data);
	$htmlcontent['thumbnail'] = $data['thumbnail'];
}

?>

