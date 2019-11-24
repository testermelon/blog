<?php
//check if config file exists, 
//if exist, include it, if not load default config file
if(file_exists('config.php'))
	$config = include('config.php');
else
	$config = include('defaults.php');

include(dirname(__FILE__) . '/renderer.php');
?>

<!DOCTYPE html>
<html>
<head>
	<base href="<?php echo $config['base']; ?>" >
	<title> <?php
		if(isset($_GET['category']))
			echo "testermelon - ". $_GET['category'];
		else
		if(isset($_GET['article'])){
			$content = get_article_content($_GET['article'],$config['dataroot']);
			echo $content['title'];
		}
		else
			echo "testermelon - Home";
	?> </title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" type="text/css" 
		href="<?php 
		//using '//' to force the link as absolute reference to file
		echo '//' . $_SERVER['SERVER_NAME'] . $config['csspath']; 
	?>">

		<link type="image/x-icon" rel="icon" href="favicon-blog.ico" >

	<meta property="og:image" 
		content="<?php
		//load image for social media if set. If not load default thumbnail image
		if (isset($content['thumbnail']))
			echo $content['thumbnail'];
		else 
			echo 'http://testermelon.com/img/testermelon-banner.png';
	?>" />

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
			show_article_header($content);
			show_article_body($content);
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
