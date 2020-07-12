<?php

//--------------------------------------------
//Components, functions to return html content
//All starts with print_
//
//TODO All functions' input must be $content or none
//
//TODO clear all logic/error handling out
//--------------------------------------------


function print_article_body(&$content){
	$html ="";
	if($content === false){
		$html .= "<br> Kosong <br>";
		return $html;
	}

	$html .= $content['body'];
	$html .= "<br>";
	return $html;
}


function print_share_buttons(&$content) {
	$html ="";
	$fb_link = "https://www.facebook.com/sharer/sharer.php?u=";
	$tw_link = "https://twitter.com/intent/tweet?url=";
	$sharelink = "https://testermelon.com/article/" . $content['urlname'];

	$html .= '<div id="social-links">';
	$html .= "Bagikan ke: ";
	$html .= '<a target="_blank" href="' . $fb_link . $sharelink . '"> Facebook </a>';
	$html .= ", ";
	$html .= '<a target="_blank" href="' . $tw_link . $sharelink . '"> Twitter  </a>';
	$html .= '</div>';
	return $html;
}


function print_404_article() {
	$html = "<h2> Ups! Artikel Tidak Ditemukan </h2>";
	$html .= "<p> Bisa jadi ada salah ketik di URL, 
		atau mungkin artikel itu sudah dihapus,
		atau belum dibuat. </p>";
	return $html;
}
	
function print_article_header(&$content){
	$html = "<h2>" . $content['title'] . "</h2>";
	$html .= "<small> ". format_date($content['date']) . " </small>" ;
	$html .= "<br>";
	$html .= print_share_buttons($content);
	$html .= "<br>";

	return $html;
}

/*Show category menu
 * Reads $dataroot and show directories as categories,
 * formatted as unordered list.
 *
 * TODO This function has inappropriate abstraction level
 * Generation of category list should be done in data handling level
 * THis function should only deal with generation of html building blocks
 *
 * Sorted alphabetically (case sensitive)
 * The first two character of directory name 
 * will be discarded when printing, 
 * use this to order the categories in the list
 *
 * Returns the html of the list
 */
function print_cat_menu_li(&$content) {
	$html = "";
	foreach($content['categories']['names'] as $name ){
		$html .= '<li ';
		//determine if category was set and give appropriate class
		if($name == $content['categories']['active']) {
			$html .= 'class="active"';
		}
		$html .= '>';
		$html .= '<a href="/category/' . $name . '">' . substr($name, 2) . '</a>';
		$html .= '</li>';
	}
	return $html;
}

function print_cat_menu(&$content){
	$html = "";
	$html .= '<label class="navi" for="menu-toggle">'; 
	$html .= '<li>';
	$html .= '<a>&#9776; &nbsp;' . substr($content['categories']['active'], 2). '</a> ';
	$html .= '</li>';
	$html .= '</label>';
	$html .= '<input id="menu-toggle" type="checkbox" style="display:none"> </input>';

	$html .= '<ul class="navi" id="categories">';
	$html .= '<li id="site-name"> <a href="/" > <img src="/favicon.ico" style="padding-bottom:4px;vertical-align:middle;height:35px"> </a> </li>';
	$html .= print_cat_menu_li($content);
	$html .= '<li ';
	if($content['urlname'] == 'about') {
		$html.= 'class="active"';
	}
	$html .= '> <a href="/article/about"> Tentang Saya </a> </li>';

	$html .= "</ul>";

	return $html;
}

function print_article_nav_away($content){
	$catlink = '/category/'. $content['cat'];
	$html = "";
	$html .= '<div id="nav-away"> ';
	$html .= "Kembali ke:";
	$html .= "<br>";
	$html .= '<a href="'. $catlink . '">' ;
	$html .= '&#171 Kategori ' . substr($content['cat'], 2) ;
	$html .= '</a>';
	$html .= '<br>'; 
	$html .= '<a href="/"> &#171 Halaman Depan  </a>';
	$html .= '</div>';

	return $html;
}

/*takes array of file path string and spits out html of
 * the article list
 *
 * TODO This function has inappropriate abstraction level
 *
 * Currently fixed to sort by date (newest first)
 */
function print_urlname_list($content){
	$html ="";

	if($content['urlname-list'] == []){
		$html .= "<p> Masih Kosong </p>";
		return $html;
	}

	//print data to html
	foreach($content['urlname-list'] as $date => $details){
		$link = '/article/' . $details[1];
		$linkcat = '/category/' . $details[2];

		$html .= '<a href="' . $link . '">' . $details[0] . '</a>';
		$html .= "<br>";
		$html .= "<small>";
		$html .= format_date($date) . ', dalam ';
		$html .= '<a href="' . $linkcat . '">' . substr($details[2], 2) . '</a>';
		$html .= "</small>";
		$html .= "<br> <br>";
	}
	return $html;
}

//Print recent articles
function print_recent($dataroot){
	$html = "";
	$all_files = glob("$dataroot*/*");

	$html .= "<p>";
	$html .= print_urlname_list($content);
	$html .= "</p>";

	return $html;
}

//Print recent articles in a category
function print_category($cat,$dataroot){
	$html = "";
	$all_files = glob("$dataroot$cat/*");

	$html .= "<h2> " . substr($cat, 2) . " </h2>";
	$html .= "<p>";
	$html .= print_urlname_list($content);
	$html .= "</p>";

	return $html;
}

//print a theme select button

function print_theme_buttons() {

	$html .= '<form '. $req_str . 'method="POST" >';
	$html .= '<input type="submit" name="theme" value="gelap">';
	$html .= '<input type="submit" name="theme" value="terang">';
	//$html .= '<input type="submit" name="theme" value="polos">';
	$html .= "</form>";

	return $html;
}


function print_song_player() {
	$html .= '<audio src = ';

}


?>
