<?php

/**
 * Sample API php client application
 *
 * In this example we will create new section
 *
 *	http://phpipam/api/client/createNewSection.php?name=secton1&description=test&strictMode=1
 */

# config
include_once('apiConfig.php');

# API caller class
include_once('apiClient.php');

# commands
$req['controller'] 	= "sections";
$req['action']		= "create";

# set parameters
if(!isset($_REQUEST['name'])) 			{ die("Please provide section name"); }
else 									{ $req['name'] 			= $_REQUEST['name']; }
# mandatory
if(isset($_REQUEST['strictMode'])) 		{ $req['strictMode']	= $_REQUEST['strictMode']; }
if(isset($_REQUEST['permissions'])) 	{ $req['permissions']	= $_REQUEST['permissions']; }
if(isset($_REQUEST['description'])) 	{ $req['description']	= $_REQUEST['description']; }
if(isset($_REQUEST['order'])) 			{ $req['order']			= $_REQUEST['order']; }
if(isset($_REQUEST['subnetOrdering'])) 	{ $req['subnetOrdering']= $_REQUEST['subnetOrdering']; }

# wrap in try to catch exceptions
try {
	# initialize API caller
	$apicaller = new ApiCaller($app['id'], $app['enc'], $url);
	# send request
	$response = $apicaller->sendRequest($req);

	print "<pre>";
	print_r($response);
}
catch( Exception $e ) {
	//catch any exceptions and report the problem
	print "Error: ".$e->getMessage();
}

?>