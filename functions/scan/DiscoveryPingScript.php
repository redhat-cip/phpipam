<?php

// include required scripts
require_once( dirname(__FILE__) . '/../functions.php' );
require_once( dirname(__FILE__) . '/../scripts/Thread.php');

// no error reporting!
ini_set('display_errors', 0);

// test to see if threading is available
if( !Thread::available() ) 	{ 
	$error[] = "Threading is required for scanning subnets. Please recompile PHP with pcntl extension";
	$error   = json_decode($error);
	die($error); 
}

$count = 1;						// number of pings

// set result arrays
$alive = array();				// alive hosts
$dead  = array();				// dead hosts

// get all IP addresses to be scanned from $argv cmd line
$addresses = explode(";",$argv[1]);

// get size of addresses to ping
$size = sizeof($addresses);

$z = 0;			//addresses array index


// run per MAX_THREADS
for ($m=0; $m<=$size; $m += $MAX_THREADS) {
    // create threads 
    $threads = array();
    
    // fork processes
    for ($i = 0; $i <= $MAX_THREADS && $i <= $size; $i++) {
    	//only if index exists!
    	if(isset($addresses[$z])) {      	
			//start new thread
            $threads[$z] = new Thread( 'pingHost' );
            $threads[$z]->start( Transform2long($addresses[$z]), $count, true );
            $z++;				//next index
		}
    }

    // wait for all the threads to finish 
    while( !empty( $threads ) ) {
        foreach( $threads as $index => $thread ) {
            if( ! $thread->isAlive() ) {
            	//get exit code
            	$exitCode = $thread->getExitCode();
            	//online, save to array
            	if($exitCode == 0) {
            		$alive[] = $addresses[$index];
            	}
            	else {
	            	$dead[]  = $addresses[$index];
            	}
                //remove thread
                unset( $threads[$index] );
            }
        }
        usleep(500);
    }

}

# save to json
$alive = json_encode($alive);

# print result
print_r($alive);
?>