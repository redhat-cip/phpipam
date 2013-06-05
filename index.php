<?php

/* set cookie parameters for max lifetime */
/*
ini_set('session.gc_maxlifetime', '86400');
ini_set('session.save_path', '/tmp/php_sessions/');
*/

session_start();
ob_start();

/* site config */
require('config.php');

/* site functions */
require('functions/functions.php');

# set default page
if(!isset($_REQUEST['page'])) { $_REQUEST['page'] = "dashboard"; }

/* check for new installation */
if($_REQUEST['page'] != "install") { require('functions/dbInstallCheck.php'); }
if($_REQUEST['page'] == "install") { 
	$settings['siteTitle'] = "phpIPAM"; 
}
else {
	/* get all site settings */
	$settings = getAllSettings();
}

/* verify login and permissions */
if($_REQUEST['page'] != "login" && $_REQUEST['page'] != "request_ip"  && $_REQUEST['page'] != "upgrade" && $_REQUEST['page'] != "install") { isUserAuthenticatedNoAjax(); }


if($_REQUEST['page'] != 'upgrade' && $_REQUEST['page'] != "login" && $_REQUEST['page'] != "install") { 
	include('functions/dbUpgradeCheck.php'); 	# check if database needs upgrade 
	include('functions/checkPhpBuild.php');		# check for support for PHP modules and database connection 
}

/* recreate base */
if($_SERVER['SERVER_PORT'] == "443") 		{ $url = "https://$_SERVER[SERVER_NAME]".BASE; }
/* custom port */
else if($_SERVER['SERVER_PORT'] != "80")  	{ $url = "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]".BASE; }
/* normal http */
else								 		{ $url = "http://$_SERVER[SERVER_NAME]".BASE; }

/* site header */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<base href="<?php print $url; ?>" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<meta name="Description" content=""> 
	<meta name="title" content="<?php print $settings['siteTitle']; ?>"> 
	<meta name="robots" content="noindex, nofollow"> 
	<meta http-equiv="X-UA-Compatible" content="IE=9" >
	<meta name="viewport" content="width=1024, initial-scale=0.85, user-scalable=yes">
	
	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">
  
	<!-- title -->
	<title><?php print $settings['siteTitle']; ?></title>
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap-custom.css">
	<link rel="shortcut icon" href="css/images/favicon.ico">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<!-- 	<script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script> -->

	<script type="text/javascript" src="js/jclock.jquery.js"></script>
<!-- 	<script type="text/javascript" src="js/magic.min.js"></script> -->
	<script type="text/javascript" src="js/magic.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

	<?php 
	if ($settings['showTooltips'] == 1) { 
	?>
	<script type="text/javascript">
	$(document).ready(function(){
	     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
	});
	</script>
	<?php
	} 
	?>
	<!--[if IE 6]>
	<script type="text/javascript" src="js/dieIE.js"></script>
	<![endif]-->
	<!--[if IE 7]>
	<script type="text/javascript" src="js/dieIE.js"></script>
	<![endif]-->
</head>

<!-- body -->
<body>

<!-- wrapper -->
<div class="wrapper">

<!-- jQuery error -->
<div class="jqueryError">jQuery error!</div>

<!-- Popups -->
<div id="popupOverlay"></div>
<div id="popup" class="popup popup_w400"></div>
<div id="popup" class="popup popup_w500"></div>
<div id="popup" class="popup popup_w700"></div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><img src="css/images/ajax-loader.gif"></div>

<!-- page header -->
<div id="header">
<div class="hero-unit">
	<a href=""><?php print $settings['siteTitle']; if($_REQUEST['page'] == "login") { print " | "._('login'); } if($_REQUEST['page'] == "install") { print " | "._('installation'); } ?></a>
</div>
</div>


<!-- page user menu -->
<div class="user_menu">
	<?php if($_REQUEST['page'] != "login" && $_REQUEST['page'] != "logout" && $_REQUEST['page'] != "request_ip" && $_REQUEST['page'] != "upgrade" && $_REQUEST['page'] != "install") include('site/userMenu.php');?>
</div>

<!-- page sections / menu -->
<div class="sections_overlay">
<div class="sections">
    <?php if($_REQUEST['page'] != "login" && $_REQUEST['page'] != "logout" && $_REQUEST['page'] != "request_ip" && $_REQUEST['page'] != "upgrade" && $_REQUEST['page'] != "install")  include('site/sections.php');?>
</div>
</div>


<!-- content -->
<div class="content_overlay">
<div class="container-fluid">
	<div class="row-fluid">
		<?php
		/* error */
		if($_REQUEST['page'] == "error") {
			print "<div id='error'>";
			include_once('site/error.php');
			print "</div>";
		}
		/* upgrade */
		else if ($_REQUEST['page'] == "upgrade") {
			print "<div id='dashboard'>";
			include_once("site/upgrade/index.php");
			print "</div>";			
		}
		/* install */
		else if ($_REQUEST['page'] == "install") {
			print "<div id='dashboard'>";
			include_once("site/install/index.php");
			print "</div>";			
		}
		/* login, logout, ipRequest */
		else if($_REQUEST['page'] == "login" || $_REQUEST['page'] == "logout" || $_REQUEST['page'] == "request_ip") {
			print "<div id='dashboard'>";
			include_once("site/login/index.php");
			print "</div>";			
		}
		/* dashboard */
		else if(!isset($_REQUEST['page']) || $_REQUEST['page'] == "dashboard") {
			print "<div id='dashboard'>";
			include_once("site/dashboard/index.php");
			print "</div>";
		}
		# side menus
		else {
			# load left menu
			print "<div id='leftMenu' class='span2'>";
				if($_REQUEST['page'] == "subnets")				{ include_once("site/subnets.php"); }
				else if ($_REQUEST['page'] == "tools")			{ include_once("site/tools/toolsMenu.php"); }
				else if ($_REQUEST['page'] == "administration")	{ include_once("site/admin/adminMenu.php"); }
			print "</div>";
			
			# load content
			print "<div id='content' class='span10'>";
				if( isset($_REQUEST['toolsId']) && (strlen($_REQUEST['toolsId']) == 0) )	{ unset($_REQUEST['toolsId']); }
				# subnets
				if($_REQUEST['page'] == "subnets" && !isset($_REQUEST['subnetId']))					{ print "<div class='alert alert-info alert-dash'><i class='icon-gray icon-chevron-left'></i> "._('Please select subnet from left menu')."!</div>"; }
				else if($_REQUEST['page'] == "subnets")												{ include_once("site/ipaddr/ipAddressSwitch.php"); }
				# tools		
				else if ($_REQUEST['page'] == "tools" && !isset($_REQUEST['toolsId']))				{ print "<div class='alert alert-info alert-dash'><i class='icon-gray icon-chevron-left'></i> "._('Please select tool from left menu!')."</div>"; }
				else if ($_REQUEST['page'] == "tools")												{ include_once("site/tools/$_REQUEST[toolsId].php"); }
				# admin
				else if ($_REQUEST['page'] == "administration"  && !isset($_REQUEST['adminId']))	{ print "<div class='alert alert-info alert-dash'><i class='icon-gray icon-chevron-left'></i> "._('Please select setting from left menu!')."</div>"; }    	
				else if ($_REQUEST['page'] == "administration")										{ include_once("site/admin/$_REQUEST[adminId].php"); }    	
			print "</div>";
    	}
    	?>
      	
    </div>
</div>
</div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- Page footer -->
<div class="footer"><?php include('site/footer.php'); ?></div>

<!-- export div -->
<div class="exportDIV"></div>

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>