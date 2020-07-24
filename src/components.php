<?php

//--------------------------------------------
//Components, functions to return html content
//All starts with print_
//
//This should contain functions to return elements of page (components)
//
//If the component needs to fetch or manipulate data,
//preferably it should be the one to do it independently.
//The caller (layouts) should just call them without fetching or manipulating data first.
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
	
function print_article_header($data){
	$html = "<h1>" . $data['title'] . "</h1>";
	$html .= "<small> ". format_date($data['date']) . " </small>" ;
	$html .= "<br>";
	$html .= print_share_buttons($data);
	$html .= "<br>";

	return $html;
}

/*Show menu
 * Reads $dataroot and show directories as categories,
 * formatted as unordered list.
 * 
 * Sorted by the metadata "order" in the --info file
 */

function print_menu($dataroot,$target_path){

	//getting list of directories
	$lsroot = glob("$dataroot*");
	$cats = [];
	foreach($lsroot as $it) {
		if(is_dir($it)){
			array_push($cats,$it);
		}
	}
	//var_dump($cats);

	//obtaining directories data and extract relevant info
	$ol_cat = [];
	$active_dir = "";
	foreach($cats as $catit){
	//	var_dump($catit);
		$fname = $catit . "/--info";
		$hfile = fopen($fname, 'r');
		if($hfile == false) {
			return false;
		}

		$meta = [];
		do{
			$temp_read = trim(fgets($hfile));
			if($temp_read == '----')
				break;
			$metadata = explode('=',$temp_read);
			$meta[$metadata[0]] = $metadata[1];
		}while( ($temp_read != '----' ) && !feof($hfile) );

		$dir_link = end(explode('/',$catit));
		//var_dump($dir_link);

		//use order as key to enable simple sorting
		$ol_cat += array($meta['order']  => ['title' => $meta['title'],'path' => $dir_link]);
		//take note of active dir title

		//var_dump($fname);
		//var_dump($target_path);
		if($fname == $target_path) 
			$active_dir = $meta['title'];
		fclose($hfile);
	}
	//var_dump($ol_cat);
	
	//sort list according to order (used as key)
	ksort($ol_cat);

	$html = "";

	//element for mobile
	$html .= '<label class="navi" for="menu-toggle">'; 
	$html .= '<li>';
	$html .= '<a>&#9776; &nbsp;' . $active_dir . '</a> ';
	$html .= '</li>';
	$html .= '</label>';
	$html .= '<input id="menu-toggle" type="checkbox" style="display:none"> </input>';

	//menu list
	$html .= '<ul class="navi" id="categories">';
	foreach($ol_cat as $order => $dirs ){
		$html .= '<li ';
		//determine if category was set and give appropriate class
		if($dirs['title'] == $active_dir)
			$html .= 'class="active"';
		$html .= '>';
		$html .= '<a href="/'.$dirs['path'].'">'.$dirs['title'].'</a>';
		$html .= '</li>';
	}
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
function print_urlname_list($dataroot,$target_path){

	$dirpath = end(explode($dataroot,$target_path));
	$dirpath = str_replace('--info','',$dirpath);
	var_dump($dirpath);

	$content['urlname_list'] = get_urlname_list($dataroot,"$dirpath*");

	if($content['urlname-list'] == []){
		$html .= "<p> Masih Kosong </p>";
		return $html;
	}

	//print data to html
	foreach($content['urlname-list'] as $date => $details){
		$link = '/'.$details[2] . '/' . $details[1];
		$linkcat = '/' . $details[2];

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


function print_music_item($src,$imgsrc,$title) {
	$html .= '
		<div style="padding:5px;margin-top:20px; display:flex ">
			<img src="'.$imgsrc.'" style="display:block;height:150px;width:150px"> 
			<div style="line-height:75px">
				<span style="vertical-align:top;font-size:1.5em;padding:5px;">'.$title.'</span>
				<audio style="vertical-align:bottom;width:100%;display:inline-block" src="'.$src.'" controls></audio>
			</div>
		</div>
		';
	return $html;
}


?>
