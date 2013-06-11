<?php

/**
 * Script to display usermod result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify that description is present if action != delete */
if($_POST['action'] != "delete" && strlen($_POST['g_name']) < 2)	{ die("<div class='alert alert-error'>"._('Name must be at least 2 characters long')."!</div>"); }

/* remove users from this group if delete */
if($_POST['action'] == "delete") { deleteUsersFromGroup($_POST['g_id']); }

/* remove group from sections if delete */
if($_POST['action'] == "delete") { deleteGroupFromSections($_POST['g_id']); }

/* try to execute */
if(!modifyGroup($_POST)) { print "<div class='alert alert-error'  >"._("Group $_POST[action] error")."!</div>"; }
else 					 { print "<div class='alert alert-success'>"._("Group $_POST[action] success")."!</div>"; }

?>