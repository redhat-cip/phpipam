<?php

/**
 *
 *	Config file for network scanning paths etc
 *
 */

//general configs
$scanMaxHosts 	= 32;				//maximum number of scans per once
$scanDNSresolve = false;			//try to resolve DNS name

// ping path
$pathPing = "/sbin/ping";

// nmap path
$pathNmap = "/usr/local/bin/nmap";


?>