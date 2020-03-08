<?php

//--------------------------------------------
//Content data getter, setter, and manipulators
//---------------------------------------------

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
 */
function get_article_data($filepath){
	$content = [];

	$hfile = fopen($filepath,'r');
	if(!$hfile)
		return $content;

	$content['date']  = fgets($hfile);
	$content['title'] = fgets($hfile);

	do{
		$temp_read = fgets($hfile);
		if(feof($hfile)) 
			return;
		if(trim($temp_read) == '----')
			break;
		$metadata = explode(',',$temp_read);
		$content[$metadata[0]] = $metadata[1];
	}while($temp_read != '----');

	$content['body'] ="";
	while(!feof($hfile)){
		$content['body'] .= fgets($hfile);
	}
	fclose($hfile);

	return $content;
}

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
function get_article_content($article,$dataroot){
	$filepath = get_article_path($article,$dataroot);
	if($filepath == ""){
		//File tidak ditemukan
		return false;
	}

	$content = get_article_data($filepath);

	//getting data that is not inside the file
	//but inferred from context
	
	$content['urlname'] = $article;

	$path_nodes = explode('/',$filepath);
	array_pop($path_nodes);
	$content['cat'] = array_pop($path_nodes);

	//load image for social media if set. If not load default thumbnail image
	if ($content['thumbnail']=="")
		$content['thumbnail'] = 'http://testermelon.com/img/testermelon-banner.png';

	return $content;
}

/* Use regex to parse and convert markdown syntaxes to html
 *
 * Not complete as markdown parser, only subset
 */
function render_to_html($string){
	//links
	$string = preg_replace('/(?<=[^!])\[(.*?)\]\((.*?)\)/','<a href="$2" target="_blank">$1</a>',$string);
	
	//images
	$string = preg_replace('/!\[(.*?)\]\((.*?)\)/','<img src="$2" alt="$1" />',$string);

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

	return $string;
}

?>
