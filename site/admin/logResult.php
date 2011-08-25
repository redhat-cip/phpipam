<table class="normalTable logs">

<?php

/**
 * Script to print selected logs
 **********************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* if nothing is provided display all! */
if ( empty($_POST['Informational']) && empty($_POST['Notice']) && empty($_POST['Warning']) ) {
    $_POST['Informational'] = "Informational";
    $_POST['Notice']        = "Notice";
    $_POST['Warning']       = "Warning";
}
?>

<!-- print headers -->
<tr class="th">
    <th class="id">Id</th>
    <th>Severity</th>
    <th>Username</th>
    <th colspan="2">Event</th>
    <th class="date">Date</th>
</tr>

<!-- print logs -->
<?php
/* count all logs */
$numberOfLogs = countAllLogs();

/* fetch 25 at once logs */
$logCount = 20;

/* set classes based on severity */   
if ($_POST['Informational'] == "Informational") {
	$_POST['InformationalQuery'] = "0";
} else {
	$_POST['InformationalQuery'] = "10";
}
if ($_POST['Notice'] == "Notice") {
	$_POST['NoticeQuery'] = "1";
}
 else {
	$_POST['NoticeQuery'] = "10";
}
if ($_POST['Warning'] == "Warning") {
	$_POST['WarningQuery'] = "2";
}
 else {
	$_POST['WarningQuery'] = "10";
}

/* get highest lastId */
$highestId = getHighestLogId();

if(empty($_POST['lastId']) || ($_POST['lastId'] == "undefined")) {
	$_POST['lastId'] = $highestId;
}

/* set empty durection */
if(!isset($_POST['direction'])) {
	$_POST['direction'] = "";
}

/* get requested logs */
$logs = getAllLogs($logCount, $_POST['direction'], $_POST['lastId'], $highestId, $_POST['InformationalQuery'], $_POST['NoticeQuery'], $_POST['WarningQuery']);

$x = 0;
foreach ($logs as $log)
{
	if($x < $logCount) {

	    /* set classes based on severity */   
	    if ($log['severity'] == 0) {
	        $log['severityText'] = "Informational";
	    }
	    else if ($log['severity'] == 1) {
	        $log['severityText'] = "Notice";
	    }
	    else {
	        $log['severityText'] = "Warning";
	    }
    
    	if (in_array($log['severityText'], $_POST)) {
    	
    		/* format date */
/*     		$log['date'] = date("Y/m/d H:i:s", mktime($log['date'])); */
    
   	    	print '<tr class="'. $log['severityText'] .'" id="'. $log['id'] .'">'. "\n";
   	    	print '	<td class="id">'. $log['id'] .'</td>'. "\n";
   	    	print '	<td class="severity"><span>'. $log['severity'] .'</span>'. $log['severityText'] .'</td>'. "\n";
        	print '	<td class="username">'. $log['username'] .'</td>'. "\n";
            print '	<td class="command">'. $log['command']  .'</td>'. "\n";
            
            /* details */
            if(!empty($log['details'])) {
            print '	<td class="detailed"><img src="css/images/infoIP.png" title="<b>Event details</b>:<hr>'. $log['details'] .'"></td>'. "\n";            
            }
            else {
            print '	<td class="detailed"></td>'. "\n";
        	}
        	
        	print '	<td class="date">'. $log['date']     .'</td>'. "\n";
        	print '</tr>'. "\n";
    	}    	
	}
	$x++;
}

?>

</table>	<!-- end filter table -->