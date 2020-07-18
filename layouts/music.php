
<?php

function fetch_data(&$data, $config, $request){
	$data['categories'] = get_categories( "X_Music",$config['dataroot']);

	$data['title'] = "Galeri Musik";
}

function render(&$htmlcontent,$data){
	$htmlcontent['category-menu'] = print_cat_menu($data);
	$htmlcontent['main'] = '<h2>'.$data['title'] . '</h2>';
	$htmlcontent['main'] .= print_music_item("/contents/songs/oneline.mp3","/contents/img/7393099_m.jpg", "One Line");
	$htmlcontent['main'] .= print_music_item("/contents/songs/rempah-dan-cokelat.mp3","/contents/img/7393099_m.jpg" ,"Rempah dan Cokelat");
	$htmlcontent['main'] .= print_music_item("/contents/songs/luka-bermimpi.mp3","/contents/img/7393099_m.jpg" ,"Bermimpi (Originally by Base Jam)");
	$htmlcontent['main'] .= print_music_item("/contents/songs/pulang.mp3","/contents/img/7393099_m.jpg" ,"Pulang");
}

?>

