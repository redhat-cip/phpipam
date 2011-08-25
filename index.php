<?php
session_start();
ob_start();



/* site config */
require('config.php');

/* site functions */
require('functions/functions.php');

/* get all site settings */
$settings = getAllSettings();

/* site header */
include('site/head.php');

/* verify login and permissions */
isUserAuthenticatedNoAjax();

/* check if database needs upgrade */
include('functions/dbUpgradeCheck.php');

/* check for support for PHP modules and database connection  */
$locationPrefix = "";		//prefix for ccs in case of login page checkPhpBuild.php fails
include('functions/checkPhpBuild.php');

?>


<!-- body -->
<body>

<!-- wrapper -->
<div class="wrapper">

<!-- jQuery error -->
<div class="jqueryError"><br><br><br><br><br><br><br>jQuery error!</div>

<!-- loader -->
<div class="loading">Loading...<br><img src="css/images/ajax-loader.gif"></div>

<!-- page header -->
<div class="header"><a href=""><?php print $settings['siteTitle']; ?></a></div>

<!-- page user menu -->
<div class="user_menu"><?php include('site/userMenu.php');?></div>

<!-- page sections -->
<div class="sections_overlay">
<div class="sections">
    <?php include('site/sections.php');?>
</div>
</div>

<!-- content table -->
<div class="content_overlay">
<table class="content">
	<tr>
		<td id="subnets"><div class="subnets normalTable"></div></td>
		<td id="content"><div class="content"></div></td>
	</tr>
</table>
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