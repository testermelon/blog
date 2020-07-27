<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?php echo $htmlcontent['conf-base']; ?>" >
	<link rel="stylesheet" type="text/css" href="<?php echo $htmlcontent['active-css']; ?>">
	<title> <?php echo $htmlcontent['title']; ?> </title>
	<meta property="og:title" content="<?php echo $htmlcontent['title']; ?>" >
	<meta property="og:image" content="<?php echo $htmlcontent['thumbnail']; ?>" >
	<meta property="og:type" content="article" >
	
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	
</head>

<body>
<header>
	<?php echo $htmlcontent['header'] ?>
</header>

<div class="main">
	<?php echo $htmlcontent['main']; ?>
</div> <!-- div main --> 

<br>
<footer>
	<?php echo $htmlcontent['footer'] ?>
</footer>

</body>
</html>