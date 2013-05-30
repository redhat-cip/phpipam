<?php

/*
 * Script to check for new version!
 *************************************************/

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();
/* get latest version */
if(!$version = getLatestPHPIPAMversion()) { die('<div class="alert alert-error">'._('Version check failed').'!</div>'); }

print "<h4>phpIPAM version check</h4><hr>";

/* print result */
if($settings['version'] == $version) {
	print '<div class="alert alert-success alert-absolute">'._('Latest version').' ('. $settings['version'] .') '._('already installed').'!</div>';
}
else if ($settings['version'] > $version) {
	print '<div class="alert alert-success alert-absolute">'._('Development version').' ('. $settings['version'] .') '._('installed! Latest production version is').' '. $version .'.</div>';
}
else {
	print '<div class="alert alert-error alert-absolute"><b>'._('New version of phpipam available').':</b><br>';
	print _('Installed version').': '. $settings['version'] . "<br>";
	print _('Available version').': '. $version ."<br><br>\n";
	print _('You can download new version').' <a href="https://sourceforge.net/projects/phpipam/files/current/phpipam-'. $version .'.tar/download">'._('here').'</a>.';
}

?>