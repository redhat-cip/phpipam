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
    $_POST['Informational'] = _("Informational");
    $_POST['Notice']        = _("Notice");
    $_POST['Warning']       = _("Warning");
}
?>

<!-- print headers -->
<tr>
    <th class="date" style="width:130px;white-space:nowrap"><?php print _('Date'); ?></th>
    <th><?php print _('Severity'); ?></th>
    <th><?php print _('Username'); ?></th>
    <th><?php print _('IP address'); ?></th>
    <th colspan="2"><?php print _('Event'); ?></th>
</tr>

<!-- print logs -->
<?php
/* count all logs */
$numberOfLogs = countAllLogs();

/* fetch 25 at once logs */
$logCount = 20;

/* set classes based on severity */   
if ($_POST['Informational'] == _("Informational")) {
	$_POST['InformationalQuery'] = "0";
} else {
	$_POST['InformationalQuery'] = "10";
}
if ($_POST['Notice'] == _("Notice")) {
	$_POST['NoticeQuery'] = "1";
}
 else {
	$_POST['NoticeQuery'] = "10";
}
if ($_POST['Warning'] == _("Warning")) {
	$_POST['WarningQuery'] = "2";
}
 else {
	$_POST['WarningQuery'] = "10";
}

/* get highest lastId */
$highestId = getHighestLogId();

if(empty($_POST['lastId']) || ($_POST['lastId'] == "undefined")) 	{ $_POST['lastId'] = $highestId; }

/* set empty durection */
if(!isset($_POST['direction'])) 									{ $_POST['direction'] = ""; }

/* get requested logs */
$logs = getAllLogs($logCount, $_POST['direction'], $_POST['lastId'], $highestId, $_POST['InformationalQuery'], $_POST['NoticeQuery'], $_POST['WarningQuery']);

$x = 0;
foreach ($logs as $log)
{
	if($x < $logCount) {

	    /* set classes based on severity */   
	    if ($log['severity'] == 0) {
	        $log['severityText'] = _("Informational");
	        $color = "success";
	    }
	    else if ($log['severity'] == 1) {
	        $log['severityText'] = _("Notice");
	        $color = "warning";
	    }
	    else {
	        $log['severityText'] = _("Warning");
	        $color = "error";
	    }
    
    	if (in_array($log['severityText'], $_POST)) {
    	
    		/* format date */
/*     		$log['date'] = date("Y/m/d H:i:s", mktime($log['date'])); */

			/* reformat permissions */
			$log['details'] = str_replace("\"", "'", $log['details']);
    
   	    	print '<tr class="'.$color.' '. $log['severityText'] .'" id="'. $log['id'] .'">'. "\n";
         	print '	<td class="date">'. $log['date']     .'</td>'. "\n";
   	    	print '	<td class="severity"><span>'. $log['severity'] .'</span>'. $log['severityText'] .'</td>'. "\n";
        	print '	<td class="username">'. $log['username'] .'</td>'. "\n";
        	print '	<td class="ipaddr">'. $log['ipaddr'] .'</td>'. "\n";
            print '	<td class="command"><a href="" class="openLogDetail" data-logid="'.$log['id'].'">'. $log['command']  .'</a></td>'. "\n";
            print '	<td class="detailed">';
            /* details */
            if(!empty($log['details'])) { print '	<i class="icon-comment icon-gray" rel="tooltip" data-html="true" title="<b>'._('Event details').'</b>:<hr>'. $log['details'] .'"></i></td>'. "\n"; }
            print '	</td>'. "\n";
        	print '</tr>'. "\n";
    	}    	
	}
	$x++;
}
?>

</table>	<!-- end filter table -->

<?php
if(sizeof($logs)== 0) { print "<div class='alert alert-info'>"._('No logs available')."!</div>"; }
?>