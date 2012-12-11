<?php

require( dirname(__FILE__) . '/../../config.php' );
require( dirname(__FILE__) . '/../dbfunctions.php' );
require( dirname(__FILE__) . '/../functions-network.php' );

require_once( 'Thread.php' );

// test to see if threading is available
if( !Thread::available() ) {
    die( 'Threads not supported'."\n" );
}

$MAX_THREADS = 256;

function pingHost ($hostAddress)
{
    exec("ping -c 3 -W 1 -n $hostAddress 1>/dev/null 2>&1", $output, $retval);
    exit($retval);
}

function getTypeOfAddress($ip_addr) {
    if ( IdentifyAddress( $ip_addr ) == "IPv4") {
        $type = 0;
    }
    else {
        $type = 1;
    }
}

function scanSubnetById ($subnetId) {
    global $db;
    global $MAX_THREADS;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* get subnet */
    $query      = 'SELECT * from subnets where id = "'. $subnetId .'";';
    $subnets = $database->getArray($query);

    if (empty($subnets)) {
        print "The subnet does not exist!\n";
        return false;
    }

    /* the first record is the result subnet */
    $subnet = $subnets[0];

    $size = MaxHosts( $subnet["mask"], getTypeOfAddress($subnet["subnet"]) );
    $lastAddress = $subnet["subnet"] + $size;

    /* an array to store the statuses of IP addresses */
    $statuses = array();

    for ($ip_addr = $subnet["subnet"]+1; $ip_addr < $lastAddress; $ip_addr += $MAX_THREADS) {

        /* create threads */
        $threads = array();
        for ($i = 0; $i < $MAX_THREADS && $ip_addr+$i <= $lastAddress; $i++) {
            $threads[$ip_addr+$i] = new Thread( 'pingHost' );
            $threads[$ip_addr+$i]->start( Transform2long($ip_addr+$i) );
        }

        /* wait for all the threads to finish */
        while( !empty( $threads ) ) {
            foreach( $threads as $index => $thread ) {
                if( ! $thread->isAlive() ) {
                    $statuses[$index] = $thread->getExitCode();
                    unset( $threads[$index] );
                }
            }
            sleep(1);
        }
    }

    ksort($statuses);

    foreach ($statuses as $ip_addr => $status) {
        if ( $status == 0 ) {
            print Transform2long($ip_addr) . "\t1\n";
        } else {
            print Transform2long($ip_addr) . "\t0\n";
        }
    }
}

scanSubnetById(2);

?>
