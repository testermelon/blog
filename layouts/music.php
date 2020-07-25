
<?php

function render($data,$target_path,$config){
	//html head data
	$htmlcontent['active-css'] = $config['csspath'];
	$htmlcontent['title'] = $data['title'];
	$htmlcontent['thumbnail'] = $data['thumbnail'];

	//html body data
	$htmlcontent['header'] = print_menu($config['dataroot'],$target_path);
	$htmlcontent['main'] .= '<h1>'.$data['title'].'</h1>';
	$htmlcontent['main'] .= '<p>'.render_to_html($data['body'],$config['imgpath']). '</p>';
	$htmlcontent['main'] .= print_music_item("/contents/songs/oneline.mp3","/contents/img/7393099_m.jpg", "One Line");
	$htmlcontent['main'] .= print_music_item("/contents/songs/rempah-dan-cokelat.mp3","/contents/img/7393099_m.jpg" ,"Rempah dan Cokelat");
	$htmlcontent['main'] .= print_music_item("/contents/songs/luka-bermimpi.mp3","/contents/img/7393099_m.jpg" ,"Bermimpi (Originally by Base Jam)");
	$htmlcontent['main'] .= print_music_item("/contents/songs/pulang.mp3","/contents/img/7393099_m.jpg" ,"Pulang");
	$htmlcontent['footer'] = print_footer($config);

	//call template to print html response out
	include("templates/basic.php");
}

?>

