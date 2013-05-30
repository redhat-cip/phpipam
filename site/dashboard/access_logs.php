<?php

/* print last 5 access logs */
$logs = getAllLogs(5, NULL, NULL, NULL, 0, 0,0);

print "<table class='table table-striped table-condensed table-hover table-top'>";

# headers
print "<tr>";
print "	<th>"._('Severity')."</th>";
print "	<th>"._('Command')."</th>";
print "	<th>"._('Date')."</th>";
print "	<th>"._('Username')."</th>";
print "</tr>";

# logs
foreach($logs as $log) {
	# reformat severity
	if($log['severity'] == 0)		{ $log['severityText'] = _("Info"); }
	else if($log['severity'] == 1)	{ $log['severityText'] = _("Warn"); }
	else if($log['severity'] == 2)	{ $log['severityText'] = _("Err"); }
	
	print "<tr>";
	print "	<td><span class='severity$log[severity]'>$log[severityText]</span></td>";
	print "	<td>$log[command]</td>";
	print "	<td>$log[date]</td>";
	print "	<td>$log[username]</td>";

	print "</tr>";
}

print "</table>";

# print if none
if(sizeof($logs) == 0) {
	print "<div class='alert alert-info'>"._('No logs available')."</div>";
}
?>