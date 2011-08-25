<?php

/**
 * Script to manage sections
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* print all sections with delete / edit button */
print '<h3>Section management</h3>';

$sections = fetchSections ();

?>

<!-- show sections -->
<div class="manageSection normalTable">
<table class="manageSection normalTable">

<!-- headers -->
<tr class="th">
    <th>Name</th>
    <th>Description</th>
    <th colspan=2></th>
</tr>

<!-- existing sections -->
<?php
foreach ($sections as $section)
{
	print '<tr>'. "\n";
    print '	<td>'. str_replace("_", " ", $section['name']).'</td>'. "\n";
    print '	<td>'. $section['description'] .'</td>'. "\n";
    print '	<td class="edit"><img src="css/images/edit.png"   class="sectionEdit"   id="'. $section['id'] .'" title="Edit section"></td>'. "\n";
    print '	<td class="edit"><img src="css/images/deleteIP.png" class="sectionDelete" id="'. $section['id'] .'" title="Delete section"></td>'. "\n";
	print '</tr>'. "\n";;
}
?>

<!-- add new section -->
<tr class="th" >
    <td colspan=4 class="info">
        <img src="css/images/add.png" class="sectionAdd" title="Create new section"> Add new section
    </td>
</tr>


</table>	<!-- end table -->
</div>		<!-- end overlay div -->

<!-- result holder -->
<div class="manageSectionEdit"></div>