<?php

/*
 * Script to check for new version!
 *************************************************/

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();
/* get latest version */
if(!$version = getLatestPHPIPAMversion()) { die('<div class="alert alert-error">Version check failed!</div>'); }

print "<h4>phpIPAM version check</h4><hr>";

/* print result */
if($settings['version'] == $version) {
	print '<div class="alert alert-success alert-absolute">Latest version ('. $settings['version'] .') already installed!</div>';
}
else if ($settings['version'] > $version) {
	print '<div class="alert alert-success alert-absolute">Development version ('. $settings['version'] .') installed! Latest production version is '. $version .'.</div>';
}
else {
	print '<div class="alert alert-error alert-absolute"><b>New version of phpipam available:</b><br>';
	print 'Installed version: '. $settings['version'] . "<br>";
	print 'Available version: '. $version ."<br><br>\n";
	print 'You can download new version <a href="https://sourceforge.net/projects/phpipam/files/current/phpipam-'. $version .'.tar/download">here</a>.';
}

?>