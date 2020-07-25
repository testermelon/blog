<?php

function render($data,$target_path,$config){
	//html head data
	$htmlcontent['active-css'] = $config['csspath'];
	$htmlcontent['title'] = $data['title'];
	$htmlcontent['thumbnail'] = $data['thumbnail'];

	//html body data
	$htmlcontent['header'] = print_menu($config['dataroot'],$target_path);
	$htmlcontent['main'] .= '<h1>'.$data['title'].'</h1>';
	$htmlcontent['main'] .= '<p>' . print_urlname_list($config['dataroot'],$target_path) . '</p>';
	$htmlcontent['footer'] = print_footer($config);

	//call template to print html response out
	include("templates/basic.php");
}

?>

