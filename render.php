<!DOCTYPE html>
<html>
<head>
	<title>
<?php
	$config = include('config.php');
	include 'menu.php';

	if(isset($_GET['category']))
		echo "testermelon - ". $_GET['category'];
	else
	if(isset($_GET['article'])){
		$content = get_article_content($_GET['article'],$config['dataroot']);
		echo $content['title'];
	}
	else
		echo "testermelon - Home";
?>
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="theme.css">
	<meta property="og:image" content="<?php
	if (isset($content['thumbnail']))
		echo $content['thumbnail'];
	else 
		echo 'http://testermelon.com/img/testermelon-banner.png';?>" />

	<meta property="og:type" content="article" />
</head>

<body>

<header>
<h1> <a href="/"> <img src="/img/top-logo.png" alt="~testermelon" /> </a> </h1>

<?php
	show_menu($config['dataroot']);
?>

</header>

<div class="main">
<?php
	if(isset($_GET['category']))
		show_category($_GET['category'],$config['dataroot']);
	else
	if(isset($_GET['article'])){
		show_article($content,$config['dataroot']);
	}
	else{
		show_recent($config['dataroot']);
}

?>
</div>

<footer>
<ul class="navi">
<li><a href="/article/about" >About Me</a></li>
</ul>
</footer>

</body>

</html>
