<?php

/**
 *
 *	Config file for network scanning paths etc
 *
 */

//general configs
$scanMaxHosts 	= 32;				// maximum number of scans per once
$scanDNSresolve = true;				// try to resolve DNS name
$scanIPv6 		= false;			// not yet

//configs
$MAX_THREADS = 256;					// set max concurrent threads

// ping path
$pathPing = "/bin/ping";

// nmap path
$pathNmap = "/usr/local/bin/nmap";


?>
