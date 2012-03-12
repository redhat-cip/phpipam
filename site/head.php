<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<meta name="Description" content=""> 
	<meta name="title" content="<?php print $settings['siteTitle']; ?>"> 
	<meta name="robots" content="noindex, nofollow"> 
	<meta http-equiv="X-UA-Compatible" content="IE=9" >
	<meta name="viewport" content="width=1024, initial-scale=1, user-scalable=yes">
	
	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">
  
	<!-- title -->
	<title><?php print $settings['siteTitle']; ?></title>
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/style.min.css">
	<link rel="shortcut icon" href="css/images/favicon.ico">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jclock.jquery.js"></script>
	<script type="text/javascript" src="js/magic.min.js"></script>
	<script type="text/javascript" src="js/buttons.js"></script>
	<?php 
	if ($settings['showTooltips'] == 1) { 
		print '<script type="text/javascript" src="js/jquery.tools.tooltip.min.js"></script>'; 
		print '<script type="text/javascript" src="js/tooltips.js"></script>'; 	
	} 
	?>

	<!-- HighCharts script -->
	<script type="text/javascript" src="js/Highcharts-2.2.0/highcharts.js"></script>

	<!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="css/ie.css" />
    <![endif]-->
    
    <!--[if gte IE 9]>
    <link rel="stylesheet" type="text/css" href="css/ie9.css" />
	<![endif]-->

	<!--[if IE 6]>
	<script type="text/javascript" src="js/dieIE.js"></script>
	<![endif]-->
	<!--[if IE 7]>
	<script type="text/javascript" src="js/dieIE.js"></script>
	<![endif]-->
	
	<!-- full page width theme -->
	<?php
	$user = getActiveUserDetails ();
	if($user['useFullPageWidth'] == 1) {
		print '<link rel="stylesheet" type="text/css" href="css/styleFullWidth.css">'. "\n";
	}
	?>
</head>