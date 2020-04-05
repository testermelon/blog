<?php
include "rendering.php";
?>

<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?php echo $htmlcontent['conf-base']; ?>" >
	<link rel="stylesheet" type="text/css" href="<?php echo $htmlcontent['active-css']; ?>">
	<link type="image/x-icon" rel="icon" href="/favicon-blog.ico" >
	<title> <?php echo $htmlcontent['title']; ?> </title>
	<meta property="og:image" content="<?php echo $htmlcontent['thumbnail']; ?>" >
	<meta property="og:type" content="article" >
</head>

<body>
<header>
	<h1> <a href="/"> ∿ testermelon </a> </h1>
	<?php echo $htmlcontent['category-menu'] ?>
</header>

<div class="main">
	<?php echo $htmlcontent['main']; ?>
</div> <!-- div main --> 

<footer>
	<ul class="navi">
	<li> <a href="/article/about" >tentang testermelon</a> </li>
	</ul> <!-- navi -->
	<?php echo $htmlcontent['theme-buttons']; ?>
</footer>

</body>
</html>
