<?php

// include required scripts
require_once( dirname(__FILE__) . '/../functions.php' );
require_once( dirname(__FILE__) . '/Thread.php');

/*
set cronjob:
# update host statuses exery 15 minutes
*\/15 * * * *  /usr/local/bin/php /<sitepath>/functions/scripts/pingCheck.php
*/

//configs
$MAX_THREADS = 128;						//set max concurrent threads
$email = true;							//set mail with status diff to admins
$emailText = false;						//format to send mail via text or html
//$wait = 500;							//time to wait for response in ms
$count = 1;								//number of pings to send
//response
$stateDiff = array();					//Array with differences, can be used to email to admins

// test to see if threading is available
if( !Thread::available() ) 	{ $threads = false; }		//pcntl php extension required
else						{ $threads = true; }


//get all IP addresses to be scanned
$addresses = getAllIPsforScan(true);

//get settings
$settings = getAllSettings();
// set ping statuses
$statuses = explode(";", $settings['pingStatus']);



//threads not supported, scan 1 by one - it is highly recommended to enable threading for php
if(!$threads) {
	//print warning
	print "Warning: Threading is not supported!\n";
	$m=0;														//Array count
	//scan each
	foreach($addresses as $ip) {
		//calculate diff since last alive
		$tDiff = time() - strtotime($ip['lastSeen']);
		//set Old status
		if($tDiff < $statuses[1])	{ $addresses[$m]['oldStatus'] = 0; }	//old online
		else						{ $addresses[$m]['oldStatus'] = 2; }	//old offline
		//get status
		$code = pingHost (transform2long($ip['ip_addr']), $count, false);
		//Online
		if($code == "0") {
			//update IP status
			@updateLastSeen($ip['id']);
			//set new seen
			$addresses[$m]['newSeen'] = date("Y-m-d H:i:s");
		} else {
			$code = 2;
		}
		//save new status
		$addresses[$m]['newStatus'] = $code;
		//check for status change
		if($addresses[$m]['oldStatus'] != $code) {
			$stateDiff[] = $addresses[$m];					//save to change array
		}
		//save exit code
		$addresses[$m]['newStatus'] = $code;
		//next
		$m++;
	}
}
//threaded
else {
	//get size of addresses to ping
	$size = sizeof($addresses);
	
	$z = 0;			//addresses array index

	//run per MAX_THREADS
    for ($m=0; $m<=$size; $m += $MAX_THREADS) {
        // create threads 
        $threads = array();
        
        //fork processes
        for ($i = 0; $i <= $MAX_THREADS && $i <= $size; $i++) {
        	//only if index exists!
        	if(isset($addresses[$z])) {
        		//calculate diff since last alive
				$tDiff = time() - strtotime($addresses[$z]['lastSeen']);
				//set Old status
				if($tDiff <= $statuses[1])	{ $addresses[$z]['oldStatus'] = 0; }	//old online
				else						{ $addresses[$z]['oldStatus'] = 2; }	//old offline        	

				//start new thread
	            $threads[$z] = new Thread( 'pingHost' );
	            $threads[$z]->start( Transform2long($addresses[$z]['ip_addr']), $count, true );
	            $z++;				//next index
			}
        }

        // wait for all the threads to finish 
        while( !empty( $threads ) ) {
            foreach( $threads as $index => $thread ) {
                if( ! $thread->isAlive() ) {
                	//get exit code
                	$exitCode = $thread->getExitCode();
                	//online, check diff
                	if($exitCode == "0") {
	                	//update IP status
						@updateLastSeen($addresses[$index]['id']);
						//set new seen
						$addresses[$index]['newSeen'] = date("Y-m-d H:i:s");	
                	} else {
						$exitCode = 2;
					}
					//check for status change
					if($addresses[$index]['oldStatus'] != $exitCode) {
						$stateDiff[] = $addresses[$index];					//save to change array
					}
                	//save exit code for host
                    $addresses[$index]['newStatus'] = $exitCode;
                    //remove thread
                    unset( $threads[$index] );
                }
            }
            usleep(500);
        }

	}
}

//all done, mail diff?
if(sizeof($stateDiff)>0 && $email)
{
	//send text array, cron will do that by default if you don't redirect output > /dev/null 2>&1
	if($emailText) {
		print_r($stateDiff);		
	}
	//html
	else {
	
		$mail['from']		= "$settings[siteTitle] <ipam@$settings[siteDomain]>";
		$mail['headers']	= 'From: ' . $mail['from'] . "\r\n";
		$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
		$mail['headers']   .= 'X-Mailer: PHP/' . phpversion() ."\r\n";
		
		//subject
		$mail['subject'] 	= "phpIPAM IP state change ".date("Y-m-d H:i:s");
	
		//header
		$html[] = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>";
		$html[] = "<html>";
		$html[] = "<head></head>";
		$html[] = "<body>";
		//title
		$html[] = "<h3>phpIPAM host changes</h3>";
		//table
		$html[] = "<table style='margin-left:10px;margin-top:5px;width:auto;padding:0px;border-collapse:collapse;border:1px solid gray;'>";
		$html[] = "<tr>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>id</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Subnet</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Section</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>IP</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>last seen</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>old status</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>new status</th>";

		$html[] = "</tr>";
		//Changes
		foreach($stateDiff as $change) {
			//reformat statuses
			if($change['oldStatus'] == 0)	{ $oldStatus = "Online"; }
			else							{ $oldStatus = "Offline"; }
			if($change['newStatus'] == 0)	{ $newStatus = "Online"; }
			else							{ $newStatus = "Offline"; }
			//set subnet
			$subnet = getSubnetDetails($change['subnetId']);
			$subnetPrint = Transform2long($subnet['subnet'])."/".$subnet['mask']." - ".$subnet['description'];
			//set section
			$section = getSectionDetailsById($subnet['sectionId']);
			$sectionPrint = $section['name']." (".$section['description'].")";
			//ago
			if(is_null($change['lastSeen']) || $change['lastSeen']=="0000-00-00 00:00:00") {
				$ago	  = "never";
			} else {
				$timeDiff = time() - strtotime($change['lastSeen']);
				$ago 	  = $change['lastSeen']." (".sec2hms($timeDiff)." ago)";
			}
			
			$html[] = "<tr>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$change[id]</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$subnetPrint</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$sectionPrint</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>".Transform2long($change['ip_addr'])."</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$ago</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$oldStatus</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$newStatus</td>";

			$html[] = "</tr>";
		}
		$html[] = "</table>";
		//footer
		
		//end
		$html[] = "</body>";
		$html[] = "</html>";
		
		//save to 
		$mail['content'] = implode("\n", $html);

		//send
		mail($settings['siteAdminMail'], $mail['subject'], $mail['content'], $mail['headers']);
	}
}

?>