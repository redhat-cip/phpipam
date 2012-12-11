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
<table id="manageSection" class="table table-striped table-condensed table-top table-hover">
<!-- headers -->
<tr>
    <th>Name</th>
    <th>Description</th>
    <th></th>
</tr>

<!-- existing sections -->
<?php
foreach ($sections as $section)
{
	print '<tr>'. "\n";
    print '	<td>'. str_replace("_", " ", $section['name']).'</td>'. "\n";
    print '	<td>'. $section['description'] .'</td>'. "\n";
    
   	print '	<td class="actions">'. "\n";
	print "		<button class='btn btn-small editSection' data-action='edit'   data-sectionid='$section[id]'><i class='icon-gray icon-edit'></i> Edit</button>";
	print "		<button class='btn btn-small editSection' data-action='delete' data-sectionid='$section[id]'><i class='icon-gray icon-remove'></i> Delete</button>";
	print '	</td>'. "\n";
    
	print '</tr>'. "\n";;
}
?>

</table>	<!-- end table -->

<!-- show no configured -->
<?php } else { ?>
<div class="alert alert-warn alert-absolute">No sections configured!</div>

<?php } ?>