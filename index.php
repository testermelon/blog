<?php
include "rendering.php";
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?php echo $config['base']; ?>" >
	<link rel="stylesheet" type="text/css" href="<?php echo $css_path; ?>">
	<link type="image/x-icon" rel="icon" href="favicon-blog.ico" >
	<title> <?php echo $content['title']; ?> </title>
	<meta property="og:image" content="<?php echo $content['thumbnail']; ?>" >
	<meta property="og:type" content="article" >
</head>

<body>
	<header>
		<h1> <a href="/"> <img src="/img/top-logo.png" alt="~testermelon" /> </a> </h1>
		<?php echo $comp_category_menu ?>
	</header>

	<div class="main">
		<?php echo $comp_main; ?>
	</div>

	<footer>
		<ul class="navi">
			<li><a href="/article/about" >Tentang testermelon</a></li>
		</ul>
	</footer>

</body>
</html>
