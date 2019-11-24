<?php
/*Show category menu
 * Reads $dataroot and show directories as categories,
 * formatted as unordered list.
 * Returns the currently selected category name (case sensitive)
 */
function show_menu($dataroot){
	//category to be returned
	$cat = "";

	//reads files and dirs in $dataroot
	$menu_items = glob("$dataroot*");

	//generating list of categories
	echo '<ul class="navi" id="categories">';
	foreach($menu_items as $items ){
		if(is_dir($items)){
			//take required data of each categories
			$itemarray = explode("/",$items);
			$item_name = array_pop($itemarray);
			$link = "/category/" . $item_name;

			//render each category link
			echo '<li> <a href="' . $link . '"';
			//determine if category was set and give appropriate class
			if($item_name == $_GET['category']) {
				echo 'class="active"';
				$cat = $item_name;
			}
			if(isset($_GET['article'])){
				$path = get_article_path($_GET['article'],$dataroot);
				if(strpos($path,$item_name) !== false){
					echo 'class="active"';
					$cat = $item_name;
				}
			}
			echo '>' . $item_name . '</a> </li>';
		}
	}
	echo "</ul>";
	return $cat;
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

/*takes array of file path string and spits out html of
 * the article list
 *
 * Currently fixed to sort by date (newest first)
 */
function show_itemlist($all_files){

	if($all_files == []){
		echo "<p> Masih Kosong </p>";
		return;
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

		echo '<a href="' . $link . '">' . $details[0] . '</a>';
		echo "<br>";
		echo "<small>";
		echo format_date($date) . ', dalam ';
		echo '<a href="' . $linkcat . '">' . $details[2] . '</a>';
		echo "</small>";
		echo "<br> <br>";
	}
}

//Print recent articles
function show_recent($dataroot){
	echo '<h2> Artikel Terbaru </h2>';
	$all_files = glob("$dataroot*/*");
	echo "<p>";
	show_itemlist($all_files);
	echo "</p>";
}

//Print recent articles in a category
function show_category($cat,$dataroot){
	echo "<h2> $cat </h2>";
	$all_files = glob("$dataroot$cat/*");
	echo "<p>";
	show_itemlist($all_files);
	echo "</p>";
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
	$hfile = fopen($filepath,'r');

	$content = [];

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
 */
function get_article_content($article,$dataroot){
	$filepath = get_article_path($article,$dataroot);
	if($filepath == ""){
		//File tidak ditemukan
		return false;
	}

	return get_article_data($filepath);
}

/* Use regex to parse and convert markdown syntaxes to html
 *
 * Not complete as markdown parser, only subset
 */
function render_to_html($string){
	//links
	$string = preg_replace('/(?<=[^!])\[(.*?)\]\((.*?)\)/','<a href="$2">$1</a>',$string);
	
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


/*
 * Spits out the html content of the article with the article title under <h2>
 *
 * Parameter is article content array extracted from file
 */
function show_article($content){
	if($content === false){
		echo "Ups, artikel itu sepertinya tidak ada atau belum dibuat.";
		return;
	}

	//convert to html
	$content['body'] = render_to_html($content['body']);

	echo "<h2>" ;
	echo $content['title'];
	echo '<br>';
	echo "</h2>";
	echo "<p> <small> ". format_date($content['date']) . " </small> </p>";
	echo "<hr>";

	echo $content['body'];

}

?>
