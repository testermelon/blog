<?php

function render($data,$target_path,$config){
	//html head data
	$htmlcontent['active-css'] = $config['csspath'];
	$htmlcontent['title'] = $data['title'];
	$htmlcontent['thumbnail'] = print_head_thumbnail($config['imgpath'],$data['thumbnail']);
	$htmlcontent['head-js-modules'] = ' <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>	<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script> ';

	//html body data
	$htmlcontent['header'] = print_menu($config['dataroot'],$target_path);
	$htmlcontent['main'] .= print_article_header($data,$config['dataroot'],$target_path);
	$htmlcontent['main'] .= print_article_body($data,$config['imgpath']);
	$htmlcontent['main'] .= print_article_nav_away($config['dataroot'],$target_path);
	$htmlcontent['footer'] = print_footer($config);

	//call template to print html response out
	include("templates/basic.php");
}

?>

