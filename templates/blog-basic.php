<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?php echo '//'.$_SERVER['SERVER_NAME'] ?>" >
	<link rel="stylesheet" type="text/css" href="<?php echo '//'.$_SERVER['SERVER_NAME'].'/css/basenew.css' ?>">
	<title> <?php echo $htmlcontent['title']; ?> </title>
	<meta property="og:title" content="<?php echo $htmlcontent['title']; ?>" >
	<meta property="og:image" content="<?php echo $htmlcontent['thumbnail']; ?>" >
	<meta property="og:type" content="article" >
	
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo $htmlcontent['title']; ?>">
	<!-- Twitter summary card with large image must be at least 280x150px -->
	<meta name="twitter:image:src" content="<?php echo $htmlcontent['thumbnail']; ?>"> 
	
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

	<?php echo $htmlcontent['head-js-modules'] ?>

</head>

<body>
<div class="backdrop">
	<div class="container">
		<nav>
			<?php echo $htmlcontent['navigation']; ?>
		</nav>
		<main>
			<article class="main">
				<?php echo $htmlcontent['main']; ?>
			</article>
		</main>
		<br>
		<footer>
			<?php echo $htmlcontent['footer'] ?>
		</footer>
	</div> 
</div>
</body>
</html>
