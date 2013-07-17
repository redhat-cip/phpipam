<?php

/**
 *	Script to export IP database to excel file!
 **********************************************/


/* verify that user is admin */
checkAdmin();

?>

<h4><?php print _('phpIPAM database export'); ?></h4>
<hr><br>

<div class="alert alert-info alert-absolute"><?php print _('You can download MySQL dump of database or generate XLS file of IP addresses'); ?>!</div>

<!-- MySQL dump -->
<hr style="margin-top:50px;">
<h4><?php print _('Create MySQL database dump'); ?></h4>
<button class="btn btn-small" id="MySQLdump"><i class="icon-gray icon-download"></i> <?php print _('Prepare MySQL dump'); ?></button>

<!-- XLS dump -->
<h4><?php print _('Create XLS file of IP addresses'); ?></h4>
<button class="btn btn-small" id="XLSdump"><i class="icon-gray icon-download"></i> <?php print _('Prepare XLS dump'); ?></button>

<!-- XLS dump -->
<h4><?php print _('Create hostfile dump'); ?></h4>
<button class="btn btn-small" id="hostfileDump"><i class="icon-gray icon-download"></i> <?php print _('Prepare hostfile dump'); ?></button>