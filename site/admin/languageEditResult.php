<?php

/**
 * Script to display language edit
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify that description is present if action != delete */
if($_POST['action'] != "delete" && strlen($_POST['l_code']) < 2)	{ die("<div class='alert alert-error'>">_('Code must be at least 2 characters long')."!</div>"); }
if($_POST['action'] != "delete" && strlen($_POST['l_name']) < 2)	{ die("<div class='alert alert-error'>">_('Name must be at least 2 characters long')."!</div>"); }

/* try to execute */
if(!modifyLang($_POST)) { print "<div class='alert alert-error'  >"._("Language $_POST[action] error")."!</div>"; }
else 					{ print "<div class='alert alert-success'>"._("Language $_POST[action] success")."!</div>"; }

?>