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


function print_article_body($content,$imgpath){
	$html ="";
	if($content === false){
		$html .= "<br> Kosong <br>";
		return $html;
	}

	$html .= render_to_html($content['body'],$imgpath);
	$html .= "<br>";
	return $html;
}


function print_share_buttons($social_uri) {
	$fb_link = "https://www.facebook.com/sharer/sharer.php?u=";
	$tw_link = "https://twitter.com/intent/tweet?url=";
	$sharelink = $_SERVER['SERVER_NAME'].'/'.$social_uri;
	$html .= '<div id="social-links">';
	$html .= "Bagikan ke: ";
	$html .= '<a target="_blank" href="' . $fb_link . $sharelink . '"> Facebook </a>';
	$html .= ", ";
	$html .= '<a target="_blank" href="' . $tw_link . $sharelink . '"> Twitter  </a>';
	$html .= '</div>';
	return $html;
}

function print_article_header($data,$dataroot,$target_path){
	$social_uri	= str_replace($dataroot,'',$target_path);
	$html = "<h1>" . $data['title'] . "</h1>";
	$html .= "<small> ". format_date($data['date']) . " </small>" ;
	$html .= "<br>";
	$html .= print_share_buttons($social_uri);
	$html .= "<br>";

	return $html;
}

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

		//var_dump($catit);
		//var_dump($target_path);
		if(strpos($target_path, $catit) !== false){
			$active_dir = $meta['title'];
		}
		fclose($hfile);
	}
	//var_dump($ol_cat);
	
	//sort list according to order (used as key)
	ksort($ol_cat);

	$html = "";

	//elements for mobile
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

function print_article_nav_away($dataroot,$target_link){
	//var_dump($target_link);
	$catlink = str_replace($dataroot,'',$target_link);
	//var_dump($catlink);
	$catlink = explode('/',$catlink);
	array_pop($catlink);
	$catlink = implode('/',$catlink);
	//var_dump($catlink);

	$html = "";
	$html .= '<div id="nav-away"> ';
	$html .= "Kembali ke:";
	$html .= "<br>";
	$html .= '<a href="/'.$catlink.'">' ;
	$html .= '&#171 Kategori ' ;
	$html .= '</a>';
	$html .= '<br>'; 
	$html .= '<a href="/"> &#171 Beranda  </a>';
	$html .= '</div>';

	return $html;
}

/*takes array of file path string and spits out html of
 * the article list
 *
 * Currently fixed to sort by date (newest first)
 */
function print_urlname_list($dataroot,$target_path){

	//this function only accept directories
	if(strpos($target_path,'--info') == false)
		return "<p>Tidak ada data</p>";

	//cd .. 
	$dirpath = str_replace($dataroot,'',$target_path);
	$dirpath = str_replace('--info','',$dirpath);
	//var_dump($dirpath);

	//fetching data of files in the directory
	$urlname_list = [];
	$dirls = glob("$dataroot$dirpath*");
	if($dirls == []){
		return "<p> Kategori Kosong </p>";
	}
	foreach($dirls as $files){
		if(strpos($files,'--info') != false)
			continue;
		$meta = get_file_metadata($files,array('title','date'));
		if($meta == [])
			continue;
		$link = str_replace($dataroot,'',$files);
		$urlname_list[$meta['date']] = array('title' => $meta['title'], 'link' => $link);
	}
	if($urlname_list == [])
		return "<p> Masih Kosong </p>";
	krsort($urlname_list);

	//print data to html
	foreach($urlname_list as $date => $details){
		$linkcat = '/' . $details[2];

		$html .= '<a href="' . $details['link']. '">' . $details['title'] . '</a>';
		$html .= "<br>";
		$html .= "<small>";
		$html .= format_date($date);
		$html .= "</small>";
		$html .= "<br> <br>";
	}
	return $html;
}

function print_music_item($src,$imgsrc,$title,$link) {
	$html .= '
		<div class="mplayer" style="display:flex">
			<div style="border-radius:5px;width:150px;">	
				<img src="'.$imgsrc.'" style="max-width:150px;padding:0;border-radius:5px;"> 
			</div>
			<div style="display:flex;flex-direction:column;flex-grow:1;justify-content:space-between">
				<div style="margin:auto;text-align:center">
					<a href="' .$link. '">' .$title. '</a>
				</div>
				<div style="flex:grow;padding:10px;">
					<audio style="vertical-align:bottom;width:100%;" src="'.$src.'" controls></audio>
				</div>
			</div>
		</div>
		';
	return $html;
}


function print_music_playlist($config,$target_path){

	$songpath = $config['songpath'];
	$imgpath = $config['imgpath'];
	$dataroot = $config['dataroot'];

	//obtain dir path:
	if(is_dir($target_path))
		$dirpath = $target_path;
	else{
		if(strpos($target_path,'--info') == false)
			//not having --info means not directory metadata
			return "Bukan data playlist";
		else
			//yep a directory metadata, take out the --info, and use this
			$dirpath = str_replace('--info','',$target_path);
	}

	$dirls = glob("$dirpath*");
	if($dirls == [])
		return "Masih kosong";

	foreach($dirls as $songs){
		//skip the dir metadata
		//var_dump($songs);
		if(strpos($songs,'--info') !== false)
			continue;
		$meta = get_file_metadata($songs,[]);
		if($meta == [])
			continue; 
		//var_dump($meta);
		$songfile = $meta['songfile'];
		$imgfile = $meta['illust'];
		$link = str_replace($dataroot,'',$songs);
		$html .= print_music_item("$songpath$songfile","$imgpath$imgfile",$meta['title'],$link);
	}
	return $html;
}

function print_large_music_item($config,$data){

	$imgsrc = $config['imgpath'] . $data['illust'];
	$src = $config['songpath'] . $data['songfile'];

	$html .= '
		<div class="mplayer" style="display:flex;flex-direction:column">
			<div style="border-radius:5px;">	
				<img src="'.$imgsrc.'" style="padding:0;border-radius:5px;"> 
			</div>
			<div style="display:flex;flex-direction:column;flex-grow:1;justify-content:space-between">
				<div style="flex:grow;padding:10px;">
					<audio style="vertical-align:bottom;width:100%;" src="'.$src.'" controls></audio>
				</div>
			</div>
		</div>
	';
	return $html;
}

//print a theme select button
function print_theme_buttons($active_css) {
	$html .= '<form id="theme-button" method="POST" >';
	if(strpos($active_css, "light") != false)
		$html .= '<button type="submit" name="theme" value="gelap">&#127769; </button>';
	if(strpos($active_css, "dark") != false)
		$html .= '<button type="submit" name="theme" value="terang"> &#128262;  </button>';
	$html .= "</form>";
	return $html;
}

function print_footer($config){
	$html .= '<div class="footer-navi">';
	$html .= print_theme_buttons($config['csspath']);
	$html .= '<a style="font-family:opensans;padding:9px;float:right" href="/about"> tentang testermelon </a> ';
	$html .= '</div>';

	return $html;
}

function print_head_thumbnail($imgpath,$thumbnail){
	if($thumbnail == ''){
		$thumbnail = '/testermelon-social.png';
	}else{
		$thumbnail = $imgpath . $thumbnail;
	}
	return 'https://'.$_SERVER['SERVER_NAME'] . $thumbnail;
}

?>
