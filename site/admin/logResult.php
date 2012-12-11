<table id="logs" class="table table-condensed table-hover table-top">

<?php

/**
 * Script to print selected logs
 **********************************/
 
/* required functions */
if(!function_exists('countAllLogs')) {
require_once('../../functions/functions.php'); 
}

/* if nothing is provided display all! */
if ( empty($_POST['Informational']) && empty($_POST['Notice']) && empty($_POST['Warning']) ) {
    $_POST['Informational'] = "Informational";
    $_POST['Notice']        = "Notice";
    $_POST['Warning']       = "Warning";
}
?>

<!-- print headers -->
<tr>
    <th class="date" style="width:130px;white-space:nowrap">Date</th>
    <th>Severity</th>
    <th>Username</th>
    <th>IP address</th>
    <th colspan="2">Event</th>
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
	        $color = "success";
	    }
	    else if ($log['severity'] == 1) {
	        $log['severityText'] = "Notice";
	        $color = "warning";
	    }
	    else {
	        $log['severityText'] = "Warning";
	        $color = "error";
	    }
    
    	if (in_array($log['severityText'], $_POST)) {
    	
    		/* format date */
/*     		$log['date'] = date("Y/m/d H:i:s", mktime($log['date'])); */
    
   	    	print '<tr class="'.$color.' '. $log['severityText'] .'" id="'. $log['id'] .'">'. "\n";
         	print '	<td class="date">'. $log['date']     .'</td>'. "\n";
   	    	print '	<td class="severity"><span>'. $log['severity'] .'</span>'. $log['severityText'] .'</td>'. "\n";
        	print '	<td class="username">'. $log['username'] .'</td>'. "\n";
        	print '	<td class="ipaddr">'. $log['ipaddr'] .'</td>'. "\n";
            print '	<td class="command"><a href="" class="openLogDetail" data-logid="'.$log['id'].'">'. $log['command']  .'</a></td>'. "\n";
            print '	<td class="detailed">';
            /* details */
            if(!empty($log['details'])) { print '	<i class="icon-comment icon-gray" rel="tooltip" data-html="true" title="<b>Event details</b>:<hr>'. $log['details'] .'"></i></td>'. "\n"; }
            print '	</td>'. "\n";
        	print '</tr>'. "\n";
    	}    	
	}
	$x++;
}
?>

</table>	<!-- end filter table -->

<?php
if(sizeof($logs)== 0) {
	print "<div class='alert alert-info'>No logs available!</div>";
}
?>