<?php

//--------------------------------------------
//Components, functions to return html content
//All starts with print_*
//--------------------------------------------


function print_article_body($content){
	$html ="";
	if($content === false){
		$html .= "Kosong";
		return $html;
	}

	//convert to html
	$html .= render_to_html($content['body']);
	$html .= "<hr>";
	return $html;
}


function print_share_buttons($urlname){
	$html ="";
	$fb_link = "https://www.facebook.com/sharer/sharer.php?u=";
	$tw_link = "https://twitter.com/intent/tweet?url=";
	$sharelink = "https://testermelon.com/article/" . $urlname;

	$html .= '<div id="social-links">';
	$html .= "Bagikan ke: ";
	$html .= '<a target="_blank" href="' . $fb_link . $sharelink . '"> Facebook </a>';
	$html .= ", ";
	$html .= '<a target="_blank" href="' . $tw_link . $sharelink . '"> Twitter  </a>';
	$html .= '</div>';
	return $html;
}

function print_article_header($content){
	$html ="";
	if($content === false){
		$html .= "Ups, artikel itu sepertinya tidak ada atau belum dibuat.";
		return $html;
	}

	$html .= "<h2>" ;
	$html .= $content['title'];
	$html .= '<br>';
	$html .= "</h2>";
	$html .= "<small> ". format_date($content['date']) . " </small>" ;
	$html .= "<br>";
	$html .= print_share_buttons($content['urlname']);
	$html .= "<hr>";
	return $html;
}

/*Show category menu
 * Reads $dataroot and show directories as categories,
 * formatted as unordered list.
 * Sorted alphabetically (case sensitive)
 *
 * Return the html of the list
 */
function print_cat_menu($request_cat, $dataroot){
	$html = '<ul class="navi" id="categories">';

	//reads files and dirs in $dataroot
	$menu_items = glob("$dataroot*");

	//generating list of categories
	foreach($menu_items as $items ){
		if(is_dir($items)){
			//take required data of each categories
			$itemarray = explode("/",$items);
			$item_name = array_pop($itemarray);
			$link = "/category/" . $item_name;

			//render each category link
			$html .= '<li> <a href="' . $link . '"';
			//determine if category was set and give appropriate class
			if($item_name == $request_cat) {
				$html .= 'class="active"';
			}/*
			if(isset($_GET['article'])){
				$path = get_article_path($_GET['article'],$dataroot);
				if(strpos($path,$item_name) !== false){
					$html .= 'class="active"';
				}
			}*/
			$html .= '>' . $item_name . '</a> </li>';
		}
	}
	$html .= "</ul>";
	return $html;
}

/*takes array of file path string and spits out html of
 * the article list
 *
 * Currently fixed to sort by date (newest first)
 */
function print_itemlist($all_files){
	$html ="";

	if($all_files == []){
		$html .= "<p> Masih Kosong </p>";
		return $html;
	}

	$urlname_list = [];

	//open files and obtain metadata of each files
	foreach($all_files as $files ){
		$hfile = fopen($files, 'r');

		//take data and append to list
		$date = fgets($hfile);
		$title = fgets($hfile);
		$path = explode('/',$files);
		$urlname = array_pop($path);
		$cat = array_pop($path);
		//use date as key to enable simple sorting below
		$urlname_list += array($date => [$title,$urlname,$cat]);

		fclose($hfile);
	}

	//sort list according to date (used as key)
	krsort($urlname_list);

	//print data to html
	foreach($urlname_list as $date => $details){
		$link = '/article/' . $details[1];
		$linkcat = '/category/' . $details[2];

		$html .= '<a href="' . $link . '">' . $details[0] . '</a>';
		$html .= "<br>";
		$html .= "<small>";
		$html .= format_date($date) . ', dalam ';
		$html .= '<a href="' . $linkcat . '">' . $details[2] . '</a>';
		$html .= "</small>";
		$html .= "<br> <br>";
	}
	return $html;
}

//Print recent articles
function print_recent($dataroot){
	$html = "";
	$all_files = glob("$dataroot*/*");

	$html .= '<h2> Artikel Terbaru </h2>';
	$html .= "<p>";
	$html .= print_itemlist($all_files);
	$html .= "</p>";

	return $html;
}

//Print recent articles in a category
function print_category($cat,$dataroot){
	$html = "";
	$all_files = glob("$dataroot$cat/*");

	$html .= "<h2> $cat </h2>";
	$html .= "<p>";
	$html .= print_itemlist($all_files);
	$html .= "</p>";

	return $html;
}

function print_article_nav_away($content){
	$catlink = '/category/'. $content['cat'];
	$html = "";
	$html .= '<div id="nav-away"> ';
	$html .= "Kembali ke:";
	$html .= "<br>";
	$html .= '<a href="'. $catlink . '">' ;
	$html .= '&#171 Kategori ' . $content['cat'] ;
	$html .= '</a>';
	$html .= '<br>'; 
	$html .= '<a href="/"> &#171 Halaman Depan  </a>';
	$html .= '</div>';

	return $html;
}
?>
