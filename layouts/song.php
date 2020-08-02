
<?php

function render($data,$target_path,$config){
	//html head data
	$htmlcontent['active-css'] = $config['csspath'];
	$htmlcontent['title'] = $data['title'];
	$htmlcontent['thumbnail'] = $data['thumbnail'];

	//html body data
	$htmlcontent['header'] = print_menu($config['dataroot'],$target_path);
	$htmlcontent['main'] .= print_large_music_item($config,$data);
	$htmlcontent['main'] .= '<h1>'.$data['title'].'</h1>';
	$htmlcontent['main'] .= '<p>'.render_to_html($data['body'],$config['imgpath']). '</p>';
	$htmlcontent['footer'] = print_footer($config);

	//call template to print html response out
	include("templates/basic.php");
}

?>

