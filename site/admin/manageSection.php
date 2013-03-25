<?php

/**
 * Script to manage sections
 *************************************************/


/* verify that user is admin */
checkAdmin();


$sections = fetchSections ();
?>

<h4>Section management</h4>
<hr>

<!-- Add new section -->
<button class='btn btn-small editSection' data-action='add'   data-sectionid='' style='margin-bottom:10px;'><i class='icon-gray icon-plus'></i> Add new</button>


<!-- show sections -->
<?php if(sizeof($sections) > 0) { ?>
<table id="manageSection" class="table table-striped table-condensed table-top">
<!-- headers -->
<tr>
    <th>Name</th>
    <th>Description</th>
    <th>Strict mode</th>
    <th>Group Permissions</th>
    <th></th>
</tr>

<!-- existing sections -->
<?php
foreach ($sections as $section)
{
	print '<tr>'. "\n";
    print '	<td>'. str_replace("_", " ", $section['name']).'</td>'. "\n";
    print '	<td>'. $section['description'] .'</td>'. "\n";

    # strictMode
    if($section['strictMode'] == 0)	{ $mode = "no"; }
    else							{ $mode = "yes"; }
  
    print '	<td>'. $mode .'</td>'. "\n";

    # permissions
	print "<td>";    
    if(strlen($section['permissions'])>1) {
    	$permissions = parseSectionPermissions($section['permissions']);
    	# print for each if they exist
    	if(sizeof($permissions) > 0) {
    		foreach($permissions as $key=>$p) {
	    		# get subnet name
	    		$group = getGroupById($key);
	    		# parse permissions
	    		$perm   = parsePermissions($p);
	    	
	    		print $group['g_name']." : ".$perm."<br>"; 
	    	}   
    	}
    	else {
	    	print "All groups: No access";
    	}
    }
	print "</td>";
    
   	print '	<td class="actions">'. "\n";
   	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small editSection' data-action='edit'   data-sectionid='$section[id]'><i class='icon-gray icon-pencil'></i></button>";
	print "		<button class='btn btn-small editSection' data-action='delete' data-sectionid='$section[id]'><i class='icon-gray icon-remove'></i></button>";
	print "	</div>";
	print '	</td>'. "\n";
    
	print '</tr>'. "\n";;
}
?>

</table>	<!-- end table -->

<!-- show no configured -->
<?php } else { ?>
<div class="alert alert-warn alert-absolute">No sections configured!</div>
<?php } ?>


<!-- permissions info -->
<div class="alert alert-info alert-absolute" style="margin-top:15px;">
Permissions info<hr>
<ul>
	<li>If group is not set in permissions then it will not have access to subnet</li>
	<li>Groups with RO permissions will not be able to create new subnets</li>
	<li>Subnet permissions must be set separately. By default if group has access to section<br>it will have same permission on subnets</li>
	<li>You can choose to delegate section permissions to all underlying subnets</li>
	<li>If group does not have access to section it will not be able to access subnet, even if<br>subnet permissions are set</li>
</ul>
</div>