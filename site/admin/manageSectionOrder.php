<?php

/*
 * Section ordering
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/**
 * Fetch section info
 */
$sections = fetchSections();

$size =sizeof($sections);
?>



<!-- header -->
<div class="pHeader"><?php print _('Section order'); ?></div>


<!-- content -->
<div class="pContent">

	<!-- Order note -->
	<p class="muted"><?php print _('You can manually set order in which sections are displayed in. Default is creation date.'); ?></p>

	<!-- form -->
	<form id="sectionOrder" name="sectionEdit">

		<!-- edit table -->
		<table class="table table-condensed table-top">
		
		<!-- headers -->
		<tr>
			<th><?php print _("Order"); ?></th>
			<th><?php print _("Name"); ?></th>
			<th><?php print _("Description"); ?></th>
		</tr>
	
		<?php
		// print sections
		foreach($sections as $s) {
			print "<tr>";
			
			//order
			print "	<td>";
			print "	<select name='order-$s[id]' class='input-small'>";
			for($m=0; $m<=$size;$m++) {
				if($m==0) { $print = _("Not set"); }
				else	  { $print = $m; }
				if($m==$s['order'])	{ print "<option value='$m' selected='selected'>$print</option>"; }
				else				{ print "<option value='$m'>$print</option>"; }
			}
			print "	</select>";
			print "	</td>";
			
			print "	<td>$s[name]</td>";
			print "	<td>$s[description]</td>";
			
			print "</tr>";
		}
		?>

		
		</table>	<!-- end table -->
	</form>		<!-- end form -->
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-small btn-success" id="sectionOrderSubmit"><i class="icon-white icon-ok"></i> <?php print _('Save'); ?></button>
	</div>
	<!-- result holder -->
	<div class="sectionOrderResult"></div>
</div>	
		