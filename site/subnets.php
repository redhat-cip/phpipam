<?php

/**
 * Script to print subnets from selected section
 *************************************************/
 
/* include functions */
/* if(!function_exists(CheckReferer)) { require_once(dirname(__FILE__) . '../functions/functions.php'); } */

/* verify that user is authenticated! */
isUserAuthenticated ();


/* get requested section and format it to nice output */
$sectionId = $_REQUEST['section'];

/* if it is not numeric than get ID from provided name */
if ( (!is_numeric($sectionId)) && ($sectionId != "Administration") ) {
    $sectionId = getSectionIdFromSectionName ($sectionId);
}

/**
 * Admin check, otherwise load requested subnets
 */
if ($sectionId == 'Administration')
{
    /* Print all Admin actions af user is admin :) */
    if (!checkAdmin()) {
        print '<div class="alert alert-error">Sorry, must be admin!</div>';
    }
    else {
        include('admin/adminMenu.php');
    }
}
else 
{    
    /* get section name */
    $sectionName = getSectionDetailsById ($sectionId);
    
    /* die if empty! */
    if(sizeof($sectionName) == 0) {
    	print_r($_REQUEST);
		die('<div class="alert alert-error">Section does not exist!</div>');
	}


    # header
    if(isset($_COOKIE['expandfolders'])) {
	    if($_COOKIE['expandfolders'] == "1")	{ $iconClass='icon-resize-small'; $action = 'open';}
	    else									{ $iconClass='icon-resize-full';  $action = 'close'; }
    }
    else 										{ $iconClass='icon-resize-full';  $action = 'close';}
    
    print "<h4>Available subnets <span class='pull-right' style='margin-right:5px;cursor:pointer;'><i class='icon-gray $iconClass' rel='tooltip' data-placement='bottom' title='Expand/compress all folders' id='expandfolders' data-action='$action'></i></span></h4>";	
    print "<hr>";
	
	/* print subnets table ---------- */
	print "<div class='subnets'>";
	
	# print links
	$subnets2 = fetchSubnets ($sectionId);
	$menu = get_menu_html( $subnets2 );
	print $menu;
	
	print "</div>";						# end subnets overlay
}

# add new subnet
if(checkAdmin(false, false)) {
	print "<div class='action'>";
	if(isset($_REQUEST['subnetId'])) {
	print "	<button class='btn btn-mini pull-left' id='hideSubnets' rel='tooltip' title='Hide subnet list' data-placement='right'><i class='icon-gray icon-chevron-left'></i></button>";
	}
	print "	<span>Add new subnet <button id='add_subnet' class='btn btn-small btn-success' style='margin-left:5px;' rel='tooltip' data-placement='top' title='Add new subnet to $sectionName[name]'  data-subnetId='' data-sectionId='$sectionName[id]' data-action='add'><i class='icon-plus icon-white'></i></button></span>";
	print "</div>";
} 