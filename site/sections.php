<?php

/**
 * Script to print sections and admin link on top of page
 ********************************************************/

/* use scripts, but only if requested through post! */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../functions/functions.php');
    
}

/* verify that user is authenticated! */
isUserAuthenticated ();

/* fetch result */
$sections = fetchSections ();


/* print */
print '<table class="newSections">'. "\n";
print '<tr>' . "\n";

/* sections */
print '<td id="sections">'. "";

/* Print sections */
print '<ul name="sections" id="sections">'. "\n";

foreach($sections as $section) {
	print '<li section="'. $section['name'] .'" id="'. $section['id'] .'" title="Show all subnets in '. $section['name'] .' section">'. $section['name'] .'</li>' . "\n";
}

print '</ul>';
print '</td>'. "";

/* info */
print '<td section="tools" id="instructions" title="Show IP addressing Guide"><img src="css/images/info.png" style="width:20px;"></td>'. "\n";

print '</td>'. "";

/* admin */
if(checkAdmin(false)) {
	print '<td section="Administration" id="Administration">Administration</td>'. "\n";
}
else {
	print '</td>'. "\n";
}
print '</td>'. "";

/* end table */
print '</tr>'. "\n";
print '</table>' ."\n";


/* admin menu dropdown */
print '<div class="adminMenuDropdown">'. "\n";


/* show IP request link if enabled in config file!  */
if($settings['enableIPrequests'] == 1) {    
    $requestNum = countRequestedIPaddresses();
    if($requestNum != 0) {
		print '<dd section="Administration|manageRequests" id="manageRequests">IP requests ('. $requestNum . ')</dd>' . "\n";
	}
}

/* Admin hover menu */
print '<dd section="Administration" id="settings">Server management</dd>';
print '<dd section="Administration" id="manageSection">Sections</dd>';
print '<dd section="Administration" id="manageSubnet">Subnets</dd>';
print '<dd section="Administration" id="manageSwitches">Switches</dd>';
print '<dd section="Administration" id="userMod">Users</dd>';
print '<dd section="Administration" id="log">Log files</dd>';
print '<dd section="Administration" id="all">Show all settings</dd>';

print '</div>'. "\n";

?>