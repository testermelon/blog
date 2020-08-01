<?php

//--------------------------------------------
//Content data getter and and helper manipulators
//---------------------------------------------

/************************************************
 *
 * This library should become an API for data storage of the blog
 *
 * Posts are organized in single text files containing metadata
 * Posts can be grouped into directories
 * Directories should have a file inside it to contain its metadata
 *
 * What requests can be done to this database?
 * - List items inside a directory
 * - Retrieve data in a post file
 * - List directories inside a directory
 * - Retrieve data about a directory
 *
 * This is a read only database 
 * For writing, use a text editor to directly edit the files
 *
 * ********************************************** */

/*******************************************
 * to be fed into $content['urlname-list'] = 
 * ****************************************/
function get_urlname_list($dataroot, $glob_string ) {

	$all_files = glob("$dataroot$glob_string");

	$meta = [];
	//open files and obtain metadata of each files
	foreach($all_files as $files ){
		$hfile = fopen($files, 'r');
		if($hfile==false)
			continue;
		$path = explode('/',$files);
		$urlname = array_pop($path);
		if($urlname=='--info') 
			continue;
		$cat = array_pop($path);
		//take data and append to list
		$date = fgets($hfile);
		$title = fgets($hfile);
		//use date as key to enable simple sorting below
		array_push($urlname_list,array($date => [$title,$urlname,$cat]));

		fclose($hfile);
	}

	//sort list according to date (used as key)
	krsort($urlname_list);

	return $urlname_list;
}

/************************************************
 * to be fed to $content['categories'] =
 * input: 
 * output: 
 * 	$cat_data['names'] (just the names), 
 * 	$cat_data['active'] (currently active category)
 *************************************************/

function get_categories($request_cat, $dataroot) {

	$cat_data['ids'] = [];
	$cat_data['title'] = [];
	$cat_data['order'] = [];


	//reads files and dirs in $dataroot
	$menu_items = glob("$dataroot*");

	//generating list of categories
	foreach($menu_items as $items ){
		if(is_dir($items)){
			//take required data of each categories
			$itemarray = explode("/",$items);
			$name = array_pop($itemarray);
			array_push($cat_data['names'],$name);
		}
	}
	return $cat_data;
}

/*Obtain article path from article url-name
 * $article = url-name to find
 */
function get_article_path($article,$dataroot){
	$filepath = glob("$dataroot$article");
	if($filepath == [])
		$filepath = glob("$dataroot*/$article");
	return $filepath[0];
}

/*Obtain article data for a given article filepath
 * Returns array of data for an article with the following keys:
 * date: YYYYMMDD
 * title: article title string
 * body: article text body
 * and other metadata
 */

/* Function as front end for get_article_data() 
 *
 * Uses get_article_path to extract file path of the actual article file
 * and then passes the filepath to get_article_data.
 *
 * Returns the article content array
 *
 * Note: This function is where article data passed around,
 * expand data as required in the future
 */
function get_article_content(&$content,$article,$dataroot,$imgpath){
	$filepath = get_article_path($article,$dataroot);
	if($filepath == ""){
		//File tidak ditemukan
		$content['status'] = '404';
		return false;
	}

	get_data($content, $filepath);

	//manipulate data according to context
	
	$content['urlname'] = $article;

	$path_nodes = explode('/',$filepath);
	array_pop($path_nodes);
	$content['cat'] = array_pop($path_nodes);

	//load image for social media if set. If not load default thumbnail image
	if ($content['thumbnail']=="")
		$content['thumbnail'] = 'https://testermelon.com/testermelon-social.png';
	else{
		$content['thumbnail'] = 'https://testermelon.com'. $imgpath . $content['thumbnail'];
	}

	$content['body'] = render_to_html($content['body'],$imgpath);
}

function render_puisi($puisi){
	$rendition =[];
	foreach($puisi as $poem){
		$poem = str_replace('{puisi}','',$poem);
		$poem = str_replace('{/puisi}','',$poem);
		$poem = preg_replace("/(\n|\r\n)/",'<br>',$poem);
		$poem = trim($poem);
		$poem = '<span class="poem" style="font-style:italic">' . $poem . '</span>';
		array_push($rendition,$poem);
	}
	return $rendition;
}

/* Use regex to parse and convert markdown syntaxes to html
 *
 * Not complete as markdown parser, only subset
 */
function render_to_html($string,$dataroot){

	//TODO: Make handling for literal tags 
	// HOW : 
	// 1. Cut paste the tags, and leave numbered markers on the string
	// 2. Deal with each of literal tags applying helper converters if necessary
	// 3. Do regex replacements as usual
	// 4. Finally paste back the tags into the numbered markers on the string

	$puisi = [];
	preg_match_all('/{puisi}.*?{\/puisi}/s',$string,$puisi);
	$string = preg_replace('/{puisi}.*?{\/puisi}/s','{..puisi..}',$string);
	$puisi = render_puisi($puisi[0]);

	//links
	$string = preg_replace('/(?<=[^!])\[(.*?)\]\((.*?)\)/','<a href="$2" target="_blank">$1</a>',$string);
	
	//images
	$img_repl_str = '<img src="' . $dataroot . '$2" alt="$1" />';
	$string = preg_replace('/!\[(.*?)\]\((.*?)\)/',$img_repl_str,$string);

	//emphasis
	$string = preg_replace('/\*{2}(.*?)\*{2}/','<em>$1</em>',$string);

	//ul
	$string = preg_replace('/(?<=\n)-\s*(.*)/','<uli>$1<li>',$string);
	$string = preg_replace('/((<uli>.*<li>\s*)+)/', "</p>\n<ul>$1</ul>\n<p>", $string);
	$string = preg_replace('/<uli>(.*)<li>/','<li>$1</li>', $string);
	
	//ol
	$string = preg_replace('/(?<=\n)[0-9]\.\s*(.*)/','<oli>$1<li>',$string);
	$string = preg_replace('/((<oli>.*<li>\s*)+)/','<ol>$1</ol>',$string);
	$string = preg_replace('/<oli>(.*)<li>/','<li>$1</li>', $string);

	//Headings
	$string = preg_replace('/#{2}([^\r^\n]+)/',"<h3>$1</h3>",$string);

	//paragraphs
	$string = preg_replace('/\A/', '<p>', $string);
	$string = preg_replace('/\Z/', '</p>', $string);
	$string = preg_replace('/(\n\n|\r\n\r\n)/', "</p>\n\n<p>", $string);

	foreach($puisi as $poem){
		$string = preg_replace('/{..puisi..}/',$poem,$string,1);
	}

	return $string;
}

/*takes date in format YYYYMMDD and spits out date text string
 */
function format_date($date){
	$year = substr($date,0,4);
	$month = substr($date,4,2);
	if($month == '01') $month = 'Januari';
	if($month == '02') $month = 'Februari';
	if($month == '03') $month = 'Maret';
	if($month == '04') $month = 'April';
	if($month == '05') $month = 'Mei';
	if($month == '06') $month = 'Juni';
	if($month == '07') $month = 'Juli';
	if($month == '08') $month = 'Agustus';
	if($month == '09') $month = 'September';
	if($month == '10') $month = 'Oktober';
	if($month == '11') $month = 'November';
	if($month == '12') $month = 'Desember';
	$day = substr($date,6,2);

	return $day . " " . $month . " " . $year;
}

?>
