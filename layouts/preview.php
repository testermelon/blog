<?php

function fetch_data(&$data, $config, $request){
	get_article_data($data, $request['path']);
}

function render(&$htmlcontent,$data){
	if ($data['status'] == '404' ){
		$htmlcontent['main'] =  print_404_article();
	}else{
		$htmlcontent['main'] .= print_article_header($data);
		$htmlcontent['main'] .= print_article_body($data);
	}
}

?>

