<?php
function show_menu($dataroot){
	$cat = "";
	echo '<ul class="navi" id="categories">';
	$menu_items = glob("$dataroot*");
	foreach($menu_items as $items ){
		$itemarray = explode("/",$items);
		$item_name = array_pop($itemarray);
		$link = "/category/" . $item_name;
		if(is_dir($items)){
			echo '<li>';
			echo '<a href="' . $link . '"';
			if($item_name == $_GET['category']) {
				echo 'class="active"';
				$cat = $item_name;
			}
			if(isset($_GET['article'])){
				$path = get_article_path($_GET['article']);
				if(strpos($path[0],$item_name) !== false){
					echo 'class="active"';
					$cat = $item_name;
				}
			}
			echo '>' ;
			echo $item_name;
			echo '</a>';
			echo "</li>";
		}
	}
	echo "</ul>";
	return $cat;
}

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

function show_itemlist($all_files){

	if($all_files == []){
		echo "<p> Masih Kosong </p>";
		return;
	}

	$urlname_list = [];
	foreach($all_files as $files ){
		$hfile = fopen($files, 'r');

		$date = fgets($hfile);
		$title = fgets($hfile);
		$path = explode('/',$files);
		$urlname = array_pop($path);
		$cat = array_pop($path);
		$urlname_list += array($date => [$title,$urlname,$cat]);

		fclose($hfile);
	}

	krsort($urlname_list);

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

function show_recent($dataroot){
	echo '<h2> Artikel Terbaru </h2>';
	$all_files = glob("$dataroot*/*");
	echo "<p>";
	show_itemlist($all_files);
	echo "</p>";
}

function show_category($cat,$dataroot){
	echo "<h2> $cat </h2>";
	$all_files = glob("$dataroot$cat/*");
	echo "<p>";
	show_itemlist($all_files);
	echo "</p>";
}

function get_article_path($article,$dataroot){
	$filepath = glob("$dataroot$article");
	if($filepath == [])
		$filepath = glob("$dataroot*/$article");
	return $filepath[0];
}

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

function get_article_content($article,$dataroot){
	$filepath = get_article_path($article,$dataroot);
	if($filepath == ""){
		//File tidak ditemukan
		return false;
	}

	return get_article_data($filepath);
}

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
 * Shows the content of an article whose urlname is provided with $urlname
 * and the category is $category
 * Spits out the html content of the article with the article title under <h2>
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
